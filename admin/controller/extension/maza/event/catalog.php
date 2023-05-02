<?php
class ControllerExtensionMazaEventCatalog extends Controller {
    
        
        /**
         * Clear category connection when category deleted
         * @param String $route controller route
         * @param Array $param parameter of method
         * @return Void
         */
        public function deleteCategory(String $route, array $param) : void {
                $category_id = $param[0];
                
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_category WHERE category_id = '" . (int)$category_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_to_category WHERE category_id = '" . (int)$category_id . "'");
        }
        
        /**
         * Clear manufacturer connection when manufacturer deleted
         * @param String $route controller route
         * @param Array $param parameter of method
         * @return Void
         */
        public function deleteManufacturer(String $route, array $param) : void {
                $manufacturer_id = $param[0];
                
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
        }

        /**
         * Clear product connection when product deleted
         * @param String $route controller route
         * @param Array $param parameter of method
         * @return Void
         */
        public function deleteProduct(String $route, array $param) : void {
                $product_id = $param[0];
                
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_blog_article_product WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_catalog_data_to_product WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_filter_value_to_product WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_to_tags WHERE product_id = '" . (int)$product_id . "'");
        }
}
