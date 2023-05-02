<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventCommonFooter extends Controller {
        
    public function index(string $route, array &$data): void {
        // Top header content
        $this->mz_cache->setVar('top_header', $this->load->controller('extension/maza/layout_builder', ['group' => 'top_header', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));
        
        // Main header content
        $this->mz_cache->setVar('main_header', $this->load->controller('extension/maza/layout_builder', ['group' => 'main_header', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));
        
        // Main navigation content
        $this->mz_cache->setVar('main_navigation', $this->load->controller('extension/maza/layout_builder', ['group' => 'main_navigation', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));

        // Header components
        $this->mz_cache->setVar('header_component', $this->load->controller('extension/maza/layout_builder', ['group' => 'header_component', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));

        // Page components for default layout buider
        // Popup | Sticky | Drawer
        if($this->config->get('mz_layout_type') === 'default' && !$this->mz_cache->isVar('page_component')){
			$this->mz_cache->setVar('page_component', $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]));
        }
        
        // custom code before body tag
        $data['code_before_body_tag'] = '';
        
        // Global level code
        if($this->config->get('mz_code_footer_global_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/global.html')){
                $data['code_before_body_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/global.html') . PHP_EOL;
            }
        }
        
        // theme level code
        if($this->mz_theme_config->get('code_footer_theme_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.html')){
                $data['code_before_body_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.html') . PHP_EOL;
            }
        }
        
        // skin level code
        if($this->mz_skin_config->get('code_footer_skin_status')){
            if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html')){
                $data['code_before_body_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'footer/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html') . PHP_EOL;
            }
        }
        
        /** Footer layout content */
        $data['footer_content'] = $this->load->controller('extension/maza/layout_builder', ['group' => 'footer', 'group_owner' => $this->mz_skin_config->get('skin_footer_id')]);
        
        // javascript
        if($this->config->get('maza_js_position') == 'footer'){
            $data['scripts'] = $this->mz_document->getScripts('all', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
        } elseif($this->config->get('maza_js_position') == 'default'){
            $data['scripts'] = $this->mz_document->getScripts('footer', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
        } else {
            $data['scripts'] = array();
        }
        
        // SVG data
        $data['svg_data'] = $this->mz_document->getSVGData();

        // Schemas
        $data['schemas'] = $this->mz_document->getSchema();
        
        // Language
        $data['language'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);
        
        // Currency
        $data['currency'] = $this->url->link('common/currency/currency', '', $this->request->server['HTTPS']);
        
        // Google map API
        $data['google_map_api_key'] = $this->config->get('maza_api_google_map_key');
        $data['language_code']      = $this->session->data['language'];

        // Back to Top
        $data['back_to_top'] = $this->mz_skin_config->get('style_back_to_top_status');
    }
}
