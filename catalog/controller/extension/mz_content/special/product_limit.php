<?php
class ControllerExtensionMzContentSpecialProductLimit extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/common/product_limit');
        }
}
