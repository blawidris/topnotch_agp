<?php
class ModelExtensionMazaBlogReport extends Model {
	public function getArticlesViewed($data = array()) {
		$sql = "SELECT ad.name, a.viewed FROM " . DB_PREFIX . "mz_blog_article a LEFT JOIN " . DB_PREFIX . "mz_blog_article_description ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.viewed > 0";
                
                $sort_data = array(
			'ad.name',
                        'a.viewed'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.viewed";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalArticleViews() {
		$query = $this->db->query("SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "mz_blog_article");

		return $query->row['total'];
	}

	public function getTotalArticlesViewed() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mz_blog_article WHERE viewed > 0");

		return $query->row['total'];
	}

	public function reset() {
		$this->db->query("UPDATE " . DB_PREFIX . "mz_blog_article SET viewed = '0'");
	}
}
