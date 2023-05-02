<?php
class ControllerExtensionMzWidgetSearch extends maza\layout\Widget {
	private $error = array();
        
        public function index() {
                $this->load->language('extension/mz_widget/search');

                $data = array();
                
                // Status
                if(isset($this->request->post['widget_status'])){
                    $data['widget_status'] = $this->request->post['widget_status'];
                } else {
                    $data['widget_status'] =  0;
                }
                
                // Category status
                if(isset($this->request->post['widget_category_status'])){
                    $data['widget_category_status'] = $this->request->post['widget_category_status'];
                } else {
                    $data['widget_category_status'] =  0;
                }
                
                // Category depth
                if(isset($this->request->post['widget_category_depth'])){
                    $data['widget_category_depth'] = $this->request->post['widget_category_depth'];
                } else {
                    $data['widget_category_depth'] =  1;
                }
                
                // Autocomplete
                if(isset($this->request->post['widget_autocomplete_status'])){
                    $data['widget_autocomplete_status'] = $this->request->post['widget_autocomplete_status'];
                } else {
                    $data['widget_autocomplete_status'] =  1;
                }
                
                // Autocomplete product limit
                if(isset($this->request->post['widget_autocomplete_limit'])){
                    $data['widget_autocomplete_limit'] = $this->request->post['widget_autocomplete_limit'];
                } else {
                    $data['widget_autocomplete_limit'] =  5;
                }
                
                // Search button type
                if(isset($this->request->post['widget_search_button_type'])){
                    $data['widget_search_button_type'] = $this->request->post['widget_search_button_type'];
                } else {
                    $data['widget_search_button_type'] =  'icon';
                }
                
                $data['list_search_button_type'] = array(
                    array('id' => 'icon', 'name' => $this->language->get('text_icon')),
                    array('id' => 'text', 'name' => $this->language->get('text_text')),
                    array('id' => 'both', 'name' => $this->language->get('text_both'))
                );
                
                $this->load->model('localisation/language');
                $data['languages'] = $this->model_localisation_language->getLanguages();
                
                // Search placeholder
                if(isset($this->request->post['widget_placeholder'])){
                    $data['widget_placeholder'] = $this->request->post['widget_placeholder'];
                } else {
                    foreach ($data['languages'] as $language) {
                        $data['widget_placeholder'][$language['language_id']] = $this->language->get('text_search');
                    }
                }
                
                // Route
                if(isset($this->request->post['widget_search_route'])){
                    $data['widget_search_route'] = $this->request->post['widget_search_route'];
                } else {
                    $data['widget_search_route'] =  'auto_switch';
                }
                
                $data['list_search_route'] = array(
                    array('id' => 'auto_switch', 'name' => $this->language->get('text_auto_switch')),
                    array('id' => 'product', 'name' => $this->language->get('text_product')),
                    array('id' => 'blog', 'name' => $this->language->get('text_blog'))
                );
                
                $this->response->setOutput($this->load->view('extension/mz_widget/search', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mz_widget/search')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
