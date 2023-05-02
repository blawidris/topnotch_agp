<?php
class ModelExtensionMazaCatalogProduct extends Model {
	public function editProduct(int $product_id, array $data): void {
        $this->db->query("DELETE FROM " . DB_PREFIX . "mz_product_video WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_video'])) {
			foreach ($data['product_video'] as $product_video) {
                if ($product_video['url']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "mz_product_video SET product_id = '" . (int)$product_id . "', url = '" . $this->db->escape($product_video['url']) . "', sort_order = '" . (int)$product_video['sort_order'] . "'");
                }
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', mz_quantity = '" . (int)$product_special['mz_quantity'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}
	}

	public function getTotalOrderQuantityOfProduct(int $product_id, array $data = array()): int {
        $sql = "SELECT DISTINCT SUM(op.quantity) as total FROM " . DB_PREFIX . "order_product op";
        
        if($data){
            $sql .= " LEFT JOIN " . DB_PREFIX . "order o ON op.order_id = o.order_id";
        }
        
        $sql .= " WHERE op.product_id = '" . (int)$product_id . "'";
        
        if(isset($data['store_id'])){
            $sql .= " AND o.store_id = '" . (int)$data['store_id'] . "'";
        }
        
        if(isset($data['customer_group_id'])){
            $sql .= " AND o.customer_group_id = '" . (int)$data['customer_group_id'] . "'";
        }
        
        if(isset($data['order_status'])){
            $sql .= " AND o.order_status_id IN (" . implode(',', array_map('intval', $data['order_status'])) . ")";
        }
        
        if(isset($data['date_start'])){
            $sql .= " AND (o.date_added > '" . $this->db->escape($data['date_start']) . "')";
        }
        
        if(isset($data['date_end'])){
            $sql .= " AND (o.date_added < '" . $this->db->escape($data['date_end']) . "')";
        }
        
        $sql .= " GROUP BY op.product_id";
        
        $query = $this->db->query($sql);
        
        return $query->row['total']??0;
    }

    public function getProductVideos(int $product_id): array {
        $query = $this->db->query("SELECT url, sort_order FROM " . DB_PREFIX . "mz_product_video WHERE product_id = '" . (int)$product_id . "'");

        return $query->rows;
    }
}
