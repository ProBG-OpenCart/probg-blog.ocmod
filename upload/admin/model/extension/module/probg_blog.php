<?php
class ModelExtensionModuleProbgBlog extends Model {
    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_category` (`category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, `sort_order` INT(11) NOT NULL DEFAULT '0', `status` TINYINT(1) NOT NULL DEFAULT '1', `date_added` DATETIME NOT NULL, `date_modified` DATETIME NOT NULL, PRIMARY KEY (`category_id`), KEY `status_sort_order` (`status`,`sort_order`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_category_description` (`category_id` INT(11) UNSIGNED NOT NULL, `language_id` INT(11) UNSIGNED NOT NULL, `title` VARCHAR(255) NOT NULL, `description` MEDIUMTEXT NOT NULL, `tags` MEDIUMTEXT NOT NULL, `meta_title` VARCHAR(255) NOT NULL, `meta_description` VARCHAR(255) NOT NULL, `meta_keyword` VARCHAR(255) NOT NULL, PRIMARY KEY (`category_id`,`language_id`), KEY `language_id` (`language_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_category_to_store` (`category_id` INT(11) UNSIGNED NOT NULL, `store_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', PRIMARY KEY (`category_id`,`store_id`), KEY `store_id` (`store_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_category_to_layout` (`category_id` INT(11) UNSIGNED NOT NULL, `store_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', `layout_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', PRIMARY KEY (`category_id`,`store_id`), KEY `layout_id` (`layout_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_article` (`article_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', `image` VARCHAR(255) NOT NULL DEFAULT '', `sort_order` INT(11) NOT NULL DEFAULT '0', `status` TINYINT(1) NOT NULL DEFAULT '1', `date_added` DATETIME NOT NULL, `date_modified` DATETIME NOT NULL, PRIMARY KEY (`article_id`), KEY `category_status` (`category_id`,`status`), KEY `status_date_added` (`status`,`date_added`), KEY `sort_order` (`sort_order`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_article_description` (`article_id` INT(11) UNSIGNED NOT NULL, `language_id` INT(11) UNSIGNED NOT NULL, `title` VARCHAR(255) NOT NULL, `short_description` MEDIUMTEXT NOT NULL, `description` MEDIUMTEXT NOT NULL, `meta_title` VARCHAR(255) NOT NULL, `meta_description` VARCHAR(255) NOT NULL, `meta_keyword` VARCHAR(255) NOT NULL, PRIMARY KEY (`article_id`,`language_id`), KEY `language_id` (`language_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_article_image` (`article_image_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, `article_id` INT(11) UNSIGNED NOT NULL, `image` VARCHAR(255) NOT NULL, `sort_order` INT(11) NOT NULL DEFAULT '0', PRIMARY KEY (`article_image_id`), KEY `article_sort_order` (`article_id`,`sort_order`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->ensureCategorySchema();
        $this->ensureArticleRelatedSchema();
        $this->load->model('setting/setting');
        if ($this->config->get('module_probg_blog_version') === null) {
            $this->model_setting_setting->editSetting('module_probg_blog', array('module_probg_blog_status'=>0,'module_probg_blog_sort'=>'date','module_probg_blog_limit'=>10,'module_probg_blog_image_list_width'=>400,'module_probg_blog_image_list_height'=>260,'module_probg_blog_image_article_width'=>900,'module_probg_blog_image_article_height'=>600,'module_probg_blog_image_gallery_width'=>300,'module_probg_blog_image_gallery_height'=>220,'module_probg_blog_default_image'=>'','module_probg_blog_sitemap'=>1,'module_probg_blog_cache'=>1,'module_probg_blog_version'=>'0.11.0'));
        }
    }

    public function uninstall() {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query LIKE 'probg_blog_category_id=%' OR query LIKE 'probg_blog_article_id=%' OR query = 'extension/module/probg_blog' OR query = 'route=extension/module/probg_blog'");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_article_related`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_article_image`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_article_description`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_article`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_category_to_layout`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_category_to_store`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_category_description`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "probg_blog_category`");
        $this->load->model('setting/setting');$this->model_setting_setting->deleteSetting('module_probg_blog');$this->cache->delete('probg_blog');
    }

    public function saveSectionSeo($descriptions) {
        require_once(DIR_SYSTEM . 'library/probg_blog_seo.php');$seo=new ProbgBlogSeo($this->db);$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'extension/module/probg_blog' OR query = 'route=extension/module/probg_blog'");
        foreach((array)$descriptions as $language_id=>$value){$title=isset($value['title'])?$value['title']:'';$keyword=isset($value['seo_keyword'])?trim($value['seo_keyword']):'';if($keyword==='')$keyword=$seo->slugify($title);$keyword=$seo->uniqueKeyword($keyword,$language_id,'extension/module/probg_blog');if($keyword!=='')$this->db->query("INSERT INTO `".DB_PREFIX."seo_url` SET store_id='0', language_id='".(int)$language_id."', query='extension/module/probg_blog', keyword='".$this->db->escape($keyword)."'");}$this->cache->delete('probg_blog');
    }

    public function getSectionSeo() {$data=array();$q=$this->db->query("SELECT language_id, keyword FROM `".DB_PREFIX."seo_url` WHERE store_id='0' AND (query='extension/module/probg_blog' OR query='route=extension/module/probg_blog')");foreach($q->rows as $row)$data[(int)$row['language_id']]=$row['keyword'];return $data;}

    public function migrate() {
        $this->ensureCategorySchema();
        $this->ensureArticleRelatedSchema();
        if ($this->config->get('module_probg_blog_version') === '0.11.0') return;
        require_once(DIR_SYSTEM . 'library/probg_blog_seo.php');
        $seo = new ProbgBlogSeo($this->db);

        $categories = $this->db->query("SELECT category_id, language_id, title, meta_title FROM `" . DB_PREFIX . "probg_blog_category_description`");
        foreach ($categories->rows as $row) {
            $category_id = (int)$row['category_id'];
            $language_id = (int)$row['language_id'];
            $title = $row['title'];
            if (trim($row['meta_title']) === '') $this->db->query("UPDATE `" . DB_PREFIX . "probg_blog_category_description` SET meta_title='" . $this->db->escape($title) . "' WHERE category_id='" . $category_id . "' AND language_id='" . $language_id . "'");
            $query_key = 'probg_blog_category_id=' . $category_id;
            $new_auto = $seo->slugify($title);
            $stores = $this->getCategoryStoreIds($category_id);
            if (!$stores) $stores = array(0);
            foreach ($stores as $store_id) {
                $existing = $this->db->query("SELECT seo_url_id, keyword FROM `" . DB_PREFIX . "seo_url` WHERE store_id='" . (int)$store_id . "' AND language_id='" . $language_id . "' AND query='" . $this->db->escape($query_key) . "' LIMIT 1");
                if ($existing->num_rows) continue;
                $reference = $this->db->query("SELECT keyword FROM `" . DB_PREFIX . "seo_url` WHERE language_id='" . $language_id . "' AND query='" . $this->db->escape($query_key) . "' ORDER BY store_id ASC, seo_url_id ASC LIMIT 1");
                $candidate = $reference->num_rows ? $reference->row['keyword'] : $new_auto;
                $keyword = $seo->uniqueKeyword($candidate, $language_id, $query_key, $store_id);
                $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id='" . (int)$store_id . "', language_id='" . $language_id . "', query='" . $this->db->escape($query_key) . "', keyword='" . $this->db->escape($keyword) . "'");
            }
        }

        $articles = $this->db->query("SELECT a.article_id, a.category_id, ad.language_id, ad.title, ad.meta_title FROM `" . DB_PREFIX . "probg_blog_article` a INNER JOIN `" . DB_PREFIX . "probg_blog_article_description` ad ON(ad.article_id=a.article_id)");
        foreach ($articles->rows as $row) {
            $article_id = (int)$row['article_id'];
            $language_id = (int)$row['language_id'];
            if (trim($row['meta_title']) === '') $this->db->query("UPDATE `" . DB_PREFIX . "probg_blog_article_description` SET meta_title='" . $this->db->escape($row['title']) . "' WHERE article_id='" . $article_id . "' AND language_id='" . $language_id . "'");
            $query_key = 'probg_blog_article_id=' . $article_id;
            $auto_keyword = $article_id . '-' . $seo->slugify($row['title']);
            $stores = $this->getCategoryStoreIds((int)$row['category_id']);
            if (!$stores) $stores = array(0);
            $reference = $this->db->query("SELECT keyword FROM `" . DB_PREFIX . "seo_url` WHERE language_id='" . $language_id . "' AND query='" . $this->db->escape($query_key) . "' ORDER BY store_id ASC, seo_url_id ASC LIMIT 1");
            $candidate = $reference->num_rows ? $reference->row['keyword'] : $auto_keyword;
            foreach ($stores as $store_id) {
                $existing = $this->db->query("SELECT seo_url_id FROM `" . DB_PREFIX . "seo_url` WHERE store_id='" . (int)$store_id . "' AND language_id='" . $language_id . "' AND query='" . $this->db->escape($query_key) . "' LIMIT 1");
                if ($existing->num_rows) continue;
                $keyword = $seo->uniqueKeyword($candidate, $language_id, $query_key, $store_id);
                $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id='" . (int)$store_id . "', language_id='" . $language_id . "', query='" . $this->db->escape($query_key) . "', keyword='" . $this->db->escape($keyword) . "'");
            }
        }

        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('module_probg_blog');
        $settings['module_probg_blog_version'] = '0.11.0';
        $this->model_setting_setting->editSetting('module_probg_blog', $settings);
        $this->cache->delete('probg_blog');
    }

    private function ensureCategorySchema() {
        $column=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."probg_blog_category_description` LIKE 'tags'");
        if(!$column->num_rows)$this->db->query("ALTER TABLE `".DB_PREFIX."probg_blog_category_description` ADD `tags` MEDIUMTEXT NOT NULL AFTER `description`");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_category_to_store` (`category_id` INT(11) UNSIGNED NOT NULL, `store_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', PRIMARY KEY (`category_id`,`store_id`), KEY `store_id` (`store_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_category_to_layout` (`category_id` INT(11) UNSIGNED NOT NULL, `store_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', `layout_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', PRIMARY KEY (`category_id`,`store_id`), KEY `layout_id` (`layout_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $categories=$this->db->query("SELECT category_id FROM `".DB_PREFIX."probg_blog_category`");
        $stores=array(0);$store_query=$this->db->query("SELECT store_id FROM `".DB_PREFIX."store`");foreach($store_query->rows as $row)$stores[]=(int)$row['store_id'];
        foreach($categories->rows as $category){$category_id=(int)$category['category_id'];$assigned=$this->db->query("SELECT category_id FROM `".DB_PREFIX."probg_blog_category_to_store` WHERE category_id='".$category_id."' LIMIT 1");if(!$assigned->num_rows){foreach(array_unique($stores) as $store_id)$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."probg_blog_category_to_store` SET category_id='".$category_id."', store_id='".(int)$store_id."'");}}
    }

    private function ensureArticleRelatedSchema() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "probg_blog_article_related` (`article_id` INT(11) UNSIGNED NOT NULL, `product_id` INT(11) UNSIGNED NOT NULL, PRIMARY KEY (`article_id`,`product_id`), KEY `product_id` (`product_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private function getCategoryStoreIds($category_id) {
        $stores = array();
        $query = $this->db->query("SELECT store_id FROM `" . DB_PREFIX . "probg_blog_category_to_store` WHERE category_id='" . (int)$category_id . "'");
        foreach ($query->rows as $row) $stores[] = (int)$row['store_id'];
        return array_values(array_unique($stores));
    }
}
