<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaCommon extends model {
        
        /**
         * @deprecated
         * Get list of opencart pages
         * @return Array page list
         */
        // public function getPages(): array {
        //         $pages = array();
                
        //         $pages[] = array('id' => 'home', 'name' => $this->language->get('text_home'));
        //         $pages[] = array('id' => 'wishlist', 'name' => $this->language->get('text_wishlist'));
        //         $pages[] = array('id' => 'compare', 'name' => $this->language->get('text_compare'));
        //         $pages[] = array('id' => 'checkout', 'name' => $this->language->get('text_checkout'));
        //         $pages[] = array('id' => 'account', 'name' => $this->language->get('text_account'));
        //         $pages[] = array('id' => 'login', 'name' => $this->language->get('text_login'));
        //         $pages[] = array('id' => 'logout', 'name' => $this->language->get('text_logout'));
        //         $pages[] = array('id' => 'order', 'name' => $this->language->get('text_order'));
        //         $pages[] = array('id' => 'register', 'name' => $this->language->get('text_register'));
        //         $pages[] = array('id' => 'cart', 'name' => $this->language->get('text_cart'));
        //         $pages[] = array('id' => 'contact', 'name' => $this->language->get('text_contact'));
        //         $pages[] = array('id' => 'return', 'name' => $this->language->get('text_return'));
        //         $pages[] = array('id' => 'special', 'name' => $this->language->get('text_special'));
        //         $pages[] = array('id' => 'search', 'name' => $this->language->get('text_search'));
        //         $pages[] = array('id' => 'manufacturer', 'name' => $this->language->get('text_brand'));
        //         $pages[] = array('id' => 'sitemap', 'name' => $this->language->get('text_sitemap'));
        //         $pages[] = array('id' => 'tracking', 'name' => $this->language->get('text_tracking'));
        //         $pages[] = array('id' => 'voucher', 'name' => $this->language->get('text_voucher'));
        //         $pages[] = array('id' => 'affiliate_login', 'name' => $this->language->get('text_affiliate_login'));
        //         $pages[] = array('id' => 'affiliate_register', 'name' => $this->language->get('text_affiliate_register'));
        //         $pages[] = array('id' => 'blog/home', 'name' => $this->language->get('text_blog_home'));
        //         $pages[] = array('id' => 'blog/all', 'name' => $this->language->get('text_blog_all'));
        //         $pages[] = array('id' => 'blog/search', 'name' => $this->language->get('text_blog_search'));
        //         $pages[] = array('id' => 'maza/products', 'name' => $this->language->get('text_products'));
        //         $pages[] = array('id' => 'maza/testimonial', 'name' => $this->language->get('text_testimonials'));
                
        //         array_multisort(array_column($pages, 'name'),$pages);
                
        //         return $pages;
        // }
        
        /**
         * @deprecated
         * Get list of URL types
         * @return Array URL types list
         */
        // public function getURLTypes(): array {
        //         return array(
        //                 array('id' => 'custom', 'name' => $this->language->get('text_custom')),
        //                 array('id' => 'route', 'name' => $this->language->get('text_route')),
        //                 array('id' => 'category', 'name' => $this->language->get('text_category')),
        //                 array('id' => 'product', 'name' => $this->language->get('text_product')),
        //                 array('id' => 'manufacturer', 'name' => $this->language->get('text_manufacturer')),
        //                 array('id' => 'information', 'name' => $this->language->get('text_information')),
        //                 array('id' => 'page_builder', 'name' => $this->language->get('text_page_builder')),
        //                 array('id' => 'system', 'name' => $this->language->get('text_system')),
        //                 array('id' => 'popup', 'name' => $this->language->get('text_popup')),
        //                 array('id' => 'drawer', 'name' => $this->language->get('text_drawer')),
        //                 array('id' => 'collapsible', 'name' => $this->language->get('text_collapsible')),
        //         );
        // }
        
        /**
         * Clear all caches
         */
        public function clearCache(): void {
                // Clear asset caches
                $this->mz_document->clear();

                // Clear Maza cache
                $this->mz_cache->clear();
        }
}
