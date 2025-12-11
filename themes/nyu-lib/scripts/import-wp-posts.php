#!/usr/bin/env php
<?php
/**
 * WordPress to Grav Blog Importer
 *
 * Fetches posts from WordPress REST API, downloads images, and creates Grav pages
 *
 * Usage: php import-wp-posts.php
 */

// Configuration
$config = [
    'wp_api_url' => 'https://wp.nyu.edu/embedding_preservability/wp-json/wp/v2',
    'wp_base_url' => 'https://wp.nyu.edu/embedding_preservability',
    'categories_to_import' => ['news', 'updates'],
    'grav_pages_path' => __DIR__ . '/../../../pages/03.news',
    'page_template' => 'item',
];

// Ensure output directory exists
if (!is_dir($config['grav_pages_path'])) {
    mkdir($config['grav_pages_path'], 0755, true);
    echo "Created directory: {$config['grav_pages_path']}\n";
}

// Helper function for fetching URLs with proper headers
function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo "  [warning] HTTP $httpCode for: $url\n";
        return null;
    }
    return $response;
}

// Fetch categories
echo "Fetching categories...\n";
$categoriesJson = fetchUrl($config['wp_api_url'] . '/categories?per_page=100');
$categories = json_decode($categoriesJson, true) ?? [];

$categoryMap = [];
$categoryIdsToImport = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['id']] = [
        'slug' => $cat['slug'],
        'name' => $cat['name'],
    ];
    if (in_array($cat['slug'], $config['categories_to_import'])) {
        $categoryIdsToImport[] = $cat['id'];
        echo "  Found category: {$cat['name']} (ID: {$cat['id']})\n";
    }
}

// Fetch tags
echo "Fetching tags...\n";
$tagsJson = fetchUrl($config['wp_api_url'] . '/tags?per_page=100');
$tags = json_decode($tagsJson, true) ?? [];
$tagMap = [];
foreach ($tags as $tag) {
    $tagMap[$tag['id']] = [
        'slug' => $tag['slug'],
        'name' => $tag['name'],
    ];
}

// Fetch users/authors
echo "Fetching authors...\n";
$usersJson = fetchUrl($config['wp_api_url'] . '/users?per_page=100');
$users = json_decode($usersJson, true) ?? [];
$userMap = [];
foreach ($users as $user) {
    $userMap[$user['id']] = [
        'name' => $user['name'],
        'slug' => $user['slug'],
    ];
}

// Fetch media to get featured images
echo "Fetching media library...\n";
$mediaJson = fetchUrl($config['wp_api_url'] . '/media?per_page=100');
$media = json_decode($mediaJson, true) ?? [];
$mediaMap = [];
foreach ($media as $item) {
    $mediaMap[$item['id']] = [
        'url' => $item['source_url'] ?? '',
        'alt' => $item['alt_text'] ?? '',
        'title' => $item['title']['rendered'] ?? '',
        'caption' => $item['caption']['rendered'] ?? '',
    ];
}

if (empty($categoryIdsToImport)) {
    die("Error: No matching categories found\n");
}

// Fetch all posts
echo "\nFetching posts...\n";
$postsJson = fetchUrl($config['wp_api_url'] . '/posts?per_page=100&_embed');
$posts = json_decode($postsJson, true) ?? [];

if (empty($posts)) {
    die("Error: No posts found\n");
}

echo "Found " . count($posts) . " total posts\n";

// Process posts
$importedCount = 0;
foreach ($posts as $post) {
    // Check if post belongs to target categories
    $postCategories = array_intersect($post['categories'], $categoryIdsToImport);
    if (empty($postCategories)) {
        continue;
    }

    $title = html_entity_decode($post['title']['rendered'], ENT_QUOTES, 'UTF-8');
    $slug = $post['slug'];
    $date = date('Y-m-d H:i', strtotime($post['date']));
    $dateOnly = date('Y-m-d', strtotime($post['date']));
    $modified = date('Y-m-d H:i', strtotime($post['modified']));
    $content = $post['content']['rendered'];
    $excerpt = strip_tags($post['excerpt']['rendered']);

    // Get author
    $author = isset($userMap[$post['author']]) ? $userMap[$post['author']]['name'] : '';

    // Get categories as tags
    $categoryTags = [];
    foreach ($post['categories'] as $catId) {
        if (isset($categoryMap[$catId])) {
            $categoryTags[] = $categoryMap[$catId]['slug'];
        }
    }

    // Get actual tags
    $postTags = [];
    if (!empty($post['tags'])) {
        foreach ($post['tags'] as $tagId) {
            if (isset($tagMap[$tagId])) {
                $postTags[] = $tagMap[$tagId]['slug'];
            }
        }
    }

    // Create folder
    $folderName = $dateOnly . '-' . $slug;
    $folderPath = $config['grav_pages_path'] . '/' . $folderName;

    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    // Download and update images in content
    $content = processImages($content, $folderPath, $config['wp_base_url']);

    // Get featured image
    $featuredImage = '';
    if (!empty($post['featured_media']) && isset($mediaMap[$post['featured_media']])) {
        $featuredUrl = $mediaMap[$post['featured_media']]['url'];
        if ($featuredUrl) {
            $featuredImage = downloadImage($featuredUrl, $folderPath);
        }
    }

    // Convert HTML to Markdown
    $markdownContent = convertHtmlToMarkdown($content);

    // Build frontmatter
    $frontmatter = [
        'title' => $title,
        'date' => $date,
        'modified' => $modified,
        'author' => $author,
        'taxonomy' => [
            'category' => $categoryTags,
            'tag' => $postTags,
        ],
    ];

    if ($featuredImage) {
        $frontmatter['hero_image'] = $featuredImage;
    }

    if (trim($excerpt)) {
        $frontmatter['summary'] = trim($excerpt);
    }

    // Create the markdown file
    $markdown = "---\n";
    $markdown .= yamlEncode($frontmatter);
    $markdown .= "---\n\n";
    $markdown .= $markdownContent;

    $filePath = $folderPath . '/' . $config['page_template'] . '.en.md';
    file_put_contents($filePath, $markdown);

    echo "  Created: $folderName\n";
    $importedCount++;
}

// Create the blog listing page
$blogPageContent = <<<YAML
---
title: 'News & Updates'
admin:
    children_display_order: default
blog_url: /news
content:
    items:
        - '@self.children'
    limit: 72
    order:
        by: date
        dir: desc
    pagination: false
    url_taxonomy_filters: false
---

YAML;

file_put_contents($config['grav_pages_path'] . '/blog.en.md', $blogPageContent);

echo "\nImport complete! Imported $importedCount posts.\n";
echo "Blog listing page created at: {$config['grav_pages_path']}/blog.en.md\n";

// ============================================================================
// Helper functions
// ============================================================================

function processImages($html, $folderPath, $wpBaseUrl) {
    // Find all images in content
    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $originalTag = $match[0];
        $imageUrl = $match[1];

        // Get alt text if available
        $alt = '';
        if (preg_match('/alt=["\']([^"\']*)["\']/', $originalTag, $altMatch)) {
            $alt = $altMatch[1];
        }

        // Download image
        $localFilename = downloadImage($imageUrl, $folderPath);

        if ($localFilename) {
            // Replace with markdown image
            $markdownImage = "![$alt]($localFilename)";
            $html = str_replace($originalTag, $markdownImage, $html);
        }
    }

    return $html;
}

function downloadImage($url, $folderPath) {
    // Clean up URL
    $url = html_entity_decode($url);

    // Get filename from URL
    $parsedUrl = parse_url($url);
    $filename = basename($parsedUrl['path']);

    // Remove query strings and decode
    $filename = preg_replace('/\?.*$/', '', $filename);
    $filename = urldecode($filename);

    // Sanitize filename
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '-', $filename);

    $localPath = $folderPath . '/' . $filename;

    // Skip if already downloaded
    if (file_exists($localPath)) {
        echo "    [skip] $filename (exists)\n";
        return $filename;
    }

    // Download using cURL
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || $imageData === false) {
        echo "    [error] Failed to download: $url\n";
        return '';
    }

    file_put_contents($localPath, $imageData);
    echo "    [download] $filename\n";

    return $filename;
}

function convertHtmlToMarkdown($html) {
    // Remove WordPress block comments
    $html = preg_replace('/<!--.*?-->/s', '', $html);

    // Remove WordPress-specific classes and attributes
    $html = preg_replace('/\s+class="[^"]*"/i', '', $html);
    $html = preg_replace('/\s+id="[^"]*"/i', '', $html);
    $html = preg_replace('/\s+style="[^"]*"/i', '', $html);
    $html = preg_replace('/\s+data-[a-z-]+="[^"]*"/i', '', $html);

    // Headers - add newlines before and after
    $html = preg_replace('/<h1[^>]*>(.*?)<\/h1>/is', "\n\n# $1\n\n", $html);
    $html = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n\n## $1\n\n", $html);
    $html = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n\n### $1\n\n", $html);
    $html = preg_replace('/<h4[^>]*>(.*?)<\/h4>/is', "\n\n#### $1\n\n", $html);
    $html = preg_replace('/<h5[^>]*>(.*?)<\/h5>/is', "\n\n##### $1\n\n", $html);
    $html = preg_replace('/<h6[^>]*>(.*?)<\/h6>/is', "\n\n###### $1\n\n", $html);

    // Bold and italic
    $html = preg_replace('/<strong[^>]*>(.*?)<\/strong>/is', '**$1**', $html);
    $html = preg_replace('/<b[^>]*>(.*?)<\/b>/is', '**$1**', $html);
    $html = preg_replace('/<em[^>]*>(.*?)<\/em>/is', '*$1*', $html);
    $html = preg_replace('/<i[^>]*>(.*?)<\/i>/is', '*$1*', $html);

    // Links (but not already-converted markdown images)
    $html = preg_replace('/<a[^>]*href="([^"]*)"[^>]*>(!\[[^\]]*\]\([^)]*\))<\/a>/is', '$2', $html);
    $html = preg_replace('/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/is', '[$2]($1)', $html);

    // Handle figures with captions
    $html = preg_replace_callback('/<figure[^>]*>(.*?)<\/figure>/is', function($matches) {
        $inner = $matches[1];
        preg_match('/!\[[^\]]*\]\([^)]*\)/', $inner, $imgMatch);
        $img = $imgMatch[0] ?? '';

        preg_match('/<figcaption[^>]*>(.*?)<\/figcaption>/is', $inner, $capMatch);
        $caption = isset($capMatch[1]) ? strip_tags($capMatch[1]) : '';

        if ($img && $caption) {
            return "\n\n$img\n*$caption*\n\n";
        } elseif ($img) {
            return "\n\n$img\n\n";
        }
        return '';
    }, $html);

    // Lists
    $html = preg_replace('/<ul[^>]*>/i', "\n\n", $html);
    $html = preg_replace('/<\/ul>/i', "\n\n", $html);
    $html = preg_replace('/<ol[^>]*>/i', "\n\n", $html);
    $html = preg_replace('/<\/ol>/i', "\n\n", $html);
    $html = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "- $1\n", $html);

    // Blockquotes
    $html = preg_replace_callback('/<blockquote[^>]*>(.*?)<\/blockquote>/is', function($matches) {
        $content = strip_tags($matches[1]);
        $lines = explode("\n", trim($content));
        $quoted = array_map(function($line) {
            return '> ' . trim($line);
        }, $lines);
        return "\n\n" . implode("\n", $quoted) . "\n\n";
    }, $html);

    // Code blocks
    $html = preg_replace('/<pre[^>]*><code[^>]*>(.*?)<\/code><\/pre>/is', "\n\n```\n$1\n```\n\n", $html);
    $html = preg_replace('/<code[^>]*>(.*?)<\/code>/is', '`$1`', $html);

    // Horizontal rules
    $html = preg_replace('/<hr[^>]*\/?>/i', "\n\n---\n\n", $html);

    // Paragraphs - critical: add double newlines
    $html = preg_replace('/<p[^>]*>(.*?)<\/p>/is', "\n\n$1\n\n", $html);
    $html = preg_replace('/<br\s*\/?>/i', "  \n", $html);

    // Divs with newlines
    $html = preg_replace('/<div[^>]*>(.*?)<\/div>/is', "\n\n$1\n\n", $html);
    $html = preg_replace('/<span[^>]*>(.*?)<\/span>/is', '$1', $html);
    $html = preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/is', '', $html);

    // Clean any remaining HTML
    $html = strip_tags($html);

    // Decode HTML entities
    $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

    // Clean up whitespace - preserve newlines!
    $html = preg_replace('/[^\S\n]+/', ' ', $html); // Multiple spaces (not newlines) to single
    $html = preg_replace('/\n{3,}/', "\n\n", $html); // 3+ newlines to double
    $html = preg_replace('/^\s+$/m', '', $html); // Remove whitespace-only lines
    $html = preg_replace('/^ +| +$/m', '', $html); // Trim spaces from line start/end

    return trim($html);
}

function yamlEncode($data, $indent = 0) {
    $yaml = '';
    $prefix = str_repeat('    ', $indent);

    foreach ($data as $key => $value) {
        if (is_null($value) || (is_array($value) && empty($value))) {
            continue;
        }

        if (is_array($value)) {
            // Check if sequential array
            if (array_keys($value) === range(0, count($value) - 1)) {
                if (empty($value)) continue;
                $yaml .= "$prefix$key:\n";
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $yaml .= yamlEncode($item, $indent + 1);
                    } else {
                        $yaml .= "$prefix    - " . escapeYamlValue($item) . "\n";
                    }
                }
            } else {
                // Associative array
                $yaml .= "$prefix$key:\n";
                $yaml .= yamlEncode($value, $indent + 1);
            }
        } else {
            $yaml .= "$prefix$key: " . escapeYamlValue($value) . "\n";
        }
    }

    return $yaml;
}

function escapeYamlValue($value) {
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_numeric($value) && !preg_match('/^0\d/', $value)) {
        return $value;
    }
    if ($value === '') {
        return "''";
    }
    // Quote strings with special chars or that look like dates/numbers
    if (preg_match('/[:\#\[\]\{\}\,\&\*\!\|\>\'\"\%\@\`\n]/', $value) ||
        preg_match('/^\s|\s$/', $value) ||
        preg_match('/^\d{4}-\d{2}-\d{2}/', $value) ||
        preg_match('/^[\d.]+$/', $value)) {
        // Use single quotes, escape internal single quotes
        return "'" . str_replace("'", "''", $value) . "'";
    }
    return $value;
}
