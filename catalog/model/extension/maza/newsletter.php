<?php
class ModelExtensionMazaNewsletter extends Model {
    
        /**
         * Get subscriber by email id
         * @param String $email_id
         * @return String detail
         */
        public function getSubscriber($email_id){
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX ."mz_newsletter` WHERE email_id = '" . $this->db->escape($email_id) . "' LIMIT 1");

                return $query->row;
        }
        
        /**
         * Get subscriber by token
         * @param String $token
         * @return String detail
         */
        public function getSubscriberByToken($token){
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX ."mz_newsletter` WHERE `token` = '" . $this->db->escape($token) . "' LIMIT 1");

                return $query->row;
        }
        
        /**
         * Add subscriber
         * @param String $email_id subscriber email id
         * @return Int subscriber id
         */
        public function addSubscriber($email_id, $is_confirmed = 0){
                $this->db->query("INSERT INTO `" . DB_PREFIX ."mz_newsletter` SET email_id = '" . $this->db->escape($email_id) . "', `token` = '" . $this->db->escape(token(32)) . "', `status` = '" . !$this->mz_skin_config->get('newsletter_required_approval') . "', date_added = NOW(), is_confirmed = '" . (int)$is_confirmed . "'");

                return $this->db->getLastId();
        }
        
        /**
         * Confirm subscriber by token
         * @param String $token unique token
         * @return Null
         */
        public function confirmedSubscriber($token){
                $this->db->query("UPDATE `" . DB_PREFIX ."mz_newsletter` SET is_confirmed = 1 WHERE `token` = '" . $this->db->escape($token) . "'");
        }
        
        /**
         * delete subscriber by token
         * @param String $token unique token
         * @return Null
         */
        public function deleteSubscriber($token){
                $this->db->query("DELETE FROM `" . DB_PREFIX ."mz_newsletter` WHERE `token` = '" . $this->db->escape($token) . "'");
        }
        
        /**
         * Send confirmation mail of subscribe
         * @param String $email_id
         * @return NULL
         */
        public function sendSubscribeConfirmMail($email_id){
            
                // Template
                if(isset($this->mz_skin_config->get('newsletter_confirm_subscribe_template')[$this->config->get('config_language_id')])){
                    $template = $this->mz_skin_config->get('newsletter_confirm_subscribe_template')[$this->config->get('config_language_id')];
                } else {
                    $template = array();
                }
                if(empty($template['subject'])){
                    $template['subject'] = $this->language->get('help_mail_subject');
                }
                if(empty($template['message'])){
                    $template['message'] = $this->language->get('help_mail_message');
                }
                
                $subscribe_info = $this->getSubscriber($email_id);
                
                if($subscribe_info){
                    // Add confirm link in message
                    $confirm_link = $this->url->link('extension/maza/newsletter/confirm_subscribe', 'subscribe_token=' . $subscribe_info['token']);
                    $template['message'] = str_replace('[subscribe]', $confirm_link, $template['message']);
                    
                    // Send mail
                    $this->load->model('extension/maza/common');
                    $this->model_extension_maza_common->sendMail($email_id, $template['subject'], $template['message']);
                }
        }
        
        /**
         * Send confirmation mail of unsubscribe
         * @param String $email_id
         * @return NULL
         */
        public function sendUnsubscribeConfirmMail($email_id){
            
                // Template
                if(isset($this->mz_skin_config->get('newsletter_confirm_unsubscribe_template')[$this->config->get('config_language_id')])){
                    $template = $this->mz_skin_config->get('newsletter_confirm_unsubscribe_template')[$this->config->get('config_language_id')];
                } else {
                    $template = array();
                }
                if(empty($template['subject'])){
                    $template['subject'] = $this->language->get('help_mail_subject');
                }
                if(empty($template['message'])){
                    $template['message'] = $this->language->get('help_mail_message');
                }
                
                $subscribe_info = $this->getSubscriber($email_id);
                
                if($subscribe_info){
                    // Add confirm link in message
                    $confirm_link = $this->url->link('extension/maza/newsletter/confirm_unsubscribe', 'subscribe_token=' . $subscribe_info['token']);
                    $template['message'] = str_replace('[unsubscribe]', $confirm_link, $template['message']);
                    
                    // Send mail
                    $this->load->model('extension/maza/common');
                    $this->model_extension_maza_common->sendMail($email_id, $template['subject'], $template['message']);
                }
        }
        
        /**
         * Send welcome mail to new subscriber
         * @param String $email_id
         * @return NULL
         */
        public function sendWelcomeMail($email_id){
            
                // Template
                if(isset($this->mz_skin_config->get('newsletter_welcome_mail_template')[$this->config->get('config_language_id')])){
                    $template = $this->mz_skin_config->get('newsletter_welcome_mail_template')[$this->config->get('config_language_id')];
                } else {
                    $template = array();
                }
                if(empty($template['subject'])){
                    $template['subject'] = $this->language->get('help_mail_subject');
                }
                if(empty($template['message'])){
                    $template['message'] = $this->language->get('help_mail_message');
                }
                
                // Send mail
                $this->load->model('extension/maza/common');
                $this->model_extension_maza_common->sendMail($email_id, $template['subject'], $template['message']);
        }
        
        
}