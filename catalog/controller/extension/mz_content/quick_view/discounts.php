<?php
class ControllerExtensionMzContentQuickViewDiscounts extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/product/discounts');
        }
}
