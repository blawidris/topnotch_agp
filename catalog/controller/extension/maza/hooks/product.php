<?php
class ControllerExtensionMazahooksProduct extends Controller {
    /**
     * add data for product list view
     * @param Array $data data
     * @param Array $setting data
     */
    public function view(array &$data, array $setting = array()): void {
        // if(!$this->config->get('maza_status')) return null;
        
        if(isset($setting['mz_suffix'])){
            $data['mz_suffix'] = $setting['mz_suffix'];
        }
        
        $data['special_countdown_status']  =    $this->mz_skin_config->get('catalog_grid_countdown_status');
        $data['special_sold_status']       =    $this->mz_skin_config->get('catalog_grid_sold_status');
        $data['lazy_loading']              =    $this->mz_skin_config->get('catalog_grid_image_lazy_loading');
        $data['transparent']               =    $this->model_extension_maza_image->transparent($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
    }
    
    /**
     * Add extra meta data to products data list
     * @param Array $products_info
     * @param Array $products_data
     * @param Array $setting
     */
    public function AddToProductDataList(array $products_info, array &$products_data, array $setting = array()): void {
        // if(!$this->config->get('maza_status')) return null;
        
        // Add extra data to product data array
        foreach($products_data as $key => $product_data){
            $product_id = $product_data['product_id'];
            
            // Get product info if not exist
            if(empty($products_info[$product_id])){
                $products_info[$product_id] = $this->model_catalog_product->getProduct($product_id);
            }

            // data
            if($products_info[$product_id]['manufacturer_id'] && (!isset($setting['product_grid_manufacturer']) || $setting['product_grid_manufacturer'])){
                $products_data[$key]['mz_manufacturer'] = $products_info[$product_id]['manufacturer'];
                $products_data[$key]['mz_manufacturer_href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $products_info[$product_id]['manufacturer_id']);
            }
            
            // Wishlist
            $products_data[$key]['mz_wishlist'] = $this->model_extension_maza_catalog_product->isWishlisted($product_id);
            
            // Compared
            if(isset($this->session->data['compare'])){
                $products_data[$key]['mz_compare'] = in_array($product_id, $this->session->data['compare']);
            } else {
                $products_data[$key]['mz_compare'] = false;
            }
            
            // Default data
            $products_data[$key]['mz_special_expire_at'] = false;
            $products_data[$key]['mz_special_quantity'] = false;
            $products_data[$key]['mz_special_sold'] = false;
            
            # Sale or discount badge
            $sale = false;
            if($this->mz_skin_config->get('catalog_sale_badge_status') && !is_null($products_info[$product_id]['special']) && (float)$products_info[$product_id]['special'] >= 0){
                // sale text discount
                if($this->mz_skin_config->get('catalog_sale_badge_type') === 'text'){
                    $sale = $this->language->get('text_sale');
                // sale discount badge
                } elseif($this->mz_skin_config->get('catalog_sale_badge_type') === 'discount') {

                    $price_amount = $this->tax->calculate($products_info[$product_id]['price'], $products_info[$product_id]['tax_class_id'], $this->config->get('config_tax'));
                    $special_amount = $this->tax->calculate($products_info[$product_id]['special'], $products_info[$product_id]['tax_class_id'], $this->config->get('config_tax'));

                    $discount = ($price_amount - (float)$special_amount) / $price_amount *  100;
                    $sale = '-' . round($discount) . '%';
                }
            }
            $products_data[$key]['mz_sale'] = $sale;

            # New badge
            $new = false;
            if($this->mz_skin_config->get('catalog_new_badge_status')){
                $date_added = new DateTime($products_info[$product_id]['date_added']);
                $current_date = new DateTime();
                $current_date->modify('-' . (int)$this->mz_skin_config->get('catalog_new_badge_timeout') . ' day');

                if($date_added > $current_date){
                    $new = $this->language->get('text_new');
                }
            }
            $products_data[$key]['mz_new'] = $new;
            
            # Product image width
            if(isset($setting['product_image_width'])){
                $product_image_width = $setting['product_image_width'];
            } else {
                $product_image_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
            }

            # Product image height
            if(isset($setting['product_image_height'])){
                $product_image_height = $setting['product_image_height'];
            } else {
                $product_image_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
            }
            
            # Product image srcset
            if(isset($setting['product_image_srcset'])){
                $product_image_srcset = $setting['product_image_srcset'];
            } else {
                $product_image_srcset = $this->mz_skin_config->get('catalog_grid_image_srcset');
            }
            
            # Product image srcset
            $products_data[$key]['mz_srcset'] = $this->model_extension_maza_image->getSrcSet($product_image_srcset, $products_info[$product_id]['image'], $product_image_width, $product_image_height);
                
            # additional images
            $images = array();
            
            $additional_image_status = $this->mz_skin_config->get('catalog_grid_additional_image');
            
            if(isset($setting['product_grid_additional_image']) && $setting['product_grid_additional_image'] >= 0){
                $additional_image_status = $setting['product_grid_additional_image'];
            }
            
            if($additional_image_status){
                $product_images = $this->model_catalog_product->getProductImages($products_info[$product_id]['product_id']);
                if($this->mz_skin_config->get('catalog_grid_additional_image_limit')){
                    $product_images = array_slice($product_images, 0, $this->mz_skin_config->get('catalog_grid_additional_image_limit') - 1);
                }
                foreach ($product_images as $product_image) {
                    $images[] = array(
                        'image' => $this->model_tool_image->resize($product_image['image'], $product_image_width, $product_image_height),
                        'srcset' => $this->model_extension_maza_image->getSrcSet($product_image_srcset, $product_image['image'], $product_image_width, $product_image_height),
                    );
                }
            }
            $products_data[$key]['mz_images'] = $images;
            
            # Special price expire at
            if(!is_null($products_info[$product_id]['special']) && (float)$products_info[$product_id]['special'] >= 0 && $this->mz_skin_config->get('catalog_special_countdown_status') 
                    && !empty($products_info[$product_id]['mz_special_date_end']) && !in_array($products_info[$product_id]['mz_special_date_end'], ['0000-00-00', '0000-00-00 00:00:00'])){
                
                $current_datetime = new DateTime();
                $special_date_end = new DateTime($products_info[$product_id]['mz_special_date_end']);
                
                // If showtime is specifiy then show countdown only when specify time is remaining to expire special price
                if($this->mz_skin_config->get('catalog_special_countdown_showtime')){
                    $current_datetime->modify('+' . (int)$this->mz_skin_config->get('catalog_special_countdown_showtime') . ' minute');
                }
                
                if(!$this->mz_skin_config->get('catalog_special_countdown_showtime') || $special_date_end < $current_datetime){
                    $products_data[$key]['mz_special_expire_at'] = $products_info[$product_id]['mz_special_date_end'];
                }
            }
            
            # Sold on special price
            if(!is_null($products_info[$product_id]['special']) && (float)$products_info[$product_id]['special'] >= 0 && $this->mz_skin_config->get('catalog_special_sold_status') && !empty($products_info[$product_id]['mz_special_quantity'])){
                
                // If showtime is specifiy then display sold status only when specify quantity is left to sell in total quantity of special price
                if(!$this->mz_skin_config->get('catalog_special_sold_showtime') 
                        || ($this->mz_skin_config->get('catalog_special_sold_showtime') && ($products_data[$key]['mz_special_quantity'] - $products_data[$key]['mz_special_sold']) >= $this->mz_skin_config->get('catalog_special_sold_showtime'))){
                    
                    $products_data[$key]['mz_special_quantity'] = (int)$products_info[$product_id]['mz_special_quantity'];
                    $products_data[$key]['mz_special_sold'] = (int)$products_info[$product_id]['mz_special_sold'];
                    $products_data[$key]['mz_special_sold_perc'] = round(($products_info[$product_id]['mz_special_sold'] * 100) / $products_data[$key]['mz_special_quantity']);
                    
                    // Special sold text
                    if($this->mz_skin_config->get('catalog_special_sold_unit')=='qty'){
                        $products_data[$key]['mz_special_sold_text'] = sprintf($this->language->get('text_claimed'), $products_info[$product_id]['mz_special_sold'] . '/' . $products_data[$key]['mz_special_quantity']);
                    } else {
                        $products_data[$key]['mz_special_sold_text'] = sprintf($this->language->get('text_claimed'), $products_data[$key]['mz_special_sold_perc'] . '%');
                    }
                }
            }
        }
    }
    
    /**
     * Add extra meta data to product detail
     * @param Array $product_info
     * @param Array $data
     */
    public function detail(array $product_info, array &$data): void {
        // Wishlist
        $data['mz_wishlist'] = $this->model_extension_maza_catalog_product->isWishlisted($product_info['product_id']);
        
        // Compared
        if(isset($this->session->data['compare'])){
            $data['mz_compare'] = in_array($product_info['product_id'], $this->session->data['compare']);
        } else {
            $data['mz_compare'] = false;
        }

        $data['mz_videos']           =   array_map('maza\getEmbed', $this->model_extension_maza_catalog_product->getVideos($product_info['product_id']));
        
        $data['quantity']            =   $product_info['quantity'];
        $data['stock_status']        =   $product_info['stock_status'];
        $data['tax_class_id']        =   $product_info['tax_class_id'];
        $data['manufacturer_id']     =   $product_info['manufacturer_id'];

        $data['mz_extra']   = array(
            'sku'   =>  $product_info['sku'],
            'upc'   =>  $product_info['upc'],
            'ean'   =>  $product_info['ean'],
            'jan'   =>  $product_info['jan'],
            'isbn'  =>  $product_info['isbn'],
            'mpn'   =>  $product_info['mpn'],
            'viewed'=>  $product_info['viewed'],
            'sold'  =>  $this->model_extension_maza_catalog_product->getTotalOrders($product_info['product_id']),
            'date_modified' => date($this->language->get('date_format_short'), strtotime($product_info['date_modified'])),
        );

        if($product_info['date_available']){
            $data['mz_extra']['date_available']  =   date($this->language->get('date_format_short'), strtotime($product_info['date_available']));
        }
        if($product_info['weight'] > 0){
            $data['mz_extra']['weight']  =   $this->weight->format($product_info['weight'], $product_info['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'));
        }
        if($product_info['length'] > 0){
            $data['mz_extra']['length']  =   $this->length->format($product_info['length'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'));
        }
        if($product_info['width'] > 0){
            $data['mz_extra']['width']   =   $this->length->format($product_info['width'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'));
        }
        if($product_info['height'] > 0){
            $data['mz_extra']['height']  =   $this->length->format($product_info['height'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'));
        }
        
        // Default data
        $data['mz_special_expire_at'] = false;
        $data['mz_special_quantity'] = false;
        $data['mz_special_sold'] = false;
        
        # Product image srcset
        $data['mz_thumb_srcset'] = $this->model_extension_maza_image->getSrcSet($this->mz_skin_config->get('catalog_thumb_image_srcset'), $product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
        
        # Additional image srcset
        if($data['images'] && $this->mz_skin_config->get('catalog_additional_image_srcset') && array_filter(array_values($this->mz_skin_config->get('catalog_additional_image_srcset')))){
            $data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

            foreach ($results as $result) {
                $data['images'][] = array(
                    'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
                    'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')),
                    'srcset' => $this->model_extension_maza_image->getSrcSet($this->mz_skin_config->get('catalog_additional_image_srcset'), $result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
                );
            }
        }

        // Sale or discount badge and price number
        if(!is_null($product_info['special']) && (float)$product_info['special'] >= 0 && (float)$product_info['price'] > 0){
            $price_amount = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
            $special_amount = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));

            $data['mz_sale'] = '-' . round(($price_amount - $special_amount) / $price_amount * 100) . '%';
            $data['mz_price_num'] = $special_amount;
            
        } else {
            $data['mz_sale'] = false;
            $data['mz_price_num'] = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
        }

        // Special price expire at
        if(!is_null($product_info['special']) && (float)$product_info['special'] >= 0 && $this->mz_skin_config->get('catalog_special_countdown_status') 
                && !empty($product_info['mz_special_date_end']) && !in_array($product_info['mz_special_date_end'], ['0000-00-00', '0000-00-00 00:00:00'])){

            $current_datetime = new DateTime();
            $special_date_end = new DateTime($product_info['mz_special_date_end']);

            // If showtime is specifiy then show countdown only when specify time is remaining to expire special price
            if($this->mz_skin_config->get('catalog_special_countdown_showtime')){
                $current_datetime->modify('+' . (int)$this->mz_skin_config->get('catalog_special_countdown_showtime') . ' minute');
            }

            if(!$this->mz_skin_config->get('catalog_special_countdown_showtime') || $special_date_end < $current_datetime){
                $data['mz_special_expire_at'] = $product_info['mz_special_date_end'];
            }
        }

        // Sold on special price
        if(!is_null($product_info['special']) && (float)$product_info['special'] >= 0 && $this->mz_skin_config->get('catalog_special_sold_status') && !empty($product_info['mz_special_quantity'])){

            // If showtime is specifiy then display sold status only when specify quantity is left to sell in total quantity of special price
            if(!$this->mz_skin_config->get('catalog_special_sold_showtime') 
                    || ($this->mz_skin_config->get('catalog_special_sold_showtime') && ($data['mz_special_quantity'] - $data['mz_special_sold']) >= $this->mz_skin_config->get('catalog_special_sold_showtime'))){

                $data['mz_special_quantity'] = (int)$product_info['mz_special_quantity'];
                $data['mz_special_sold'] = (int)$product_info['mz_special_sold'];
                $data['mz_special_sold_perc'] = round(($product_info['mz_special_sold'] * 100) / $data['mz_special_quantity']);

                // Special sold text
                if($this->mz_skin_config->get('catalog_special_sold_unit')=='qty'){
                    $data['mz_special_sold_text'] = sprintf($this->language->get('text_claimed'), $product_info['mz_special_sold'] . '/' . $data['mz_special_quantity']);
                } else {
                    $data['mz_special_sold_text'] = sprintf($this->language->get('text_claimed'), $data['mz_special_sold_perc'] . '%');
                }
            }
        }

        // Schema
        if ($this->config->get('maza_schema')) {
            if(!empty($data['breadcrumbs'])){
                $this->mz_document->addSchema(maza\Schema::breadcrumb($data['breadcrumbs']));
            }
            
            $schema = array(
                '@context' => 'https://schema.org/',
                '@type' => 'Product',
                'name' => $product_info['name'],
                'image' => maza\getImageURL($product_info['image']),
                'description' => $product_info['meta_description']?:$product_info['description'],
                'sku' =>  $product_info['sku'],
                'upc' =>  $product_info['upc'],
                'ean' =>  $product_info['ean'],
                'jan' =>  $product_info['jan'],
                'isbn' =>  $product_info['isbn'],
                'mpn' =>  $product_info['mpn'],
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => $this->config->get('config_currency'),
                    'price' => $data['mz_price_num'],
                    'priceValidUntil' => $product_info['mz_special_date_end']?:'3333-01-01',
                    'availability' => $product_info['quantity']?'https://schema.org/InStock':'https://schema.org/OutOfStock',
                    'url' => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
                ]
            );
    
            if ($product_info['rating'] > 0) {
                $schema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => $product_info['rating'],
                    'reviewCount' => $product_info['reviews']
                ];
            }
    
            if ($product_info['manufacturer']) {
                $schema['brand'] = [
                    '@type' => 'Brand',
                    'name' => $product_info['manufacturer'],
                    'url' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'])
                ];
            }
    
            $this->load->model('catalog/review');
    
            $reviews = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], 0, 1);
    
            if($reviews){
                $schema['review'] = [
                    '@type' => 'Review',
                    'reviewBody' => $reviews[0]['text'],
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => (int)$reviews[0]['rating'],
                        'bestRating' => 5,
                    ],
                    'dateCreated' => $reviews[0]['date_added'],
                    'author' => [
                        '@type' => 'Person',
                        'name' => $reviews[0]['author']
                    ]
                ];
            }

            $this->mz_document->addSchema($schema);
        }
        

        // OGP
        if ($this->config->get('maza_ogp')) {
            $this->mz_document->addOGP('og:type', 'product');
            $this->mz_document->addOGP('og:title', $product_info['meta_title']?:$product_info['name']);
            $this->mz_document->addOGP('og:description', (string)$product_info['meta_description']);
            $this->mz_document->addOGP('og:url', $this->url->link('product/product', 'product_id=' . $product_info['product_id']));
            $this->mz_document->addOGP('og:image', maza\getImageURL($product_info['image']));
        }
    }
}