<?php
class ControllerExtensionMzContentCategoryProductCompare extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/common/product_compare');
        }
}
