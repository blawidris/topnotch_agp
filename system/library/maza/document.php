<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright           Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;
/**
* Document class
* For CSS and Javascript asset
*/
class Document extends Library {
    private $css_code = '';
    private $js_code = '';
    private $svg = array();
    private $schemas = array();
    private $metadata = array(); // metadata
    private $timestamp;
    
    public function __construct() {
        $this->timestamp = time();
    }


    /**
     * add css custom code
     * @param String $code css or scss code
     * @return void
     */
    public function addCSSCode(string $code): void {
        $this->css_code .= $code;
    }
        
    /**
     * add javascript custom code
     * @param String $code javascript
     * @return void
     */
    public function addJSCode(string $code): void {
        $this->js_code .= $code;
    }
        
    /**
     * Get custom CSS code
     * @return String CSS code
     */
    public function getCSSCode(): string {
        return $this->css_code;
    }
        
    /**
     * Get custom javascript code
     * @return String javascript code
     */
    public function getJSCode(): string {
        return $this->js_code;
    }
        
    /**
     * Get styles files links
     * @param Boolean $minify minify css
     * @return Array
     */
    public function getStyles(bool $minify = false, bool $combine = false): array {
        $styles = $this->document->getStyles();
        
        if($minify){
            foreach($styles as $href => $style){
                if(is_file(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $style['href']) && substr($style['href'], -8) !== '.min.css'){

                    $css_path_without_extension = substr($style['href'], 0, -4);
                    
                    if(!is_file(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $css_path_without_extension . '.min.css')){
                        $this->mz_minifier->css(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $style['href'], dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $css_path_without_extension . '.min.css');
                    }

                    $styles[$href]['href'] = $css_path_without_extension . '.min.css';
                }
            }
        }
            
        if($combine){
            $files = array();
        
            foreach($styles as $href => $style){
                if(is_file(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $style['href'])){
                    $files[] = $style['href'];
                    unset($styles[$href]);
                }
            }
            $a = $files;
            sort($a);
            $filename = md5(implode('', $a)) . '.css';

            $file = \MZ_CONFIG::$DIR_CSS_COMBINE . $filename;

            if(!is_file($file)){
                foreach($files as $style){
                    file_put_contents($file, file_get_contents(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $style) . PHP_EOL, FILE_APPEND);
                }
            }
            
            $combine_style = basename(DIR_APPLICATION) . DIRECTORY_SEPARATOR . substr(\MZ_CONFIG::$DIR_CSS_COMBINE, strlen(DIR_APPLICATION)) . $filename;
            
            $styles[$combine_style]['href']     = $combine_style;
            $styles[$combine_style]['rel']      = 'stylesheet';
            $styles[$combine_style]['media']    = 'all';
        }
                
		return $styles;
	}
        
    /**
     * Get javascript files links
     * @param String $position load position
     * @param Boolean $minify minify javascript
     * @param Boolean $combine combine scripts to single script
     * @return Array
     */
    public function getScripts(string $position = 'header', bool $minify = false, bool $combine = false): array {
        if($position == 'all'){
            $scripts = array_merge($this->document->getScripts('header'), $this->document->getScripts('footer'));
        } else {
            $scripts = $this->document->getScripts($position);
        }
        
        if($minify){
            foreach($scripts as $href => $script){
                if(is_file(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $script) && substr($script, -7) !== '.min.js'){
                    
                    $js_path_without_extension = substr($script, 0, -3);
                    
                    if(!is_file(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $js_path_without_extension . '.min.js')){
                        $this->mz_minifier->js(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $script, dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $js_path_without_extension . '.min.js');
                    }
                    
                    $scripts[$href] = $js_path_without_extension . '.min.js';
                }
            }
        }
            
        if($combine){
            $files = array();
        
            foreach($scripts as $href => $script){
                if(is_file(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $script)){
                    $files[] = $script;
                    unset($scripts[$href]);
                }
            }
            $a = $files;
            sort($a);
            $filename = md5(implode('', $a)) . '.js';

            $file = \MZ_CONFIG::$DIR_JS_COMBINE . $filename;

            if(!is_file($file)){
                foreach($files as $script){
                    file_put_contents($file, file_get_contents(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $script) . ';' . PHP_EOL, FILE_APPEND);
                }
            }
            
            $combine_script = basename(DIR_APPLICATION) . DIRECTORY_SEPARATOR . substr(\MZ_CONFIG::$DIR_JS_COMBINE, strlen(DIR_APPLICATION)) . $filename;
            
            $scripts[$combine_script] = $combine_script;
        }
                
		return $scripts;
	}

    /**
     * Add rich snippets schemas
     */
    public function addSchema(array $data): void {
        $this->schemas[] = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function getSchema(): array {
        return $this->schemas;
    }

    /**
     * Add html metadatas
     * <meta name="title" content="" />
     * @param Array $data = array('name' => 'title', 'content' => '')
     */
    public function addMetadata(array $data, string $key = ''): void {
        if ($key) {
            $this->metadata[$key] = $data;
        } else {
            $this->metadata[] = $data;
        }
    }

    public function getMetadata(): array {
        $metadata = array();

        foreach($this->metadata as $attributes){
            $metadata[] = implode(' ', array_map(function($key, $val){
                return $key . '="' . $val . '"';
            }, array_keys($attributes), array_values($attributes)));
        }

        return $metadata;
    }

    /**
     * Add Open Graph protocol
     */
    public function addOGP(string $property, string $content): void {
        if ($content) {
            $this->addMetadata(['property' => $property, 'content' => $content], $property);
        }
    }
        
    /**
     * Add SVG file
     */
    public function addSVG(string $file_path): string {
        $gID = 'svg' . md5($file_path);
        
        $this->svg[$gID] = $file_path;
        
        //$svg_filename = 'catalog/view/theme/' . $this->mz_theme_config->get('theme_code') . '/asset/stylesheet/' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '/route/' . str_replace('/', '_', $this->getRoute()) . '_' . $this->config->get('mz_layout_id') . '_' . $this->config->get('config_language_id') . '.svg';
        
        return '<svg><use xlink:href="#' . $gID . '"></use></svg>';
    }
    
    /**
     * Get SVG files
     */
    public function getSVG(): array {
        return $this->svg;
    }
    
    /**
     * Get SVG data
     */
    public function getSVGData(): string {
        $data = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">';
        
        foreach($this->svg as $gID => $file){
            if(is_file($file)){
                $data .= '<g id="' . $gID . '">';
                $data .= $this->gSVG($file);
                $data .= '</g>';
            }
        }
        
        $data .= '</svg>';
        
        return $data;
    }
    
    private function gSVG(string $file): string {
        $data = file_get_contents($file);
        
        // Remove XML tag
        $data = preg_replace('/<\?xml[^>]+>/', '', $data);
        
        // Remove DOCTYPE
        $data = preg_replace('/<\!DOCTYPE[^>]+>/', '', $data);
                
        return trim($data);
    }
    
    /**
     * Get current route
     * @return String route
     */
    public function getRoute(): string {
        if(isset($this->request->get['route'])){
            return (string)$this->request->get['route'];
        } else {
            return $this->config->get('action_default')??'common/home';
        }
    }

    /**
     * Check is current route
     */
    public function isRoute(string $route): bool {
        return $this->getRoute() === $route;
    }
    
    /**
     * Get route specific asset file location
     * @return String filename
     */
    public function getAssetFile(): string {
        if($this->getRoute() == 'extension/maza/page'){
            return str_replace('/', '_', $this->getRoute()) . '_' . $this->request->get['page_id'];
        } else {
            return str_replace('/', '_', $this->getRoute()) . '_' . $this->config->get('mz_layout_id');
        }
    }
    
    /**
     * Get CSS route file location
     * If exist then give current or create new location with timestamp
     */
    public function getRouteCSSFile(bool $full = false): string {
        $directory = \MZ_CONFIG::$DIR_CSS_CACHE . 'route/';
        $filename = $this->getAssetFile();
        $ext = $this->session->data['language'] . '.css';
        
        $file = $directory . $filename . '.' . $this->timestamp  . '.' . $ext;
        
        foreach(glob($directory . $filename . '.*.' . $ext) as $exfile){
            $file = $exfile;
            break;
        }
        
        
        if($full){
            return $file;
        } else {
            return basename(DIR_APPLICATION) . DIRECTORY_SEPARATOR . substr($file, strlen(DIR_APPLICATION));
        }
    }
    
    /**
     * Get JS route file location
     * If exist then give current or create new location with timestamp
     */
    public function getRouteJSFile(bool $full = false): string {
        $directory = \MZ_CONFIG::$DIR_JS_CACHE . 'route/';
        $filename = $this->getAssetFile();
        $ext = 'js';
        
        $file = $directory . $filename . '.' . $this->timestamp  . '.' . $ext;
        
        foreach(glob($directory . $filename . '.*.' . $ext) as $exfile){
            $file = $exfile;
            break;
        }
        
        
        if($full){
            return $file;
        } else {
            return basename(DIR_APPLICATION) . DIRECTORY_SEPARATOR . substr($file, strlen(DIR_APPLICATION));
        }
    }


    /**
     * Clear cache assets
     */
    public function clear(): void {
        foreach(glob(\MZ_CONFIG::$DIR_CSS_CACHE . '*.{scss,css}', GLOB_BRACE) as $file){
            unlink($file);
        }
        foreach(glob(\MZ_CONFIG::$DIR_CSS_CACHE . 'route/*.css') as $file){
            unlink($file);
        }
        foreach(glob(\MZ_CONFIG::$DIR_CSS_COMBINE . '*.css') as $file){
            unlink($file);
        }
        
        // Delete main js file
        foreach(glob(\MZ_CONFIG::$DIR_JS_CACHE . '*.js') as $file){
            unlink($file);
        }
        foreach(glob(\MZ_CONFIG::$DIR_JS_CACHE . 'route/*.js') as $file){
            unlink($file);
        }
        foreach(glob(\MZ_CONFIG::$DIR_JS_COMBINE . '*.js') as $file){
            unlink($file);
        }
    }
}