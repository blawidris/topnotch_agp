<?php
namespace maza;

/**
 * Extend twig
 */
final class Twig {
    public static function extend($twig): void {
        $functions = array(
            'oc_config'     => [Registry::get('config'), 'get'],
            'oc_router'     => [Registry::get('load'), 'controller'],
            'mz_config'     => [Registry::get('mz_skin_config'), 'get'],
            'mz_hook'       => [Registry::get('mz_hook'), 'fetch'],
            'mz_shortcode'  => [Registry::get('mz_hook'), 'shortcode'],
        );
        
        if(version_compare(VERSION, '3.0.3.4') < 1) { // Version <= 3.0.3.4
            $twig->addExtension(new \Twig_Extension_StringLoader());
            
            foreach ($functions as $name => $callable) {
                $twig->addFunction(new \Twig_SimpleFunction($name, $callable));
            }
        } else {
            $twig->addExtension(new \Twig\Extension\StringLoaderExtension());

            foreach ($functions as $name => $callable) {
                $twig->addFunction(new \Twig\TwigFunction($name, $callable));
            }
        }
    }
}
