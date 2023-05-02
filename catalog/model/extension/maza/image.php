<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaImage extends model {
        /**
         * Create transparent image of given size
         * @param Int $width width of image
         * @param Int $height height of image
         * @return String image url
         */
        public function transparent($width, $height){
            $transparent = 'cache/transparent-' . (int)$width . 'x' . (int)$height . '.png';
            
            // Create transparent image if not exist
            if(!is_file(DIR_IMAGE . $transparent)){
                $img = imagecreatetruecolor($width, $height);
                imagesavealpha($img, true);
                imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
                imagepng($img, DIR_IMAGE . $transparent);
            }
            
            if ($this->request->server['HTTPS']) {
                    return $this->config->get('config_ssl') . 'image/' . $transparent;
            } else {
                    return $this->config->get('config_url') . 'image/' . $transparent;
            }
        }
    
        /**
         * Return estimated width and height of image
         * @param String $filename image file path
         * @param String $width width of image if available
         * @param String $height height of image if available
         * @return Array image size
         */
        public function getEstimatedSize($filename, $width = null, $height = null){
            if(($width && $height) || !is_file(DIR_IMAGE . $filename)){
                return [$width?:$height, $height?:$width];
            }
            
            // Get image width and height
            list($org_width, $org_height) = getimagesize(DIR_IMAGE . $filename);
            
            if($width && !$height){
                return [$width, round($width * ($org_height/$org_width))];
            } elseif(!$width && $height){
                return [round($height * ($org_width/$org_height)), $height];
            } else {
                return [$org_width, $org_height];
            }
        }
        
        /**
         * Get HTML srcset value for image
         * @param Array $srcset_width image width per breakpoint
         * @param String $filename image file
         * @param Int $default_width default width of image
         * @param Int $default_height default height of image
         * @return String srcset value
         */
        public function getSrcSet($srcset_width, $filename, $default_width, $default_height){
            $srcset = array();
            
            foreach($srcset_width as $width){
                if($width){
                    $height = $width * ($default_height/$default_width);
                    $image = $this->model_tool_image->resize($filename, $width, $height);

                    $srcset[] = "$image " . round($width) . "w";
                }
            }

            if($srcset){
                $srcset[] = "{$this->model_tool_image->resize($filename, $default_width, $default_height)} " . round($default_width) . "w";

                return implode(',', array_unique($srcset));
            }
        }
        
        /**
         * Get value of HTML attribure size for srcset
         * @param Array $srcset array of image width per breakpoint
         * @param Int $default_width default image width
         * @return String srcset size
         */
        public function getSrcSetSize($srcset, $default_width){
            $srcset_sizes = array();
            
            foreach($srcset as $breakpoint_code => $width){
                if(!$width) continue;
                
                if(!empty($this->mz_skin_config->get('style_breakpoints')[$breakpoint_code])){
                    $srcset_sizes[] = "(min-width: {$this->mz_skin_config->get('style_breakpoints')[$breakpoint_code]}px) " . round($width) . "px";
                } else {
                    $srcset_sizes[] = round($width) . "px";
                }
            }
            
            if($srcset_sizes){
                array_unshift($srcset_sizes, "(min-width: {$this->mz_skin_config->get('style_breakpoints')['xl']}px) " . round($default_width) . "px");
                return implode(',', $srcset_sizes);
            }
        }
}
