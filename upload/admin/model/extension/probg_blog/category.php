<?php
class ModelExtensionProbgBlogCategory extends Model {
    public function addCategory($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "probg_blog_category` SET `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (int)$data['status'] . "', `date_added` = NOW(), `date_modified` = NOW()");
        $category_id = $this->db->getLastId();
        $this->saveDescriptions($category_id, $data);
        $this->saveStores($category_id, $data);
        $this->saveLayouts($category_id, $data);
        $this->saveSeoUrls($category_id, $data);
        $this->clearCache();
        return $category_id;
    }

    public function editCategory($category_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "probg_blog_category` SET `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (int)$data['status'] . "', `date_modified` = NOW() WHERE `category_id` = '" . (int)$category_id . "'");
        $this->saveDescriptions($category_id, $data);
        $this->saveStores($category_id, $data);
        $this->saveLayouts($category_id, $data);
        $this->saveSeoUrls($category_id, $data);
        $this->clearCache();
    }

    public function deleteCategory($category_id) {
        $category_id = (int)$category_id;
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_category` WHERE category_id='" . $category_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_category_description` WHERE category_id='" . $category_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_category_to_store` WHERE category_id='" . $category_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_category_to_layout` WHERE category_id='" . $category_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query='probg_blog_category_id=" . $category_id . "'");
        $this->clearCache();
    }

    public function getCategory($category_id) {
        return $this->db->query("SELECT * FROM `" . DB_PREFIX . "probg_blog_category` WHERE category_id='" . (int)$category_id . "'")->row;
    }

    public function getCategoryDescriptions($category_id) {
        $data = array();
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "probg_blog_category_description` WHERE category_id='" . (int)$category_id . "'");
        foreach ($query->rows as $row) {
            $data[(int)$row['language_id']] = array(
                'title' => $row['title'],
                'description' => $row['description'],
                'tags' => isset($row['tags']) ? $row['tags'] : '',
                'meta_title' => $row['meta_title'],
                'meta_description' => $row['meta_description'],
                'meta_keyword' => $row['meta_keyword'],
                'seo_keyword' => ''
            );
        }

        $seo = $this->db->query("SELECT language_id, keyword FROM `" . DB_PREFIX . "seo_url` WHERE query='probg_blog_category_id=" . (int)$category_id . "' ORDER BY store_id ASC, seo_url_id ASC");
        foreach ($seo->rows as $row) {
            $language_id = (int)$row['language_id'];
            if (!isset($data[$language_id])) {
                $data[$language_id] = array('title'=>'','description'=>'','tags'=>'','meta_title'=>'','meta_description'=>'','meta_keyword'=>'','seo_keyword'=>'');
            }
            if ($data[$language_id]['seo_keyword'] === '') {
                $data[$language_id]['seo_keyword'] = $row['keyword'];
            }
        }
        return $data;
    }

    public function getCategoryStores($category_id) {
        $stores = array();
        $query = $this->db->query("SELECT store_id FROM `" . DB_PREFIX . "probg_blog_category_to_store` WHERE category_id='" . (int)$category_id . "'");
        foreach ($query->rows as $row) $stores[] = (int)$row['store_id'];
        return $stores;
    }

    public function getCategoryLayouts($category_id) {
        $layouts = array();
        $query = $this->db->query("SELECT store_id, layout_id FROM `" . DB_PREFIX . "probg_blog_category_to_layout` WHERE category_id='" . (int)$category_id . "'");
        foreach ($query->rows as $row) $layouts[(int)$row['store_id']] = (int)$row['layout_id'];
        return $layouts;
    }

    public function getCategoryLayoutId($category_id, $store_id) {
        $query = $this->db->query("SELECT layout_id FROM `" . DB_PREFIX . "probg_blog_category_to_layout` WHERE category_id='" . (int)$category_id . "' AND store_id='" . (int)$store_id . "'");
        return $query->num_rows ? (int)$query->row['layout_id'] : 0;
    }

    public function getCategories($data=array()) {
        $sql = "SELECT c.*, cd.title FROM `" . DB_PREFIX . "probg_blog_category` c LEFT JOIN `" . DB_PREFIX . "probg_blog_category_description` cd ON(c.category_id=cd.category_id) WHERE cd.language_id='" . (int)$this->config->get('config_language_id') . "'";
        if (!empty($data['filter_title'])) $sql .= " AND cd.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        if (isset($data['filter_status']) && $data['filter_status'] !== '') $sql .= " AND c.status='" . (int)$data['filter_status'] . "'";
        $sorts = array('cd.title','c.sort_order','c.status','c.date_added','c.date_modified');
        $sql .= " ORDER BY " . ((isset($data['sort']) && in_array($data['sort'],$sorts,true)) ? $data['sort'] : 'c.sort_order') . ((isset($data['order']) && $data['order']==='DESC') ? ' DESC' : ' ASC') . ", cd.title ASC";
        if (isset($data['start']) || isset($data['limit'])) {
            $start = max(0, isset($data['start']) ? (int)$data['start'] : 0);
            $limit = max(1, isset($data['limit']) ? (int)$data['limit'] : 20);
            $sql .= " LIMIT " . $start . "," . $limit;
        }
        return $this->db->query($sql)->rows;
    }

    public function getTotalCategories($data=array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "probg_blog_category` c LEFT JOIN `" . DB_PREFIX . "probg_blog_category_description` cd ON(c.category_id=cd.category_id) WHERE cd.language_id='" . (int)$this->config->get('config_language_id') . "'";
        if (!empty($data['filter_title'])) $sql .= " AND cd.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        if (isset($data['filter_status']) && $data['filter_status'] !== '') $sql .= " AND c.status='" . (int)$data['filter_status'] . "'";
        return (int)$this->db->query($sql)->row['total'];
    }

    public function getTotalArticlesByCategoryId($category_id) {
        return (int)$this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "probg_blog_article` WHERE category_id='" . (int)$category_id . "'")->row['total'];
    }

    public function getSeoUrlByKeyword($keyword, $language_id, $category_id=0) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE language_id='" . (int)$language_id . "' AND keyword='" . $this->db->escape($keyword) . "'";
        if ($category_id) $sql .= " AND query!='probg_blog_category_id=" . (int)$category_id . "'";
        return $this->db->query($sql)->row;
    }

    private function saveDescriptions($category_id, $data) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_category_description` WHERE category_id='" . (int)$category_id . "'");
        if (empty($data['category_description']) || !is_array($data['category_description'])) return;
        foreach ($data['category_description'] as $language_id => $value) {
            $title = $this->value($value,'title');
            $meta_title = trim($this->value($value,'meta_title')) !== '' ? $this->value($value,'meta_title') : $title;
            $this->db->query("INSERT INTO `" . DB_PREFIX . "probg_blog_category_description` SET category_id='" . (int)$category_id . "', language_id='" . (int)$language_id . "', title='" . $this->db->escape($title) . "', description='" . $this->db->escape($this->value($value,'description')) . "', tags='" . $this->db->escape($this->value($value,'tags')) . "', meta_title='" . $this->db->escape($meta_title) . "', meta_description='" . $this->db->escape($this->value($value,'meta_description')) . "', meta_keyword='" . $this->db->escape($this->value($value,'meta_keyword')) . "'");
        }
    }

    private function saveStores($category_id, $data) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_category_to_store` WHERE category_id='" . (int)$category_id . "'");
        $stores = isset($data['category_store']) ? (array)$data['category_store'] : array(0);
        if (!$stores) $stores = array(0);
        foreach (array_unique(array_map('intval',$stores)) as $store_id) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "probg_blog_category_to_store` SET category_id='" . (int)$category_id . "', store_id='" . (int)$store_id . "'");
        }
    }

    private function saveLayouts($category_id, $data) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_category_to_layout` WHERE category_id='" . (int)$category_id . "'");
        if (empty($data['category_layout']) || !is_array($data['category_layout'])) return;
        foreach ($data['category_layout'] as $store_id => $layout_id) {
            if ((int)$layout_id > 0) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "probg_blog_category_to_layout` SET category_id='" . (int)$category_id . "', store_id='" . (int)$store_id . "', layout_id='" . (int)$layout_id . "'");
            }
        }
    }

    private function saveSeoUrls($category_id, $data) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query='probg_blog_category_id=" . (int)$category_id . "'");
        if (empty($data['category_description']) || !is_array($data['category_description'])) return;
        require_once(DIR_SYSTEM . 'library/probg_blog_seo.php');
        $seo = new ProbgBlogSeo($this->db);
        $query_key = 'probg_blog_category_id=' . (int)$category_id;
        $stores = isset($data['category_store']) ? (array)$data['category_store'] : array(0);
        if (!$stores) $stores = array(0);
        $stores = array_unique(array_map('intval',$stores));
        foreach ($data['category_description'] as $language_id => $value) {
            $keyword = isset($value['seo_keyword']) ? trim($value['seo_keyword']) : '';
            if ($keyword === '') $keyword = $seo->slugify($this->value($value,'title'));
            foreach ($stores as $store_id) {
                $unique = $seo->uniqueKeyword($keyword, $language_id, $query_key, $store_id);
                $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id='" . (int)$store_id . "', language_id='" . (int)$language_id . "', query='" . $this->db->escape($query_key) . "', keyword='" . $this->db->escape($unique) . "'");
            }
        }
    }

    private function clearCache() { $this->cache->delete('probg_blog'); }
    private function value($data,$key) { return isset($data[$key]) ? $data[$key] : ''; }
}
