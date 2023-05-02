<?php
class ControllerExtensionMzWidgetCategory extends maza\layout\Widget {
	private $error = array();
        
    public function index(): void {
        $this->load->language('extension/mz_widget/category');

        $data = array();
        

        if(isset($this->request->post['widget_status'])){
            $data['widget_status'] = $this->request->post['widget_status'];
        } else {
            $data['widget_status'] =  0;
        }
        
        // Category of
        if(isset($this->request->post['widget_category_of'])){
            $data['widget_category_of'] = $this->request->post['widget_category_of'];
        } else {
            $data['widget_category_of'] =  'auto_switch';
        }
        
        $data['list_category_of'] = array(
            array('id' => 'auto_switch', 'name' => $this->language->get('text_auto_switch')),
            array('id' => 'product', 'name' => $this->language->get('text_product')),
            array('id' => 'blog', 'name' => $this->language->get('text_blog'))
        );
        
        
        $this->response->setOutput($this->load->view('extension/mz_widget/category', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_widget/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
