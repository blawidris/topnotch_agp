<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaCron extends Controller {
        public function __construct($registry) {
            parent::__construct($registry);
            $this->load->library('cart/user');
            
            if(!$this->config->get('maza_cron_status')){
                die('<h1>503 Service Unavailable</h1>');
            }
            
            $this->load->library('cart/user');
            
            $this->load->model('extension/maza/cron');
            
            if(!$this->model_extension_maza_cron->login()){
                die('<h1>401 Unauthorized</h1>');
            }
            
            ignore_user_abort(true);
            set_time_limit(1800);
        }
        
        public function index() {
            $this->load->model('extension/maza/extension');
            
            // Flush expire cache
            $this->mz_cache->flush();
            
            // Generate product and blog tag
            if($this->model_extension_maza_extension->hasInstalled('module', 'mz_tags')){
                $this->load->controller('extension/module/mz_tags/generateTags');
            }
            
            // Sync filter
            if($this->model_extension_maza_extension->hasInstalled('module', 'mz_filter')){
                $this->filter();
            }
            
            $this->removeJunkFile();
            
            die('<h1>Cron completed</h1>');
        }
        
        /**
         * Sync filter values to products
         */
        private function filter() {
                $this->model_extension_maza_cron->callToAdmin('extension/maza/filter/sync');
        }
        
        /**
         * Delete Temporary files
         */
        private function removeJunkFile(){
            // Backup archive files
            foreach(glob(DIR_UPLOAD . 'mz.*.backup.zip') as $file){
                unlink($file);
            }
        }
}
