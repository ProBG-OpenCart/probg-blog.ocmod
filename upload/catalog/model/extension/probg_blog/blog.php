<?php
class ModelExtensionProbgBlogBlog extends Model {
    private $store_table_exists = null;
    private $layout_table_exists = null;

    public function getCategories() {
        $key = $this->key('categories');
        $cached = $this->getCache($key);
        if ($cached !== false) return $cached;

        $language_id = (int)$this->config->get('config_language_id');
        $store_join = $this->categoryStoreJoin('c');
        $sql = "SELECT c.*, cd.title, cd.description, cd.meta_title, cd.meta_description, cd.meta_keyword, (SELECT COUNT(*) FROM `" . DB_PREFIX . "probg_blog_article` a INNER JOIN `" . DB_PREFIX . "probg_blog_article_description` ad ON(ad.article_id=a.article_id AND ad.language_id='" . $language_id . "') WHERE a.category_id=c.category_id AND a.status='1') AS article_count FROM `" . DB_PREFIX . "probg_blog_category` c INNER JOIN `" . DB_PREFIX . "probg_blog_category_description` cd ON(cd.category_id=c.category_id AND cd.language_id='" . $language_id . "')" . $store_join . " WHERE c.status='1' ORDER BY c.sort_order ASC, cd.title ASC";
        $rows = $this->db->query($sql)->rows;
        $this->setCache($key, $rows);
        return $rows;
    }

    public function getCategory($id) {
        $key = $this->key('category.' . (int)$id);
        $cached = $this->getCache($key);
        if ($cached !== false) return $cached;

        $language_id = (int)$this->config->get('config_language_id');
        $store_join = $this->categoryStoreJoin('c');
        $row = $this->db->query("SELECT c.*, cd.title, cd.description, cd.meta_title, cd.meta_description, cd.meta_keyword FROM `" . DB_PREFIX . "probg_blog_category` c INNER JOIN `" . DB_PREFIX . "probg_blog_category_description` cd ON(cd.category_id=c.category_id AND cd.language_id='" . $language_id . "')" . $store_join . " WHERE c.category_id='" . (int)$id . "' AND c.status='1'")->row;
        $this->setCache($key, $row);
        return $row;
    }

    public function getArticles($data=array()) {
        $key = $this->key('articles.' . sha1(json_encode($data)));
        $cached = $this->getCache($key);
        if ($cached !== false) return $cached;

        $language_id = (int)$this->config->get('config_language_id');
        $store_join = $this->categoryStoreJoin('c');
        $sql = "SELECT a.*, ad.title, ad.short_description, ad.description, ad.meta_title, ad.meta_description, ad.meta_keyword, cd.title AS category_title FROM `" . DB_PREFIX . "probg_blog_article` a INNER JOIN `" . DB_PREFIX . "probg_blog_article_description` ad ON(ad.article_id=a.article_id AND ad.language_id='" . $language_id . "') INNER JOIN `" . DB_PREFIX . "probg_blog_category` c ON(c.category_id=a.category_id AND c.status='1')" . $store_join . " INNER JOIN `" . DB_PREFIX . "probg_blog_category_description` cd ON(cd.category_id=a.category_id AND cd.language_id='" . $language_id . "') WHERE a.status='1'";
        if (!empty($data['category_id'])) $sql .= " AND a.category_id='" . (int)$data['category_id'] . "'";
        $sort = isset($data['sort']) ? $data['sort'] : $this->config->get('module_probg_blog_sort');
        $sql .= ($sort === 'sort_order') ? " ORDER BY a.sort_order ASC, a.date_added DESC, a.article_id DESC" : " ORDER BY a.date_added DESC, a.article_id DESC";
        $start = max(0, isset($data['start']) ? (int)$data['start'] : 0);
        $limit = max(1, isset($data['limit']) ? (int)$data['limit'] : (int)$this->config->get('module_probg_blog_limit'));
        $sql .= " LIMIT " . $start . "," . $limit;
        $rows = $this->db->query($sql)->rows;
        $this->setCache($key, $rows);
        return $rows;
    }

    public function getTotalArticles($category_id=0) {
        $key = $this->key('total.' . (int)$category_id);
        $cached = $this->getCache($key);
        if ($cached !== false) return (int)$cached;

        $language_id = (int)$this->config->get('config_language_id');
        $store_join = $this->categoryStoreJoin('c');
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "probg_blog_article` a INNER JOIN `" . DB_PREFIX . "probg_blog_article_description` ad ON(ad.article_id=a.article_id AND ad.language_id='" . $language_id . "') INNER JOIN `" . DB_PREFIX . "probg_blog_category` c ON(c.category_id=a.category_id AND c.status='1')" . $store_join . " WHERE a.status='1'";
        if ($category_id) $sql .= " AND a.category_id='" . (int)$category_id . "'";
        $total = (int)$this->db->query($sql)->row['total'];
        $this->setCache($key, $total);
        return $total;
    }

    public function getArticle($id) {
        $key = $this->key('article.' . (int)$id);
        $cached = $this->getCache($key);
        if ($cached !== false) return $cached;

        $language_id = (int)$this->config->get('config_language_id');
        $store_join = $this->categoryStoreJoin('c');
        $row = $this->db->query("SELECT a.*, ad.title, ad.short_description, ad.description, ad.meta_title, ad.meta_description, ad.meta_keyword, cd.title AS category_title FROM `" . DB_PREFIX . "probg_blog_article` a INNER JOIN `" . DB_PREFIX . "probg_blog_article_description` ad ON(ad.article_id=a.article_id AND ad.language_id='" . $language_id . "') INNER JOIN `" . DB_PREFIX . "probg_blog_category` c ON(c.category_id=a.category_id AND c.status='1')" . $store_join . " INNER JOIN `" . DB_PREFIX . "probg_blog_category_description` cd ON(cd.category_id=a.category_id AND cd.language_id='" . $language_id . "') WHERE a.article_id='" . (int)$id . "' AND a.status='1'")->row;
        $this->setCache($key, $row);
        return $row;
    }

    public function getArticleImages($id) {
        $key = $this->key('images.' . (int)$id);
        $cached = $this->getCache($key);
        if ($cached !== false) return $cached;
        $rows = $this->db->query("SELECT * FROM `" . DB_PREFIX . "probg_blog_article_image` WHERE article_id='" . (int)$id . "' ORDER BY sort_order ASC, article_image_id ASC")->rows;
        $this->setCache($key, $rows);
        return $rows;
    }

    public function getCategoryLayoutId($category_id) {
        if (!$this->hasLayoutTable()) return 0;
        $query = $this->db->query("SELECT layout_id FROM `" . DB_PREFIX . "probg_blog_category_to_layout` WHERE category_id='" . (int)$category_id . "' AND store_id='" . (int)$this->config->get('config_store_id') . "'");
        return $query->num_rows ? (int)$query->row['layout_id'] : 0;
    }

    public function getSitemapData() {
        $key = $this->key('sitemap');
        $cached = $this->getCache($key);
        if ($cached !== false) return $cached;

        $language_id = (int)$this->config->get('config_language_id');
        $store_join = $this->categoryStoreJoin('c');
        $categories = $this->db->query("SELECT c.category_id,c.date_modified FROM `" . DB_PREFIX . "probg_blog_category` c INNER JOIN `" . DB_PREFIX . "probg_blog_category_description` cd ON(cd.category_id=c.category_id AND cd.language_id='" . $language_id . "')" . $store_join . " WHERE c.status='1'")->rows;
        $articles = $this->db->query("SELECT a.article_id,a.category_id,a.date_modified FROM `" . DB_PREFIX . "probg_blog_article` a INNER JOIN `" . DB_PREFIX . "probg_blog_article_description` ad ON(ad.article_id=a.article_id AND ad.language_id='" . $language_id . "') INNER JOIN `" . DB_PREFIX . "probg_blog_category` c ON(c.category_id=a.category_id AND c.status='1')" . $store_join . " WHERE a.status='1'")->rows;
        $result = array('categories'=>$categories,'articles'=>$articles);
        $this->setCache($key, $result);
        return $result;
    }

    private function categoryStoreJoin($category_alias) {
        if (!$this->hasStoreTable()) return '';
        return " INNER JOIN `" . DB_PREFIX . "probg_blog_category_to_store` c2s ON(c2s.category_id=" . $category_alias . ".category_id AND c2s.store_id='" . (int)$this->config->get('config_store_id') . "')";
    }

    private function hasStoreTable() {
        if ($this->store_table_exists !== null) return $this->store_table_exists;
        $query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . "probg_blog_category_to_store") . "'");
        $this->store_table_exists = (bool)$query->num_rows;
        return $this->store_table_exists;
    }

    private function hasLayoutTable() {
        if ($this->layout_table_exists !== null) return $this->layout_table_exists;
        $query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . "probg_blog_category_to_layout") . "'");
        $this->layout_table_exists = (bool)$query->num_rows;
        return $this->layout_table_exists;
    }

    private function key($suffix){return 'probg_blog.'.(int)$this->config->get('config_store_id').'.'.(int)$this->config->get('config_language_id').'.'.$suffix;}
    private function getCache($key){if(!$this->config->get('module_probg_blog_cache'))return false;$value=$this->cache->get($key);return $value===null?false:$value;}
    private function setCache($key,$value){if($this->config->get('module_probg_blog_cache'))$this->cache->set($key,$value);}
}
