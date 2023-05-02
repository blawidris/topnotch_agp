<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventAsset extends Controller {
    public function index(): void {
        if($this->mz_skin_config->get('flag_compile_route_asset')){
            ### CSS ###
            $css = $this->mz_document->getRouteCSSFile(true);
            
            // Compile custom CSS
            file_put_contents($css, $this->getCustomCSS());
            
            // Delete minified file
            $minified_css = substr($css, 0, -3) . 'min.css';
            if(file_exists($minified_css)){
                unlink($minified_css);
            }
            
            ### Javascript ###
            $js = $this->mz_document->getRouteJSFile(true);
            
            // Compile JS
            file_put_contents($js, $this->mz_document->getJSCode());
            
            // Delete minified file
            $minified_js = substr($js, 0, -2) . 'min.js';
            if(file_exists($minified_js)){
                unlink($minified_js);
            }
            
            ### SVG data file ###
            //file_put_contents(MZ_CONFIG::$DIR_CSS_CACHE . 'route/' . $file_name . '_' . $this->config->get('config_language_id') . '.svg', $this->mz_document->getSVGData());
        }
        
        // page cache
        if($this->config->get('maza_cache_page') && $this->mz_cache->canPageCache() && in_array($this->mz_document->getRoute(), $this->config->get('mz_cache_route'))){
            $this->mz_cache->set('page.' . $this->session->data['language'] . '.' . $this->session->data['currency'] . '.' . md5((isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] === 'on'?'https':'http') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI']), $this->response->getOutput());
        }
    }
    
    private function getCustomCSS(): string {
        $scss = '';
        $scss .= '@import "bootstrap4/functions";' . PHP_EOL; // Bootstrap SCSS
        $scss .= '@import "skins/bootstrap.config";' . PHP_EOL; // Bootstrap SCSS
        $scss .= '@import "bootstrap4/mixins";' . PHP_EOL; // Bootstrap SCSS

        // sass style variable file
        $scss .= $this->load->controller('extension/maza/startup/asset/getSassStyleVariable');

        $scss .=  $this->mz_document->getCSSCode();
        
        if($this->language->get('direction') === 'rtl'){
            $scss_compiler = $this->getScss(['rtl' => 'true']);
        } else {
            $scss_compiler = $this->getScss(['rtl' => 'false']);
        }
        
        return $scss_compiler->compile($scss);
    }
    
    /**
     * Get scss compiler
     */
    private function getScss(array $variables = array()): maza\Scss {
        $mz_scss = new maza\Scss();

        // Add import paths of sass file
        $import_paths = array();
        $import_paths[] = DIR_APPLICATION . 'view/javascript/maza/stylesheet/sass/';
        $import_paths[] = DIR_APPLICATION . 'view/theme/' . $this->mz_theme_config->get('theme_code') . '/';
        $mz_scss->setImportPaths($import_paths);
        $mz_scss->setVariables($variables);
        
        return $mz_scss;
    }
}
