<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Twig\TwigExtension;
use Twig\TwigFunction;
use RocketTheme\Toolbox\Event\Event;
use FilesystemIterator;
use Grav\Framework\Psr7\Response;

/**
 * Class FontawesomeProIconsPlugin
 * @package Grav\Plugin
 */
class FontawesomeProIconsPlugin extends Plugin
{
    /** @var array|null */
    protected static $iconManifest;

    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized'        => ['onPluginsInitialized', 0],
            'onTwigInitialized'           => ['onTwigInitialized', 0],
            'onShortcodeHandlers'         => ['onShortcodeHandlers', 0],
            'onAssetsInitialized'         => ['onAssetsInitialized', 0],
            'onAdminTwigTemplatePaths'    => ['onAdminTwigTemplatePaths', 0],
        ];
    }

    /**
     * Composer autoload
     *
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        $icon_paths[] = 'plugins://fontawesome-pro-icons/icons/';
        $this->grav['locator']->addPath('faicons', '', $icon_paths);

        if ($this->isAdmin()) {
            $this->enable([
                'onPageInitialized' => ['onPageInitialized', 0],
            ]);
        }
    }

    public function onShortcodeHandlers()
    {
        $this->grav['shortcode']->registerAllShortcodes(__DIR__ . '/classes/shortcodes');
    }

    public function onTwigInitialized()
    {
        $twig = $this->grav['twig'];

        $twig->twig()->addFunction(
            new TwigFunction('fa_icon', [$this, 'faIconFunction'])
        );

        $twig->twig()->addFunction(
            new TwigFunction('fa_icon_manifest', [$this, 'faIconManifestFunction'])
        );
    }

    public static function faIconFunction($path, $classes = null)
    {
        $path = Grav::instance()['locator']->findResource('faicons://' . $path, true);
        return TwigExtension::svgImageFunction($path, $classes);
    }

    public function faIconManifestFunction(): array
    {
        return $this->getIconManifest();
    }

    public function onAdminTwigTemplatePaths(Event $event): void
    {
        if (!$this->isAdmin()) {
            return;
        }

        $paths = $event['paths'] ?? [];
        $paths[] = __DIR__ . '/templates';
        $event['paths'] = $paths;
    }

    public function onAssetsInitialized(): void
    {
        if (!$this->isAdmin()) {
            return;
        }

        $assets = $this->grav['assets'];
        $assets->addCss('plugin://fontawesome-pro-icons/css/faicon-field.css');
        $assets->addJs('plugin://fontawesome-pro-icons/js/faicon-field.js', [
            'group' => 'bottom',
            'loading' => 'defer',
            'priority' => 120,
        ]);
    }

    public function onPageInitialized(): void
    {
        if (!$this->isAdmin()) {
            return;
        }

        $uri = $this->grav['uri'];
        $path = trim($uri->path(), '/');
        $adminRoute = trim($this->grav['config']->get('plugins.admin.route', '/admin'), '/');

        if ($path !== $adminRoute . '/faicon-icons') {
            return;
        }

        $mode = $uri->query('mode') ?? 'icons';
        $limit = (int)($uri->query('limit') ?? 50);
        $limit = max(1, min($limit, 200));
        $offset = max(0, (int)($uri->query('offset') ?? 0));
        $type = (string)($uri->query('type') ?? 'regular');
        $search = trim((string)($uri->query('q') ?? ''));

        if ($mode === 'types') {
            $payload = [
                'types' => $this->getIconTypesList(),
            ];
            $this->jsonResponse($payload);
            return;
        }

        $manifest = $this->getIconManifest();
        if (!isset($manifest[$type])) {
            $type = array_key_first($manifest) ?? $type;
        }

        $icons = $manifest[$type] ?? [];

        if ($search !== '') {
            $icons = array_values(array_filter($icons, static function ($icon) use ($search) {
                return stripos($icon, $search) !== false;
            }));
        }

        $total = count($icons);
        if ($offset >= $total) {
            $offset = max(0, $total - ($total % $limit));
        }

        $slice = array_slice($icons, $offset, $limit);
        $items = array_map(static function ($icon) use ($type) {
            return [
                'name' => $icon,
                'value' => $type . '/' . $icon . '.svg',
            ];
        }, $slice);

        $payload = [
            'type' => $type,
            'icons' => $items,
            'offset' => $offset,
            'limit' => $limit,
            'total' => $total,
        ];

        // Include type metadata on initial load for convenience.
        if ($offset === 0 && $search === '') {
            $payload['types'] = $this->getIconTypesList();
        }

        $this->jsonResponse($payload);
    }

    protected function getIconManifest(): array
    {
        if (self::$iconManifest !== null) {
            return self::$iconManifest;
        }

        $manifest = [];
        $locator = $this->grav['locator'];
        $basePath = $locator->findResource('plugins://fontawesome-pro-icons/icons', true, true);

        if ($basePath && is_dir($basePath)) {
            $types = new FilesystemIterator($basePath, FilesystemIterator::SKIP_DOTS);
            foreach ($types as $folder) {
                if (!$folder->isDir()) {
                    continue;
                }

                $type = $folder->getFilename();
                $icons = [];
                $files = new FilesystemIterator($folder->getPathname(), FilesystemIterator::SKIP_DOTS);
                foreach ($files as $file) {
                    if ($file->isFile() && strtolower($file->getExtension()) === 'svg') {
                        $icons[] = $file->getBasename('.svg');
                    }
                }

                sort($icons, SORT_NATURAL | SORT_FLAG_CASE);
                $manifest[$type] = $icons;
            }

            ksort($manifest, SORT_NATURAL | SORT_FLAG_CASE);
        }

        self::$iconManifest = $manifest;

        return $manifest;
    }

    protected function getIconTypesList(): array
    {
        $manifest = $this->getIconManifest();
        $types = [];
        foreach ($manifest as $type => $icons) {
            $types[] = [
                'value' => $type,
                'count' => count($icons),
            ];
        }

        return $types;
    }

    protected function jsonResponse(array $payload, int $status = 200): void
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = json_encode(['error' => 'Unable to encode response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $status = 500;
        }

        $response = new Response($status, ['Content-Type' => 'application/json'], $json);
        $this->grav->close($response);
    }
}
