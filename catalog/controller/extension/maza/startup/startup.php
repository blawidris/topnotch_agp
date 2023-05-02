<?php
class ControllerExtensionMazaStartupStartup extends Controller {
	public function index(): Action {
        // Config
        $this->config->load('maza/catalog');
        
        // Library
        $this->mz_theme_config  = new maza\config\Theme();
        $this->mz_skin_config   = new maza\config\Skin();
        $this->mz_document      = new maza\Document();
        $this->mz_cache         = new maza\Cache($this->config->get('mz_cache_engine'), $this->config->get('cache_expire'));
        $this->mz_load          = new maza\Loader($this->registry);
        $this->mz_hook          = new maza\Hook($this->registry);
        $this->mz_minifier      = new maza\Minifier();
        
        $this->cart->mz_skin_config = $this->mz_skin_config;

        if ($this->config->get('db_autostart')) {
            if(version_compare(VERSION, '3.0.0.0') < 0){
                $this->mz_db = new maza\DB($this->config->get('db_type'), $this->config->get('db_hostname'), $this->config->get('db_username'), $this->config->get('db_password'), $this->config->get('db_database'), $this->config->get('db_port'));
            } else {
                $this->mz_db = new maza\DB($this->config->get('db_engine'), $this->config->get('db_hostname'), $this->config->get('db_username'), $this->config->get('db_password'), $this->config->get('db_database'), $this->config->get('db_port'));
            }
        }
        
        // Model Autoload
        if ($this->config->has('mz_model_autoload')) {
            foreach ($this->config->get('mz_model_autoload') as $value) {
                $this->load->model($value);
            }
        }

        // Developer mode
        if($this->config->get('maza_developer_mode')){
            $this->config->set('maza_cache_status', 0);
            $this->config->set('maza_minify_css', 0);
            $this->config->set('maza_minify_js', 0);
            $this->config->set('maza_combine_css', 0);
            $this->config->set('maza_combine_js', 0);
            // $this->config->set('maza_css_autoprefix', 0);
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            @set_time_limit(0);
        }
        
        // Page cache
        if($this->config->get('maza_cache_page') && $this->mz_cache->canPageCache()){
            $page_cache = $this->mz_cache->get('page.' . $this->session->data['language'] . '.' . $this->session->data['currency'] . '.' . md5((isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] === 'on'?'https':'http') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI']));
        } else {
            $page_cache = false;
        }
        if($page_cache){
            $this->mz_cache->setVar('page_cache', $page_cache);
            return new Action('extension/maza/cache/page');
        }
        
        // Common language
        $this->load->language('extension/maza/common');

        // Theme
        $mz_theme_code = $this->config->get('theme_default_directory');
        
        if(isset($this->session->data['user_id']) && isset($this->request->get['mz_theme_code'])){
            $mz_theme_code = $this->request->get['mz_theme_code'];
        }

        $theme_setting = $this->model_extension_maza_theme->getSetting($mz_theme_code, $this->config->get('config_store_id'));
        
        if($theme_setting){
            foreach ($theme_setting as $key => $value) {
                $this->mz_theme_config->set($key, $value);
            }
            
            $this->mz_theme_config->set('theme_code', $mz_theme_code);
            $this->config->set('maza_status', TRUE);
        } else {
            $theme_config = $this->model_extension_maza_theme->getThemeConfig($mz_theme_code);
            
            if($theme_config){
                throw new Exception('Please install theme');
            }

            if(strpos($this->mz_document->getRoute(), 'extension/maza/') === 0){
                return new Action('error/not_found');
            }
            return new Action($this->config->get('action_router'));
        }
        
        return new Action('extension/maza/startup/setting');
	}
}
