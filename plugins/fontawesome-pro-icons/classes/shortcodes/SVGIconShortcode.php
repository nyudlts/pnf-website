<?php

namespace Grav\Plugin\Shortcodes;

use Grav\Plugin\FontawesomeProIconsPlugin;
use Grav\Plugin\SVGIconsPlugin;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class FAIconShortcode extends Shortcode
{
    public function init()
    {
        $this->shortcode->getHandlers()->add('fa-icon', function(ShortcodeInterface $sc) {

            // Get shortcode content and parameters
            $icon = $sc->getParameter('icon', $sc->getParameter('fa-icon', $this->getBbCode($sc)));
            $set = $sc->getParameter('set', 'regular');
            $classes = $sc->getParameter('class', 'svg-icon inline-block align-middle');
            $path = $set . "/" . $icon . ".svg";

            $svg = FontawesomeProIconsPlugin::faIconFunction($path, $classes);

            return $svg ?: '';

        });
    }
}