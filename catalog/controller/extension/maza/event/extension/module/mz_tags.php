<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventExtensionModuleMzTags extends Controller {
        
        /**
         * Run code before product search view
         * @param String $route route
         * @param Array $param parameter of method
         * @return Void
         */
        public function productSearchView($route, $data) {
            if($this->config->get('maza_status')){
                $this->load->model('extension/module/mz_tags');
                
                if (isset($this->request->get['tag'])) {
                    $this->model_extension_module_mz_tags->updateProductTagViewed($this->request->get['tag']);
                }
            }
        }
        
        /**
         * Run code before product search view
         * @param String $route route
         * @param Array $param parameter of method
         * @return Void
         */
        public function blogSearchView($route, $data) {
            if($this->config->get('maza_status')){
                $this->load->model('extension/module/mz_tags');
                
                if (isset($this->request->get['tag'])) {
                    $this->model_extension_module_mz_tags->updateBlogTagViewed($this->request->get['tag']);
                }
            }
        }
        
}
