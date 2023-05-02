<?php
class ControllerExtensionMzWidgetCart extends maza\layout\Widget {
	private $error = array();
        
    public function index() {
        $this->load->language('extension/mz_widget/cart');
        
        $this->load->model('tool/image');
        $this->load->model('localisation/language');

        $data = array();
        
        // Language
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        // Status
        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        // Title
        if(isset($this->request->post['widget_title'])){
            $data['widget_title'] = $this->request->post['widget_title'];
        } else {
            foreach ($data['languages'] as $language) {
                $data['widget_title'][$language['language_id']] = $this->language->get('text_cart');
            }
        }
        
        // Icon
        $data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder_svg'] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
        $data['placeholder_font']   = 'fa fa-font';
        
        // Image
        if(isset($this->request->post['widget_icon_image'])){
            $data['widget_icon_image'] = $this->request->post['widget_icon_image'];
        } else {
            $data['widget_icon_image'] =  array();
        }
        
        // image svg
        if(isset($this->request->post['widget_icon_svg'])){
            $data['widget_icon_svg'] = $this->request->post['widget_icon_svg'];
        } else {
            $data['widget_icon_svg'] =  array();
        }
        
        // image font
        if(isset($this->request->post['widget_icon_font'])){
            $data['widget_icon_font'] = $this->request->post['widget_icon_font'];
        } else {
            $data['widget_icon_font'] =  array();
        }
        
        // Image
        $data['thumb_icon_image'] = array();
        
        if (isset($this->request->post['widget_icon_image'])){
            foreach ($this->request->post['widget_icon_image'] as $language_id => $icon_image) {
                if($icon_image){
                    $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize($icon_image, 100, 100);
                } else {
                    $data['thumb_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }
        
        // image svg
        $data['thumb_icon_svg'] = array();
        
        if (isset($this->request->post['widget_icon_svg'])){
            foreach ($this->request->post['widget_icon_svg'] as $language_id => $icon_svg) {
                if($icon_svg){
                    $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $icon_svg;
                } else {
                    $data['thumb_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
                }
            }
        }
        
        if(isset($this->request->post['widget_icon_width'])){
            $data['widget_icon_width'] = $this->request->post['widget_icon_width'];
        } else {
            $data['widget_icon_width'] =  '';
        }
        
        if(isset($this->request->post['widget_icon_height'])){
            $data['widget_icon_height'] = $this->request->post['widget_icon_height'];
        } else {
            $data['widget_icon_height'] =  '';
        }
        
        if(isset($this->request->post['widget_icon_size'])){
            $data['widget_icon_size'] = $this->request->post['widget_icon_size'];
        } else {
            $data['widget_icon_size'] =  '';
        }
        
        
        $this->response->setOutput($this->load->view('extension/mz_widget/cart', $data));
    }
    
    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/mz_widget/cart')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    /**
     * Change default setting
     */
    public function getSettings(): array {
        $setting['xl'] = $setting['lg'] = $setting['md'] = 
        $setting['sm'] = $setting['xs'] = array(
            'widget_flex_grow' => 0,
            'widget_flex_shrink' => 0,
        );
        
        return \maza\array_merge_subsequence(parent::getSettings(), $setting);
    }
}
