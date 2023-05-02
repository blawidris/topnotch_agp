<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventCommon extends Controller {
    
        public function beforeView($route, &$data) {
                $data['mz_skin_code']       = $this->mz_skin_config->get('skin_code');
                $data['mz_header_code']     = $this->mz_skin_config->get('skin_header_code');
                $data['mz_footer_code']     = $this->mz_skin_config->get('skin_footer_code');
                
                $data['mz_oc_flag']         = 3; // OC 3
                
                if (version_compare(VERSION, '3.0.0.0') < 0){
                    $data['mz_oc_flag']     = 2; // OC 2
                }
        }

        /**
         * Add placeholder image to missing image src
         */
        public function resize(string $route, array $param){
                list($filename, $width, $height) = $param;

                if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return $filename?"https://dummyimage.com/{$width}x$height/dddddd/787878.png":false;
		}
        }
}
