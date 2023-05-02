<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventCommonProduct extends Controller {
    
    /**
     * overwrite default getProducts method
     * @param String $route model route
     * @param Array $param parameter of method
     */
    public function getProducts(string $route, array $param): array {
        if(empty($param[0])){
            return $this->model_extension_maza_catalog_product->getProducts();
        } else {
            return $this->model_extension_maza_catalog_product->getProducts($param[0]);
        }
    }
    
    /**
     * overwrite default getTotalProducts method
     * @param String $route model route
     * @param Array $param parameter of method
     */
    public function getTotalProducts(string $route, array $param): int {
        if(empty($param[0])){
            return $this->model_extension_maza_catalog_product->getTotalProducts();
        } else {
            return $this->model_extension_maza_catalog_product->getTotalProducts($param[0]);
        }
    }
}
