<?php
class ModelExtensionMazaPage extends Model {
	public function getPage($page_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mz_page p LEFT JOIN " . DB_PREFIX . "mz_page_description pd ON (p.page_id = pd.page_id) LEFT JOIN " . DB_PREFIX . "mz_page_to_store p2s ON (p.page_id = p2s.page_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.page_id = '" . (int)$page_id . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.status = '1'");

		return $query->row;
	}
}