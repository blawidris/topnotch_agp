<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventLayoutBuilder extends Controller {
        /**
         * Event type: View
         * Default layout
         * For content left, right, top, bottom
         * @param String $route route of view
         * @param Array $data variables of view
         * @return Null
         */
        public function default($route, &$data) {
            if($this->config->get('mz_layout_type') === 'default'){
                $content = false;
            
                switch ($route) {
                    case 'common/column_left': // Column left
                        $content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_column_left', 'group_owner' => $this->config->get('mz_layout_id')]);
                        break;
                    case 'common/column_right': // Column right
                        $content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_column_right', 'group_owner' => $this->config->get('mz_layout_id')]);
                        break;
                    case 'common/content_top': // content top
                        $content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_content_top', 'group_owner' => $this->config->get('mz_layout_id')]);
                        break;
                    case 'common/content_bottom': // content bottom
                        $content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_content_bottom', 'group_owner' => $this->config->get('mz_layout_id')]);
                        break;
                }

                if($content){
                    $data['modules'][] = $content;
                }
            }
        }
        
}
