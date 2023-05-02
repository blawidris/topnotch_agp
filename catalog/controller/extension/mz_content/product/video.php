<?php
class ControllerExtensionMzContentProductVideo extends maza\layout\Content {
        public function index($setting) {
                $data = array();
                $data['mz_suffix']        = $setting['mz_suffix'];
                
                $data['column_xs']        = $setting['content_column_xs'];
                $data['column_sm']        = $setting['content_column_sm'];
                $data['column_md']        = $setting['content_column_md'];
                $data['column_lg']        = $setting['content_column_lg'];
                $data['column_xl']        = $setting['content_column_xl'];
                
                return $this->load->view('product/product/video', $data);
        }
}
