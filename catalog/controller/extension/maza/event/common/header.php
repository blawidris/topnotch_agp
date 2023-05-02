<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventCommonHeader extends Controller {
        
        public function index($route, &$data) {
                // Top header content
                $data['top_header'] = $this->mz_cache->getVar('top_header');
                
                // Main header content
                $data['main_header'] = $this->mz_cache->getVar('main_header');
                
                // Main navigation content
                $data['main_navigation'] = $this->mz_cache->getVar('main_navigation');

                // Header component
                $data['header_component'] = $this->mz_cache->getVar('header_component');
                
                // Page component
                $data['page_component'] = $this->mz_cache->getVar('page_component');
                
                // Preloader
                $data['preloader_status'] = $this->mz_skin_config->get('style_page_loader_status');
                
                $style_loader_spinner_image = maza\getOfLanguage($this->mz_skin_config->get('style_loader_spinner_image'));
                if($style_loader_spinner_image && is_file(DIR_IMAGE . $style_loader_spinner_image)){
                    $data['spinner_url'] = $this->config->get('mz_store_url') . 'image/' . $style_loader_spinner_image;
                } else {
                    $data['spinner_url'] = $this->config->get('mz_store_url') . 'image/no_image.png';
                }
                $data['spinner_type'] = pathinfo($data['spinner_url'], PATHINFO_EXTENSION);
                
                /** custom code between head tag */
                $data['code_in_head_tag'] = '';
                
                // Global level code
                if($this->config->get('mz_code_header_global_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/global.html')){
                        $data['code_in_head_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/global.html') . PHP_EOL;
                    }
                }
                
                // theme level code
                if($this->mz_theme_config->get('code_header_theme_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.html')){
                        $data['code_in_head_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.html') . PHP_EOL;
                    }
                }
                
                // skin level code
                if($this->mz_skin_config->get('code_header_skin_status')){
                    if(file_exists(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html')){
                        $data['code_in_head_tag'] .= file_get_contents(MZ_CONFIG::$DIR_CUSTOM_CODE . 'header/' . $this->mz_theme_config->get('theme_code') . '.skin.' . $this->mz_theme_config->get('skin_id') . '.html') . PHP_EOL;
                    }
                }
                
                $this->document->addStyle('catalog/view/theme/' . $this->mz_theme_config->get('theme_code') . '/asset/stylesheet/' . $this->mz_skin_config->get('skin_code') . '.' . $this->mz_skin_config->get('skin_id') . '/main.' . $this->session->data['language'] . '.css');
                $this->document->addStyle($this->mz_document->getRouteCSSFile());

                
                // Stylesheet file
                $data['styles'] = $this->mz_document->getStyles($this->config->get('maza_minify_css'), $this->config->get('maza_combine_css'));
                
                // links file
                $data['links'] = $this->document->getLinks();
                
                // javascript file
                if($this->config->get('maza_js_position') == 'header'){
                    $data['scripts'] = $this->mz_document->getScripts('all', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
                } elseif($this->config->get('maza_js_position') == 'default'){
                    $data['scripts'] = $this->mz_document->getScripts('header', $this->config->get('maza_minify_js'), $this->config->get('maza_combine_js'));
                } else {
                    $data['scripts'] = array();
                }
                
                // Remove or replace unwanted stylesheet
                unset($data['styles']['catalog/view/javascript/jquery/swiper/css/opencart.css']);
                
                $data['mz_cdn'] = $this->config->get('maza_cdn');

                // OGP
                $this->mz_document->addOGP('og:type', 'website');
                

                // Document
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_document d LEFT JOIN " . DB_PREFIX . "mz_document_description dd ON (d.document_id = dd.document_id) WHERE d.status = 1 AND d.route = '" . $this->db->escape($this->mz_document->getRoute()) . "' AND d.store_id = '" . (int)$this->config->get('config_store_id') . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                if ($query->num_rows > 0) {
                    $this->mz_document->addOGP('og:title', $query->row['og_title']);
                    $this->mz_document->addOGP('og:description', $query->row['og_description']);
                    $this->mz_document->addOGP('og:video', $query->row['og_video']);
                    $this->mz_document->addOGP('og:image:alt', $query->row['og_image_alt']);

                    if($query->row['og_image_width'] && $query->row['og_image_height']){
                        $this->mz_document->addOGP('og:image', $this->model_tool_image->resize($query->row['og_image'], $query->row['og_image_width'], $query->row['og_image_height']));
                        $this->mz_document->addOGP('og:image:width', $query->row['og_image_width']);
                        $this->mz_document->addOGP('og:image:height', $query->row['og_image_height']);
                    } else {
                        $this->mz_document->addOGP('og:image', $query->row['og_image']);
                    }
                    
                    if ($query->row['meta_title']) {
                        $this->document->setTitle($query->row['meta_title']);
                    }
                    if ($query->row['meta_description']) {
                        $this->document->setDescription($query->row['meta_description']);
                    }
                    if ($query->row['meta_keyword']) {
                        $this->document->setKeywords($query->row['meta_keyword']);
                    }
                }

                // Meta data
                $data['metadata'] = $this->mz_document->getMetadata();

                $data['page_class'] = str_replace('/', '-', $this->mz_document->getRoute());
        }
}
