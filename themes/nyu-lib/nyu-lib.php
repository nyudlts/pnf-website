<?php

namespace Grav\Theme;

use Grav\Common\Theme;
use RocketTheme\Toolbox\Event\Event;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Twig\TwigFilter;

class NYULib extends Theme
{
    protected $svgs = [];

    public static function getSubscribedEvents()
    {
        return [
            'onThemeInitialized' => [
                ['onThemeInitialized', 0]
            ],
            'onPageInitialized' => ['onPageInitialized', 0],
            'onTwigInitialized' => ['onTwigInitialized', 0],
            'onShortcodeHandlers' => ['onShortcodeHandlers', 0],
        ];
    }

    public function onShortcodeHandlers(Event $e): void
    {
        $this->grav['shortcode']->getHandlers()->add('example', static function(ShortcodeInterface $sc) {
            return '<div class="example">' . $sc->getContent() . '</div>';
        });
    }

    public function onThemeInitialized()
    {
        if ($this->isAdmin()) {
            return;
        }
    }

    public function onTwigInitialized()
    {
        $twig = $this->grav['twig'];

        $twig->twig()->addFilter(
            new TwigFilter('convert_svgs', [$this, 'convertSVGs'])
        );

        $form_class_variables = [
            'form_outer_classes' => '',
            'form_button_outer_classes' => 'text-center',
            'form_button_classes' => 'form-button',
            'form_errors_classes' => '',
            'form_field_outer_classes' => '',
            'form_field_outer_label_classes' => '',
            'form_field_label_classes' => '',
            'form_field_outer_data_classes' => '',
            'form_field_input_classes' => 'form-input',
            'form_field_textarea_classes' => 'form-textarea',
            'form_field_select_classes' => 'form-select',
            'form_field_radio_classes' => 'form-radio',
            'form_field_checkbox_classes' => 'form-checkbox',
        ];

        $twig->twig_vars = array_merge($twig->twig_vars, $form_class_variables);
    }

    public function onPageInitialized(): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $request = $this->grav['request'];
        if (!method_exists($request, 'getHeaderLine')) {
            return;
        }

        $hxHeader = $request->getHeaderLine('HX-Request');

        if (!$hxHeader || strtolower($hxHeader) !== 'true') {
            return;
        }

        $page = $this->grav['page'];
        if (!$page) {
            return;
        }

        $this->grav['config']->set('system.debugger.enabled', false);
        if (isset($this->grav['debugger'])) {
            $this->grav['debugger']->enabled(false);
        }

        $htmxTemplate = $page->template() . '-htmx';
        $loader = $this->grav['twig']->twig()->getLoader();

        if (method_exists($loader, 'exists') && $loader->exists($htmxTemplate . '.html.twig')) {
            $page->template($htmxTemplate);
            $this->grav['twig']->twig_vars['is_htmx_request'] = true;
        }
    }

    public function convertSVGs($content)
    {
        // Quick check: if content doesn't contain '[', no platforms to replace
        if (strpos($content, '[') === false) {
            return $content;
        }

        // Only process if we find at least one platform marker
        $replacements = [];
        foreach (array_keys($this->svgs) as $platform) {
            if (str_contains($content, "[{$platform}]")) {
                $replacements["[{$platform}]"] = $this->svgs[$platform];
            }
        }

        // If we found any replacements, do them all at once
        if (!empty($replacements)) {
            $content = strtr($content, $replacements);
        }

        return $content;
    }
}
