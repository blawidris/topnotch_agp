<?php
class ControllerExtensionMzContentProductVideo extends maza\layout\Content {
    public function index(): void {
        $this->load->language('extension/mz_content/product/video');

        $data = array();
        

        if(isset($this->request->post['content_status'])){
            $data['content_status'] = $this->request->post['content_status'];
        } else {
            $data['content_status'] =  0;
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
            $data['content_column_md'] =  2;
        }
        
        if(isset($this->request->post['content_column_lg'])){
            $data['content_column_lg'] = $this->request->post['content_column_lg'];
        } else {
            $data['content_column_lg'] =  3;
        }
        
        if(isset($this->request->post['content_column_xl'])){
            $data['content_column_xl'] = $this->request->post['content_column_xl'];
        } else {
            $data['content_column_xl'] =  3;
        }
        
        $this->response->setOutput($this->load->view('extension/mz_content/product/video', $data));
    }
}
