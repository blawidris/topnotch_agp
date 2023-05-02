<?php
class ControllerExtensionMzWidgetForm extends maza\layout\Widget {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_widget/form');
        
        $this->load->model('extension/maza/asset');
        $this->load->model('localisation/language');
        $this->load->model('extension/maza/form');
        
        $data = array();
        
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        // Title
        if(isset($this->request->post['widget_title'])){
            $data['widget_title'] = $this->request->post['widget_title'];
        } else {
            $data['widget_title'] =  1;
        }

        // Label
        if(isset($this->request->post['widget_label'])){
            $data['widget_label'] = $this->request->post['widget_label'];
        } else {
            $data['widget_label'] =  'top';
        }

        $data['list_label'] = array(
            array('code' => '', 'text' => $this->language->get('text_disabled')),
            array('code' => 'top', 'text' => $this->language->get('text_top')),
            array('code' => 'left', 'text' => $this->language->get('text_left'))
        );
        
        // color
        if(isset($this->request->post['widget_color'])){
            $data['widget_color'] = $this->request->post['widget_color'];
        } else {
            $data['widget_color'] =  'primary';
        }
        
        $color_types = $this->model_extension_maza_asset->getColorTypes();
        
        $data['colors'] = array();
        foreach($color_types as $color_type){
            $data['colors'][] = array('code' => $color_type, 'text' => ucfirst($color_type));
        }

        // Size
        if(isset($this->request->post['widget_size'])){
            $data['widget_size'] = $this->request->post['widget_size'];
        } else {
            $data['widget_size'] =  'md';
        }

        $data['sizes'] = array(
            array('code' => 'sm', 'text' => $this->language->get('text_small')),
            array('code' => 'md', 'text' => $this->language->get('text_medium')),
            array('code' => 'lg', 'text' => $this->language->get('text_large'))
        );

        // Submit button position
        if(isset($this->request->post['widget_submit_align'])){
            $data['widget_submit_align'] = $this->request->post['widget_submit_align'];
        } else {
            $data['widget_submit_align'] =  'left';
        }

        // Form
        if(isset($this->request->post['widget_form_id'])){
            $data['widget_form_id'] = $this->request->post['widget_form_id'];
        } else {
            $data['widget_form_id'] = 0;
        }

        $form_info = $this->model_extension_maza_form->getForm($data['widget_form_id']);

        if($form_info){
            $data['widget_form'] = $form_info['name'];
        }
        
        $data['user_token'] = $this->session->data['user_token'];
        
        $this->response->setOutput($this->load->view('extension/mz_widget/form', $data));
    }

    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting = parent::getSettings();
        
        $setting['widget_cache'] = 'hard';
        
        return $setting;
    }
}
