<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazalayoutBuilder extends model {
        /**
         * Get layout entries
         * @param String $group entry group
         * @param Int $group_owner entry group owner
         * @param Int $parent_entry_id parent entry id
         * @return Array $entries
         */
        function getLayoutEntries($group, $group_owner, $parent_entry_id = 0) {
                $entries = array();
                
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_layout_entry WHERE `group` = '" . $this->db->escape($group) . "' AND group_owner = '" . (int)$group_owner . "' AND parent_entry_id = '" . (int)$parent_entry_id . "' AND `skin_id` = '" . (int)$this->mz_skin_config->get('skin_id') . "' ORDER BY entry_id ASC");
                
                foreach ($query->rows as $entry) {
                    $setting = json_decode($entry['setting'], TRUE);
                    
                    $entry_info = array(
                        'entry_id'      => $entry['entry_id'],
                        'type'          => $entry['type'],
                        'code'          => $entry['code'],
                        'device_hidden' => empty($setting['device_hidden'])?array():$setting['device_hidden'],
                        'device_order'  => empty($setting['device_order'])?array():$setting['device_order'],
                        'child_entry'   => $this->getLayoutEntries($group, $group_owner, $entry['entry_id'])
                    );
                    
                    $entry_info['setting'] = array();
                    parse_str(html_entity_decode($setting['setting']), $entry_info['setting']);
                    
                    // Column
                    if($entry['type'] == 'col'){
                        $entry_info['device_size']      = isset($setting['device_size'])?$setting['device_size']:array();
                    }
                    
                    // Widget
                    if($entry['type'] == 'widget'){
                        $entry_info['widget_data'] = array();
                        parse_str(html_entity_decode($setting['widget_data']), $entry_info['widget_data']);
                    }
                    
                    // Design
                    if($entry['type'] == 'design'){
                        $entry_info['design_data'] = array();
                        parse_str(html_entity_decode($setting['design_data']), $entry_info['design_data']);
                    }
                    
                    // Content
                    if($entry['type'] == 'content'){
                        $entry_info['content_data'] = array();
                        parse_str(html_entity_decode($setting['content_data']), $entry_info['content_data']);
                    }
                    
                    $entries[] = $entry_info;
                }
                
                return $entries;
        }
        
        
        /**
         * Get layout detail
         * @param Int $layout_id id of layout
         * @return Array detail
         */
        public function getLayout($layout_id){
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "layout` WHERE layout_id = '" . (int)$layout_id . "' LIMIT 1");
                
                if($query->row){
                    $query->row['mz_override_skin'] = json_decode($query->row['mz_override_skin'], true);
                }
                
                return $query->row;
        }
        
        /**
         * Get entry setting
         * @param Int $entry_id entry id
         * @return Array setting
         */
        public function getEntrySetting($entry_id){
                $data = array();
                
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_layout_entry` WHERE `entry_id` = '" . (int)$entry_id . "'");
                
                if($query->row){
                    $setting = json_decode($query->row['setting'], TRUE);
                    
                    switch($query->row['type']){
                        case 'widget': $entry_data = $setting['widget_data'];
                            break;
                        case 'design': $entry_data = $setting['design_data'];
                            break;
                        case 'content': $entry_data = $setting['content_data'];
                            break;
                        default: $entry_data = array();
                            break;
                    }
                    
                    parse_str(html_entity_decode($entry_data), $data);
                }
                
                return $data;
        }
}
