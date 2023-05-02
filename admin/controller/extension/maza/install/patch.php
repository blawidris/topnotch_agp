<?php
class ControllerExtensionMazaInstallPatch extends Controller {
    public function index(): void {
        $this->load->language('extension/maza/install/engine');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'extension/maza/install/engine')) {
            $json['error'] = $this->language->get('error_permission');
        }
        
        if(isset($this->session->data['mz_patch'])){
            $json['next'] = 1;
        }

        if(!$json){
            $this->db->query("SET SESSION sql_mode = ''");
            
            $this->event->register('model/extension/maza/install/merger/content/after', new Action('extension/mz_design/tabs/merger'));
            $this->event->register('model/extension/maza/install/merger/content/after', new Action('extension/mz_design/accordion/merger'));

            $this->event->register('model/extension/maza/install/merger/content/after', new Action('extension/mz_content/product/dynamic/merger'));
            $this->event->register('model/extension/maza/install/merger/design/after', new Action('extension/mz_content/product/dynamic/merger'));

            $this->event->register('model/extension/maza/install/merger/content/after', new Action('extension/mz_content/quick_view/dynamic/merger'));
            $this->event->register('model/extension/maza/install/merger/design/after', new Action('extension/mz_content/quick_view/dynamic/merger'));

            $this->update();

            // Success
            $json['next'] = $this->session->data['mz_patch'] = 1;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function update(): void {
        // Merge setting with old version
        $this->load->model('extension/maza/install/merger');
        $this->load->model('extension/maza/skin');

        // 1.1.4
        if(version_compare($this->config->get('mz_version'), '1.1.4') < 0){
            $this->patch114();
        }

        // 1.2.0
        if(version_compare($this->config->get('mz_version'), '1.2.0') < 0){
            $this->patch120();
        }

        // 1.2.4
        if(version_compare($this->config->get('mz_version'), '1.2.4') < 0){
            $this->patch124();
        }

        // 1.3.0
        if(version_compare($this->config->get('mz_version'), '1.3.0') < 0){
            $this->patch130();
        }

        // 1.4.0
        if(version_compare($this->config->get('mz_version'), '1.4.0') < 0){
            $this->patch140();
        }

        // 1.4.5
        if(version_compare($this->config->get('mz_version'), '1.4.5') < 0){
            $this->patch145();
        }

        // 1.4.6
        if(version_compare($this->config->get('mz_version'), '1.4.6') < 0){
            $this->patch146();
        }

        // 1.4.8
        if(version_compare($this->config->get('mz_version'), '1.4.8') < 0){
            $this->patch148();
        }

        // 1.5
        if(version_compare($this->config->get('mz_version'), '1.5.0') < 0){
            $this->patch150();
        }

        // 1.5
        if(version_compare($this->config->get('mz_version'), '1.5.1') < 0){
            $this->patch151();
        }
    }

    // Version 1.1.4
    private function patch114(): void {
        $this->model_extension_maza_install_merger->content('product.extra', ['content_viewed' => '0', 'content_sku' => '0', 'content_upc' => '0', 'content_ean' => '0', 'content_jan' => '0', 'content_isbn' => '0', 'content_mpn' => '0', 'content_date_modified' => '0', 'content_date_available' => '0', 'content_weight' => '0', 'content_length' => '0', 'content_width' => '0', 'content_height' => '0']);
        $this->model_extension_maza_install_merger->content('product.quantity', ['content_color' => 'light', 'content_outline' => 0]);

        $this->model_extension_maza_install_merger->design('carousel', ['design_image_srcset' => array('lg' => '', 'md' => '', 'sm' => '', 'xs' => '')]);
        $this->model_extension_maza_install_merger->entry('section', maza\layout\Section::getSettings());

        
        $this->model_extension_maza_install_merger->module('mz_product_listing', ['product_grid_manufacturer' => 0]);

        $skins = $this->model_extension_maza_skin->getSkins();
        foreach($skins as $skin){
            $setting = array_merge(['catalog_additional_image_slides' => 4], $this->model_extension_maza_skin->getSetting($skin['skin_id'], 'catalog'));
            $this->model_extension_maza_skin->editSetting($skin['skin_id'], 'catalog', $setting);
        }
    }

    // Version 1.2.0
    private function patch120(): void {
        $this->model_extension_maza_install_merger->design('menu', ['design_title_url_type' => 'custom', 'design_title_url_custom' => '']);
    }

    // Version 1.2.4
    private function patch124(): void {
        // Modify old setting base on new structure
        // Merge new variable of setting to avoid undefined index error
        // return new setting to replace
        $this->model_extension_maza_install_merger->design('button', function($old_setting){
            $setting = array();

            if(isset($old_setting['design_target_popup_id'])){
                $setting['design_url_popup'] = $old_setting['design_target_popup_id'];
                unset($old_setting['design_target_popup_id']);
            }
            if(isset($old_setting['design_target_drawer_id'])){
                $setting['design_url_drawer'] = $old_setting['design_target_drawer_id'];
                unset($old_setting['design_target_drawer_id']);
            }
            if(isset($old_setting['design_target_collapsible_id'])){
                $setting['design_url_collapsible'] = $old_setting['design_target_collapsible_id'];
                unset($old_setting['design_target_collapsible_id']);
            }

            if(isset($old_setting['design_target']) && $old_setting['design_target'] !== 'url'){
                $old_setting['design_target_url_type'] = $old_setting['design_target'];
                unset($old_setting['design_target']);
            }

            foreach($old_setting as $key => $value){
                $setting[str_replace('design_target_', 'design_', $key)] = $value;
            }

            return $setting;
        });

        // Modify old setting base on new structure
        // Merge new variable of setting to avoid undefined index error
        // return new setting to replace
        $this->model_extension_maza_install_merger->design('link', function($old_setting){
            $setting = array();

            if(isset($old_setting['design_target_popup_id'])){
                $setting['design_url_popup'] = $old_setting['design_target_popup_id'];
                unset($old_setting['design_target_popup_id']);
            }
            if(isset($old_setting['design_target_drawer_id'])){
                $setting['design_url_drawer'] = $old_setting['design_target_drawer_id'];
                unset($old_setting['design_target_drawer_id']);
            }
            if(isset($old_setting['design_target_collapsible_id'])){
                $setting['design_url_collapsible'] = $old_setting['design_target_collapsible_id'];
                unset($old_setting['design_target_collapsible_id']);
            }

            if(isset($old_setting['design_target']) && $old_setting['design_target'] !== 'url'){
                $old_setting['design_target_url_type'] = $old_setting['design_target'];
                unset($old_setting['design_target']);
            }

            foreach($old_setting as $key => $value){
                $setting[str_replace('design_target_', 'design_', $key)] = $value;
            }

            return $setting;
        });
    }

    // Version 1.3.0
    private function patch130(): void {
        $this->model_extension_maza_install_merger->module('mz_product_listing', ['product_grid_tax' => 0]);
        $this->model_extension_maza_install_merger->module('mz_article_listing', ['article_image_position' => 'top']);
        $this->model_extension_maza_install_merger->module('mz_filter', ['sort_by' => 'product']);

        $this->model_extension_maza_install_merger->content('product.related', ['content_manufacturer_status' => 0, 'content_tax_status' => 0]);
        $this->model_extension_maza_install_merger->content('category.products', ['content_manufacturer_status' => 0, 'content_tax_status' => 0]);
        $this->model_extension_maza_install_merger->content('manufacturer_info.products', ['content_manufacturer_status' => 0, 'content_tax_status' => 0]);
        $this->model_extension_maza_install_merger->content('search.products', ['content_manufacturer_status' => 0, 'content_tax_status' => 0]);
        $this->model_extension_maza_install_merger->content('special.products', ['content_manufacturer_status' => 0, 'content_tax_status' => 0]);
        $this->model_extension_maza_install_merger->content('products.products', ['content_manufacturer_status' => 0, 'content_tax_status' => 0]);

        $this->model_extension_maza_install_merger->content('product.specification', [
            'content_size' => 'md', 'content_style_head' => 'light', 'content_style_striped' => false,
            'content_style_dark' => false, 'content_style_bordered' => true, 'content_style_borderless' => false,
            'content_style_hover' => false,
        ]);

        $this->model_extension_maza_install_merger->widget('countdown', ['widget_title' => array('1' => '')]);
        $this->model_extension_maza_install_merger->widget('banner', ['widget_alt' => array('1' => '')]);

        // table oc_mz_menu_item
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "mz_menu_item`");
        $field = array_column($query->rows, 'Field');
        
        if(!in_array('customer', $field)){
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "mz_menu_item` ADD COLUMN `customer` TINYINT(1) NOT NULL DEFAULT '0'");
        }
        if(!in_array('customer_group_id', $field)){
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "mz_menu_item` ADD COLUMN `customer_group_id` INT(11) NOT NULL");
        }
    }

    // Version 1.4.0
    private function patch140(): void {
        $this->load->model('extension/maza/opencart');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_admin_core_before_delete_module');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_admin_core_before_delete_layout');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_admin_core_column_left_view');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_admin_core_before_delete_category');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_admin_core_before_delete_manufacturer');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_admin_core_before_delete_product');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_before_header_view');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_before_footer_view');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_before_view');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_product_before_getProducts_model');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_product_before_getTotalProducts_model');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_before_column_left_view');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_before_column_right_view');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_before_content_top_view');
        $this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_core_before_content_bottom_view');

        $this->model_extension_maza_install_merger->widget('html', ['widget_type' => 'html', 'widget_path' => '']);

        $this->model_extension_maza_install_merger->content('category.refine_search', ['content_design' => 'grid']);
        $this->model_extension_maza_install_merger->content('product.image', ['content_video_position' => 'start']);
        $this->model_extension_maza_install_merger->content('product.specification', ['content_design' => 'table', 'content_accordion_auto_close' => '1', 'content_tab_fade_effect' => '1', 'content_pill_orientation' => 'horizontal']);
        $this->model_extension_maza_install_merger->content('product.extra', ['content_sold' => '0']);
        $this->model_extension_maza_install_merger->content('product.button', ['content_stock_status' => '1']);

        $this->model_extension_maza_install_merger->design('menu', function($old_setting){
            foreach($old_setting['design_items'] as $key => $item){
                if(!isset($old_setting['design_items'][$key]['url_target'])){
                    $old_setting['design_items'][$key]['url_target'] = '_self';
                }
            }

            return $old_setting;
        });

        // Order status
        $skins = $this->model_extension_maza_skin->getSkins();
        foreach($skins as $skin){
            $setting = $this->model_extension_maza_skin->getSetting($skin['skin_id'], 'catalog');

            if(isset($setting['catalog_special_sold_order_status'])){
                $setting['catalog_sold_order_statuses'] = $setting['catalog_special_sold_order_status'];
                unset($setting['catalog_special_sold_order_status']);
                $this->model_extension_maza_skin->editSetting($skin['skin_id'], 'catalog', $setting);
            }
        }

        // Filter add input shapes
        $query = $this->db->query("SELECT filter_id, setting FROM " . DB_PREFIX . "mz_filter");
        foreach ($query->rows as $row){
            $setting = json_decode($row['setting'], TRUE);

            if(!isset($setting['input_shape'])){
                $setting['input_shape'] = 'default';

                $this->db->query("UPDATE " . DB_PREFIX . "mz_filter SET `setting` = '" . $this->db->escape(json_encode($setting)) . "' WHERE filter_id = '" . (int)$row['filter_id'] . "'");
            }
        }

        // Fixed merger of update 1.3 for catalog data
        $this->model_extension_maza_install_merger->design('tabs', ['design_hook' => true]);
        $this->model_extension_maza_install_merger->design('accordion', ['design_hook' => true]);
        $this->model_extension_maza_install_merger->widget('faq', ['widget_hook' => true]);
    }

    // Version 1.4.5
    private function patch145(): void {
        $this->model_extension_maza_install_merger->design('alert', ['design_title_size' => '']);

        $skins = $this->model_extension_maza_skin->getSkins();
        foreach($skins as $skin){
            $setting = array_merge(['catalog_checkout_status' => 1], $this->model_extension_maza_skin->getSetting($skin['skin_id'], 'catalog'));
            $this->model_extension_maza_skin->editSetting($skin['skin_id'], 'catalog', $setting);
        }
    }

    // Version 1.4.6
    private function patch146(): void {
        $skins = $this->model_extension_maza_skin->getSkins();
        foreach($skins as $skin){
            $setting = array_merge([
                'catalog_compare_model_status' => 1,
                'catalog_compare_stock_status' => 1,
                'catalog_compare_weight_status' => 1,
                'catalog_compare_dimension_status' => 1,
                'catalog_compare_attribute_status' => 1
            ], $this->model_extension_maza_skin->getSetting($skin['skin_id'], 'catalog'));

            $this->model_extension_maza_skin->editSetting($skin['skin_id'], 'catalog', $setting);
        }
    }

    // Version 1.4.8
    private function patch148(): void {
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product`");
        $field = array_column($query->rows, 'Field');
        if(in_array('mz_video', $field)){
            $query = $this->db->query("SELECT product_id, mz_video FROM `" . DB_PREFIX . "product`");

            foreach($query->rows as $product){
                if ($product['mz_video']) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "mz_product_video` SET product_id = '" . (int)$product['product_id'] . "', url = '" . $this->db->escape($product['mz_video']) . "'");
                }
            }

            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN mz_video");
        }
    }

    // Version 1.5
    private function patch150(): void {
        $this->model_extension_maza_install_merger->content('product.image', ['content_additional_image_status' => '1', 'content_video_status' => '1']);

        $skins = $this->model_extension_maza_skin->getSkins();
        foreach($skins as $skin){
            $setting = array_merge([
                'catalog_image_scale' => '',
                'catalog_image_quality' => 90,
                'catalog_account_download_status' => 1,
                'catalog_account_recurring_status' => 1,
                'catalog_account_reward_status' => 1,
                'catalog_account_return_status' => 1,
                'catalog_account_affiliate_status' => 1
            ], $this->model_extension_maza_skin->getSetting($skin['skin_id'], 'catalog'));

            $this->model_extension_maza_skin->editSetting($skin['skin_id'], 'catalog', $setting);
        }

        // Default value for enable or disable category name in articles listing
        $this->model_extension_maza_install_merger->content('blog_all.articles', ['content_category' => '0']);
        $this->model_extension_maza_install_merger->content('blog_author.articles', ['content_category' => '0']);
        $this->model_extension_maza_install_merger->content('blog_category.articles', ['content_category' => '0']);
        $this->model_extension_maza_install_merger->content('blog_search.articles', ['content_category' => '0']);
        $this->model_extension_maza_install_merger->module('mz_article_listing', ['article_grid_category_status' => '0']);

        // Article Extra
        $this->model_extension_maza_install_merger->content('blog_article.extra', ['content_author' => 1, 'content_timestamp' => 1, 'content_viewed' => 1, 'content_comments' => 1]);

        // Update for linkmanager
        $design_url_struct_func_1 = function($old_setting){
            if (isset($old_setting['design_url_type'])) {
                $old_setting['design_url_link_code'] =  '';

                $map = [
                    'custom' => 'design_url_custom',
                    'route' => 'design_url_route',
                    'popup' => 'design_url_popup',
                    'drawer' => 'design_url_drawer',
                    'collapsible' => 'design_url_collapsible',
                    'category' => 'design_url_category_id',
                    'product' => 'design_url_product_id',
                    'manufacturer' => 'design_url_manufacturer_id',
                    'page_builder' => 'design_url_page_id',
                    'information' => 'design_url_information_id',
                    'system' => 'design_url_system',
                ];

                if ($old_setting['design_url_type'] && !empty($old_setting[$map[$old_setting['design_url_type']]])) {
                    $old_setting['design_url_link_code'] = $old_setting['design_url_type'] . '.' . $old_setting[$map[$old_setting['design_url_type']]];
                }

                unset($old_setting['design_url_type']);
                foreach ($map as $value) {
                    unset($old_setting[$value]);
                }

                return $old_setting;
            }
        };
        $this->model_extension_maza_install_merger->design('link', $design_url_struct_func_1);
        $this->model_extension_maza_install_merger->design('button', $design_url_struct_func_1);
        $this->model_extension_maza_install_merger->design('image', $design_url_struct_func_1);

        $this->model_extension_maza_install_merger->design('carousel', function($old_setting){
            $isChanged = false;
            if (isset($old_setting['design_slides'])) {
                foreach ($old_setting['design_slides'] as $key => $slide) {
                    if (isset($slide['url_type'])) {
                        $old_setting['design_slides'][$key]['url_link_code'] =  '';

                        $map = [
                            'custom' => 'url_custom',
                            'route' => 'url_route',
                            'popup' => 'url_popup',
                            'drawer' => 'url_drawer',
                            'collapsible' => 'url_collapsible',
                            'category' => 'url_category_id',
                            'product' => 'url_product_id',
                            'manufacturer' => 'url_manufacturer_id',
                            'page_builder' => 'url_page_id',
                            'information' => 'url_information_id',
                            'system' => 'url_system',
                        ];

                        if ($slide['url_type'] && !empty($slide[$map[$slide['url_type']]])) {
                            $old_setting['design_slides'][$key]['url_link_code'] = $slide['url_type'] . '.' . $slide[$map[$slide['url_type']]];
                        }

                        unset($old_setting['design_slides'][$key]['url_type']);
                        foreach ($map as $value) {
                            unset($old_setting['design_slides'][$key][$value]);
                        }
                        $isChanged = true;
                    }
                }
            }
            if ($isChanged) {
                return $old_setting;
            }
        });
        $this->model_extension_maza_install_merger->design('dropdown', function($old_setting){
            $isChanged = false;
            if (isset($old_setting['design_items'])) {
                foreach ($old_setting['design_items'] as $key => $item) {
                    if (isset($item['url_type'])) {
                        $old_setting['design_items'][$key]['url_link_code'] =  '';

                        $map = [
                            'custom' => 'url_custom',
                            'route' => 'url_route',
                            'popup' => 'url_popup',
                            'drawer' => 'url_drawer',
                            'collapsible' => 'url_collapsible',
                            'category' => 'url_category_id',
                            'product' => 'url_product_id',
                            'manufacturer' => 'url_manufacturer_id',
                            'page_builder' => 'url_page_id',
                            'information' => 'url_information_id',
                            'system' => 'url_system',
                        ];

                        if ($item['url_type'] && !empty($item[$map[$item['url_type']]])) {
                            $old_setting['design_items'][$key]['url_link_code'] = $item['url_type'] . '.' . $item[$map[$item['url_type']]];
                        }

                        unset($old_setting['design_items'][$key]['url_type']);
                        foreach ($map as $value) {
                            unset($old_setting['design_items'][$key][$value]);
                        }
                        $isChanged = true;
                    }
                }
            }
            if ($isChanged) {
                return $old_setting;
            }
        });
        $this->model_extension_maza_install_merger->design('list', function($old_setting){
            $isChanged = false;
            if (isset($old_setting['design_list'])) {
                foreach ($old_setting['design_list'] as $key => $item) {
                    if (isset($item['url_type'])) {
                        $old_setting['design_list'][$key]['url_link_code'] =  '';

                        $map = [
                            'custom' => 'url_custom',
                            'route' => 'url_route',
                            'popup' => 'url_popup',
                            'drawer' => 'url_drawer',
                            'collapsible' => 'url_collapsible',
                            'category' => 'url_category_id',
                            'product' => 'url_product_id',
                            'manufacturer' => 'url_manufacturer_id',
                            'page_builder' => 'url_page_id',
                            'information' => 'url_information_id',
                            'system' => 'url_system',
                        ];

                        if ($item['url_type'] && !empty($item[$map[$item['url_type']]])) {
                            $old_setting['design_list'][$key]['url_link_code'] = $item['url_type'] . '.' . $item[$map[$item['url_type']]];
                        }

                        unset($old_setting['design_list'][$key]['url_type']);
                        foreach ($map as $value) {
                            unset($old_setting['design_list'][$key][$value]);
                        }
                        $isChanged = true;
                    }
                }
            }
            if ($isChanged) {
                return $old_setting;
            }
        });
        $this->model_extension_maza_install_merger->design('menu', function($old_setting){
            $isChanged = false;

            if (isset($old_setting['design_title_url_type'])) {
                $old_setting['design_title_url_link_code'] =  '';

                $map = [
                    'custom' => 'design_title_url_custom',
                    'route' => 'design_title_url_route',
                    'popup' => 'design_title_url_popup',
                    'drawer' => 'design_title_url_drawer',
                    'collapsible' => 'design_title_url_collapsible',
                    'category' => 'design_title_url_category_id',
                    'product' => 'design_title_url_product_id',
                    'manufacturer' => 'design_title_url_manufacturer_id',
                    'page_builder' => 'design_title_url_page_id',
                    'information' => 'design_title_url_information_id',
                    'system' => 'design_title_url_system',
                ];

                if ($old_setting['design_title_url_type'] && !empty($old_setting[$map[$old_setting['design_title_url_type']]])) {
                    $old_setting['design_title_url_link_code'] = $old_setting['design_title_url_type'] . '.' . $old_setting[$map[$old_setting['design_title_url_type']]];
                }

                unset($old_setting['design_title_url_type']);
                foreach ($map as $value) {
                    unset($old_setting[$value]);
                }

                $isChanged = true;
            }

            if (isset($old_setting['design_items'])) {
                foreach ($old_setting['design_items'] as $key => $item) {
                    if (isset($item['url_type'])) {
                        $old_setting['design_items'][$key]['url_link_code'] =  '';

                        $map = [
                            'custom' => 'url_custom',
                            'route' => 'url_route',
                            'popup' => 'url_popup',
                            'drawer' => 'url_drawer',
                            'collapsible' => 'url_collapsible',
                            'category' => 'url_category_id',
                            'product' => 'url_product_id',
                            'manufacturer' => 'url_manufacturer_id',
                            'page_builder' => 'url_page_id',
                            'information' => 'url_information_id',
                            'system' => 'url_system',
                        ];

                        if ($item['url_type'] && !empty($item[$map[$item['url_type']]])) {
                            $old_setting['design_items'][$key]['url_link_code'] = $item['url_type'] . '.' . $item[$map[$item['url_type']]];
                        }

                        unset($old_setting['design_items'][$key]['url_type']);
                        foreach ($map as $value) {
                            unset($old_setting['design_items'][$key][$value]);
                        }
                        $isChanged = true;
                    }
                }
            }
            if ($isChanged) {
                return $old_setting;
            }
        });

        // Widget
        $this->model_extension_maza_install_merger->widget('banner', function($old_setting){
            if (isset($old_setting['widget_url_type'])) {
                $old_setting['widget_url_link_code'] =  '';

                $map = [
                    'custom' => 'widget_url_custom',
                    'route' => 'widget_url_route',
                    'popup' => 'widget_url_popup',
                    'drawer' => 'widget_url_drawer',
                    'collapsible' => 'widget_url_collapsible',
                    'category' => 'widget_url_category_id',
                    'product' => 'widget_url_product_id',
                    'manufacturer' => 'widget_url_manufacturer_id',
                    'page_builder' => 'widget_url_page_id',
                    'information' => 'widget_url_information_id',
                    'system' => 'widget_url_system',
                ];

                if ($old_setting['widget_url_type'] && !empty($old_setting[$map[$old_setting['widget_url_type']]])) {
                    $old_setting['widget_url_link_code'] = $old_setting['widget_url_type'] . '.' . $old_setting[$map[$old_setting['widget_url_type']]];
                }

                unset($old_setting['widget_url_type']);
                foreach ($map as $value) {
                    unset($old_setting[$value]);
                }

                return $old_setting;
            }
        });
        $this->model_extension_maza_install_merger->widget('navbar', function($old_setting){
            if (isset($old_setting['widget_brand_url_type'])) {
                $old_setting['widget_brand_url_link_code'] =  '';

                $map = [
                    'custom' => 'widget_brand_url_custom',
                    'route' => 'widget_brand_url_route',
                    'popup' => 'widget_brand_url_popup',
                    'drawer' => 'widget_brand_url_drawer',
                    'collapsible' => 'widget_brand_url_collapsible',
                    'category' => 'widget_brand_url_category_id',
                    'product' => 'widget_brand_url_product_id',
                    'manufacturer' => 'widget_brand_url_manufacturer_id',
                    'page_builder' => 'widget_brand_url_page_id',
                    'information' => 'widget_brand_url_information_id',
                    'system' => 'widget_brand_url_system',
                ];

                if ($old_setting['widget_brand_url_type'] && !empty($old_setting[$map[$old_setting['widget_brand_url_type']]])) {
                    $old_setting['widget_brand_url_link_code'] = $old_setting['widget_brand_url_type'] . '.' . $old_setting[$map[$old_setting['widget_brand_url_type']]];
                }

                unset($old_setting['widget_brand_url_type']);
                foreach ($map as $value) {
                    unset($old_setting[$value]);
                }

                return $old_setting;
            }
        });

        // Module
        $module_url_struct_func_1 = function($old_setting){
            if (isset($old_setting['banner_link_type'])) {
                $old_setting['banner_link_code'] =  '';

                $map = [
                    'custom' => 'banner_link_custom',
                    'route' => 'banner_link_route',
                    'popup' => 'banner_link_popup',
                    'drawer' => 'banner_link_drawer',
                    'collapsible' => 'banner_link_collapsible',
                    'category' => 'banner_link_category_id',
                    'product' => 'banner_link_product_id',
                    'manufacturer' => 'banner_link_manufacturer_id',
                    'page_builder' => 'banner_link_page_id',
                    'information' => 'banner_link_information_id',
                    'system' => 'banner_link_system',
                ];

                if ($old_setting['banner_link_type'] && !empty($old_setting[$map[$old_setting['banner_link_type']]])) {
                    $old_setting['banner_link_code'] = $old_setting['banner_link_type'] . '.' . $old_setting[$map[$old_setting['banner_link_type']]];
                }

                unset($old_setting['banner_link_type']);
                foreach ($map as $value) {
                    unset($old_setting[$value]);
                }

                return $old_setting;
            }
        };
        $this->model_extension_maza_install_merger->module('mz_product_listing', $module_url_struct_func_1);
        $this->model_extension_maza_install_merger->module('mz_article_listing', $module_url_struct_func_1);

        $this->model_extension_maza_install_merger->module('mz_slider', function($old_setting){
            $isChanged = false;
            if (isset($old_setting['slides'])) {
                foreach ($old_setting['slides'] as $key => $item) {
                    if (isset($item['url_type'])) {
                        $old_setting['slides'][$key]['url_link_code'] =  '';

                        $map = [
                            'custom' => 'url_custom',
                            'route' => 'url_route',
                            'popup' => 'url_popup',
                            'drawer' => 'url_drawer',
                            'collapsible' => 'url_collapsible',
                            'category' => 'url_category_id',
                            'product' => 'url_product_id',
                            'manufacturer' => 'url_manufacturer_id',
                            'page_builder' => 'url_page_id',
                            'information' => 'url_information_id',
                            'system' => 'url_system',
                        ];

                        if ($item['url_type'] && !empty($item[$map[$item['url_type']]])) {
                            $old_setting['slides'][$key]['url_link_code'] = $item['url_type'] . '.' . $item[$map[$item['url_type']]];
                        }

                        unset($old_setting['slides'][$key]['url_type']);
                        foreach ($map as $value) {
                            unset($old_setting['slides'][$key][$value]);
                        }
                        $isChanged = true;
                    }
                }
            }
            if ($isChanged) {
                return $old_setting;
            }
        });

        // Menu item
        $this->load->model('extension/maza/menu');

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_menu_item`");
        foreach ($query->rows as $item) {
            $setting = json_decode($item['setting'], true);

            if (isset($setting['link_type'])) {
                $setting['link_code'] =  '';

                $map = [
                    'custom' => 'link_custom',
                    'route' => 'link_route',
                    'popup' => 'link_popup',
                    'drawer' => 'link_drawer',
                    'collapsible' => 'link_collapsible',
                    'category' => 'link_category_id',
                    'product' => 'link_product_id',
                    'manufacturer' => 'link_manufacturer_id',
                    'page_builder' => 'link_page_id',
                    'information' => 'link_information_id',
                    'system' => 'link_system',
                ];

                if ($setting['link_type'] && !empty($setting[$map[$setting['link_type']]])) {
                    $setting['link_code'] = $setting['link_type'] . '.' . $setting[$map[$setting['link_type']]];
                }

                unset($setting['link_type']);
                foreach ($map as $value) {
                    unset($setting[$value]);
                }

                $item['setting'] = $setting;

                $this->model_extension_maza_menu->editItem($item['item_id'], $item);
            }
        }
    }

    // Version 1.5.1
    private function patch151(): void {
        $this->model_extension_maza_install_merger->design('accordion', ['design_collapsed' => 0]);
    }
}