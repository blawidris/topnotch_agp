<?php
class ControllerExtensionMzContentSpecialProducts extends maza\layout\Content {
    private $error = array();
    
    public function index(): void {
        $this->load->language('extension/mz_content/special/products');

        $data = array();
        

        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
        }
        
        if(isset($this->request->post['content_list_grid'])){
            $data['content_list_grid'] = $this->request->post['content_list_grid'];
        } else {
            $data['content_list_grid'] =  'grid';
        }
        
        
        // Grid size
        if(isset($this->request->post['content_column_xs'])){
            $data['content_column_xs'] = $this->request->post['content_column_xs'];
        } else {
            $data['content_column_xs'] =  2;
        }
        
        if(isset($this->request->post['content_column_sm'])){
            $data['content_column_sm'] = $this->request->post['content_column_sm'];
        } else {
            $data['content_column_sm'] =  2;
        }
        
        if(isset($this->request->post['content_column_md'])){
            $data['content_column_md'] = $this->request->post['content_column_md'];
        } else {
            $data['content_column_md'] =  3;
        }
        
        if(isset($this->request->post['content_column_lg'])){
            $data['content_column_lg'] = $this->request->post['content_column_lg'];
        } else {
            $data['content_column_lg'] =  4;
        }
        
        if(isset($this->request->post['content_column_xl'])){
            $data['content_column_xl'] = $this->request->post['content_column_xl'];
        } else {
            $data['content_column_xl'] =  5;
        }
        
        // Products product element
        if(isset($this->request->post['content_cart_status'])){
            $data['content_cart_status'] = $this->request->post['content_cart_status'];
        } else {
            $data['content_cart_status'] =  1;
        }
        
        if(isset($this->request->post['content_compare_status'])){
            $data['content_compare_status'] = $this->request->post['content_compare_status'];
        } else {
            $data['content_compare_status'] =  1;
        }
        
        if(isset($this->request->post['content_wishlist_status'])){
            $data['content_wishlist_status'] = $this->request->post['content_wishlist_status'];
        } else {
            $data['content_wishlist_status'] =  1;
        }
        
        if(isset($this->request->post['content_quick_view_status'])){
            $data['content_quick_view_status'] = $this->request->post['content_quick_view_status'];
        } else {
            $data['content_quick_view_status'] =  1;
        }
        
        if(isset($this->request->post['content_rating_status'])){
            $data['content_rating_status'] = $this->request->post['content_rating_status'];
        } else {
            $data['content_rating_status'] =  1;
        }

        if(isset($this->request->post['content_manufacturer_status'])){
            $data['content_manufacturer_status'] = $this->request->post['content_manufacturer_status'];
        } else {
            $data['content_manufacturer_status'] =  0;
        }

        if(isset($this->request->post['content_tax_status'])){
            $data['content_tax_status'] = $this->request->post['content_tax_status'];
        } else {
            $data['content_tax_status'] =  0;
        }
        
        if(isset($this->request->post['content_description_status'])){
            $data['content_description_status'] = $this->request->post['content_description_status'];
        } else {
            $data['content_description_status'] =  0;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/special/products', $data));
    }
    
    protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/mz_content/special/products')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
