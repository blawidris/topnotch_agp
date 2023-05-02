<?php
class ControllerExtensionMazaStartupContent extends Controller {
    public function index(): Action {
		// Create tempoaray table for Applicable tax on user
		$tax_class = $this->tax->mz_getTaxRates();
		
		$sql_values['p'] = $sql_values['f'] = array();
		
		foreach($tax_class as $tax_class_id => $tax_rates){
			foreach($tax_rates as $tax_rate){
				if($tax_rate['type'] == 'P'){
					$sql_values['p'][] = "('" . (int)$tax_class_id . "', '" . (int)$tax_rate['tax_rate_id'] . "', '" . (float)$tax_rate['rate'] . "', '" . (int)$tax_rate['priority'] . "')";
				} else {
					$sql_values['f'][] = "('" . (int)$tax_class_id . "', '" . (int)$tax_rate['tax_rate_id'] . "', '" . (float)$tax_rate['rate'] . "', '" . (int)$tax_rate['priority'] . "')";
				}
			}
		}
		
		$this->db->query(
		"CREATE TEMPORARY TABLE " . DB_PREFIX . "mz_user_ptax_rates (
			`tax_class_id` int(11) NOT NULL,
			`tax_rate_id` int(11) NOT NULL,
			`rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`priority` int(5) NOT NULL DEFAULT '1'
		)");
		
		$this->db->query(
		"CREATE TEMPORARY TABLE " . DB_PREFIX . "mz_user_ftax_rates (
			`tax_class_id` int(11) NOT NULL,
			`tax_rate_id` int(11) NOT NULL,
			`rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`priority` int(5) NOT NULL DEFAULT '1'
		)");
		
		if($sql_values['p']){
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_user_ptax_rates VALUES " . implode(',', $sql_values['p']));
		}
		
		if($sql_values['f']){
			$this->db->query("INSERT INTO " . DB_PREFIX . "mz_user_ftax_rates VALUES " . implode(',', $sql_values['f']));
		}

		// Find category herency and set correct path
		$this->load->model('extension/maza/catalog/category');
		$this->load->model('extension/maza/blog/category');

		$category_id = 0;
		if(isset($this->request->get['path']) && (strpos($this->mz_document->getRoute(), 'product/') === 0 || strpos($this->mz_document->getRoute(), 'extension/maza/blog/') === 0)){
			$parts = explode('_', (string)$this->request->get['path']);
			$category_id = (int)array_pop($parts);    
		}

		// Product category
		if($this->mz_document->isRoute('product/product') && isset($this->request->get['product_id'])){
			if(!$category_id && isset($this->request->get['category_id'])){
				$category_id = $this->request->get['category_id'];
			}
			if(!$category_id || !$this->model_extension_maza_catalog_product->validateCategory($this->request->get['product_id'], $category_id)){
				$category_id = $this->model_extension_maza_catalog_category->getProductCategory($this->request->get['product_id']);
			}
		}

		if($category_id && strpos($this->mz_document->getRoute(), 'product/') === 0){
			$this->request->get['path'] = implode('_', array_column($this->model_extension_maza_catalog_category->getCategoryPath($category_id), 'path_id'));
		}

		// Blog category
		if($this->mz_document->isRoute('extension/maza/blog/article') && isset($this->request->get['article_id'])){
			$this->load->model('extension/maza/blog/article');

			if(!$category_id && isset($this->request->get['category_id'])){
				$category_id = $this->request->get['category_id'];
			}
			if(!$category_id || !$this->model_extension_maza_blog_article->validateCategory($this->request->get['article_id'], $category_id)){
				$category_id = $this->model_extension_maza_blog_category->getArticleCategory($this->request->get['article_id']);
			}
		}

		if($category_id && strpos($this->mz_document->getRoute(), 'extension/maza/blog/') === 0){
			$this->request->get['path'] = implode('_', array_column($this->model_extension_maza_blog_category->getCategoryPath($category_id), 'path_id'));
		}

		// Load Catalog data 
		$this->load->controller('extension/maza/hooks/data');

		return new Action($this->config->get('action_router'));
    }
}
