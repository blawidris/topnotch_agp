<?php
class ControllerExtensionMzContentCategorySortBy extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('product/common/sort_by');
        }
}
