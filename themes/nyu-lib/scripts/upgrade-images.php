#!/usr/bin/env php
<?php
/**
 * WordPress Image Upgrader
 *
 * Finds low-res WordPress images and downloads the original high-res versions
 *
 * Usage: php upgrade-images.php [--dry-run]
 */

$dryRun = in_array('--dry-run', $argv);
$wpBaseUrl = 'https://wp.nyu.edu/embedding_preservability/wp-content/uploads';
$newsPath = __DIR__ . '/../../../pages/03.news';

if ($dryRun) {
    echo "=== DRY RUN MODE - No files will be modified ===\n\n";
}

// Pattern to match WordPress resized images
// Matches: filename-300x200.jpg, filename-1024x768.png, etc.
$sizePattern = '/-(\d+x\d+)(\.[a-zA-Z]+)$/';
$scaledPattern = '/-scaled(\.[a-zA-Z]+)$/';

// Find all image files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($newsPath, RecursiveDirectoryIterator::SKIP_DOTS)
);

$upgrades = [];

foreach ($iterator as $file) {
    if (!$file->isFile()) continue;

    $filename = $file->getFilename();
    $filepath = $file->getPathname();
    $ext = strtolower($file->getExtension());

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) continue;

    // Check if this is a resized WordPress image
    if (preg_match($sizePattern, $filename, $matches)) {
        $size = $matches[1];
        $extension = $matches[2];
        $originalFilename = preg_replace($sizePattern, '$2', $filename);

        $upgrades[] = [
            'current_path' => $filepath,
            'current_filename' => $filename,
            'original_filename' => $originalFilename,
            'size' => $size,
            'directory' => dirname($filepath),
        ];
    } elseif (preg_match($scaledPattern, $filename, $matches)) {
        // -scaled images are usually already high-res, but we can try to get original
        $extension = $matches[1];
        $originalFilename = preg_replace($scaledPattern, '$1', $filename);

        $upgrades[] = [
            'current_path' => $filepath,
            'current_filename' => $filename,
            'original_filename' => $originalFilename,
            'size' => 'scaled',
            'directory' => dirname($filepath),
        ];
    }
}

echo "Found " . count($upgrades) . " images to potentially upgrade\n\n";

// Process each image
$upgraded = 0;
$failed = 0;
$skipped = 0;

foreach ($upgrades as $image) {
    echo "Processing: {$image['current_filename']}\n";
    echo "  Current size: {$image['size']}\n";
    echo "  Target filename: {$image['original_filename']}\n";

    // Try to construct the original URL
    // WordPress uploads are usually in /wp-content/uploads/YYYY/MM/filename.ext
    $originalUrl = findOriginalUrl($image['current_filename'], $wpBaseUrl);

    if (!$originalUrl) {
        echo "  [skip] Could not determine original URL\n\n";
        $skipped++;
        continue;
    }

    echo "  Original URL: $originalUrl\n";

    if ($dryRun) {
        echo "  [dry-run] Would download and replace\n\n";
        continue;
    }

    // Download the original
    $imageData = downloadImage($originalUrl);

    if ($imageData === false) {
        echo "  [failed] Could not download original\n\n";
        $failed++;
        continue;
    }

    // Save with the original filename (without size suffix)
    $newPath = $image['directory'] . '/' . $image['original_filename'];
    file_put_contents($newPath, $imageData);

    $oldSize = filesize($image['current_path']);
    $newSize = strlen($imageData);

    echo "  [success] Downloaded: " . formatBytes($oldSize) . " -> " . formatBytes($newSize) . "\n";

    // Update markdown files to reference the new filename
    updateMarkdownReferences($image['directory'], $image['current_filename'], $image['original_filename']);

    // Remove the old low-res file if different name
    if ($image['current_filename'] !== $image['original_filename']) {
        unlink($image['current_path']);
        echo "  [cleanup] Removed old file: {$image['current_filename']}\n";
    }

    echo "\n";
    $upgraded++;
}

echo "\n=== Summary ===\n";
echo "Upgraded: $upgraded\n";
echo "Failed: $failed\n";
echo "Skipped: $skipped\n";

// ============================================================================
// Helper functions
// ============================================================================

function findOriginalUrl($filename, $wpBaseUrl) {
    // Remove the size suffix to get original filename
    $originalFilename = preg_replace('/-\d+x\d+(\.[a-zA-Z]+)$/', '$1', $filename);
    $originalFilename = preg_replace('/-scaled(\.[a-zA-Z]+)$/', '$1', $originalFilename);

    // WordPress stores uploads in year/month folders
    // We'll need to try different paths or query the API

    // First, let's try to fetch from WordPress media API to find the exact URL
    $apiUrl = 'https://wp.nyu.edu/embedding_preservability/wp-json/wp/v2/media?per_page=100&search=' . urlencode(pathinfo($originalFilename, PATHINFO_FILENAME));

    $response = fetchUrl($apiUrl);
    if ($response) {
        $media = json_decode($response, true);
        if (!empty($media)) {
            foreach ($media as $item) {
                // Check if this media item matches our filename
                $sourceUrl = $item['source_url'] ?? '';
                if (stripos($sourceUrl, pathinfo($originalFilename, PATHINFO_FILENAME)) !== false) {
                    // Return the full size URL
                    if (isset($item['media_details']['sizes']['full']['source_url'])) {
                        return $item['media_details']['sizes']['full']['source_url'];
                    }
                    return $sourceUrl;
                }
            }
        }
    }

    // Fallback: try common year/month paths
    $years = range(2022, 2025);
    $months = range(1, 12);

    foreach ($years as $year) {
        foreach ($months as $month) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $testUrl = "$wpBaseUrl/$year/$monthStr/$originalFilename";

            if (urlExists($testUrl)) {
                return $testUrl;
            }
        }
    }

    return null;
}

function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200) ? $response : null;
}

function urlExists($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}

function downloadImage($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200) ? $data : false;
}

function updateMarkdownReferences($directory, $oldFilename, $newFilename) {
    if ($oldFilename === $newFilename) return;

    $mdFiles = glob($directory . '/*.md');
    foreach ($mdFiles as $mdFile) {
        $content = file_get_contents($mdFile);
        $newContent = str_replace($oldFilename, $newFilename, $content);
        if ($content !== $newContent) {
            file_put_contents($mdFile, $newContent);
            echo "  [updated] " . basename($mdFile) . "\n";
        }
    }
}

function formatBytes($bytes) {
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}
