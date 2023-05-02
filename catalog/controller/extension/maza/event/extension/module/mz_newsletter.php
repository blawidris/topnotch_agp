<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventExtensionModuleMzNewsletter extends Controller {
        
        /**
         * add newsletter for new customer
         * @param String $route route
         * @param Array $param parameter of method
         * @return Void
         */
        public function addCustomerAfter($route, $param) {
            if($this->config->get('maza_status') && $this->config->get('module_mz_newsletter_status')){
                $data = $param[0];

                $this->load->model('extension/maza/newsletter');

                $subscriber_info = $this->model_extension_maza_newsletter->getSubscriber($data['email']);

                if(isset($data['newsletter']) && $data['newsletter'] && !$subscriber_info){
                    $this->model_extension_maza_newsletter->addSubscriber($data['email']);
                } elseif($subscriber_info) {
                    $this->model_extension_maza_newsletter->deleteSubscriber($subscriber_info['token']);
                }
            }
        }
        
        /**
         * edit newsletter
         * @param String $route route
         * @param Array $param parameter of method
         * @return Void
         */
        public function editNewsletter($route, $param) {
            if($this->config->get('maza_status') && $this->config->get('module_mz_newsletter_status')){
                $newsletter = $param[0];
                
                $this->load->model('extension/maza/newsletter');
                
                $subscriber_info = $this->model_extension_maza_newsletter->getSubscriber($this->customer->getEmail());
                
                if($newsletter && !$subscriber_info){
                    $this->model_extension_maza_newsletter->addSubscriber($this->customer->getEmail(), 1);
                } elseif(!$newsletter && $subscriber_info) {
                    $this->model_extension_maza_newsletter->deleteSubscriber($subscriber_info['token']);
                }
            }
        }
}
