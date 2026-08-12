<?php
class ModelExtensionProbgBlogArticle extends Model {
    public function addArticle($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "probg_blog_article` SET category_id = '" . (int)$data['category_id'] . "', image = '" . $this->db->escape(isset($data['image']) ? $data['image'] : '') . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_added = NOW(), date_modified = NOW()");
        $article_id = $this->db->getLastId();
        $this->saveDescriptions($article_id, $data); $this->saveImages($article_id, $data); $this->saveSeoUrls($article_id, $data); $this->clearCache();
        return $article_id;
    }
    public function editArticle($article_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "probg_blog_article` SET category_id = '" . (int)$data['category_id'] . "', image = '" . $this->db->escape(isset($data['image']) ? $data['image'] : '') . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE article_id = '" . (int)$article_id . "'");
        $this->saveDescriptions($article_id, $data); $this->saveImages($article_id, $data); $this->saveSeoUrls($article_id, $data); $this->clearCache();
    }
    public function deleteArticle($article_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_article` WHERE article_id = '" . (int)$article_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_article_description` WHERE article_id = '" . (int)$article_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "probg_blog_article_image` WHERE article_id = '" . (int)$article_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'probg_blog_article_id=" . (int)$article_id . "'"); $this->clearCache();
    }
    public function getArticle($article_id) { return $this->db->query("SELECT * FROM `" . DB_PREFIX . "probg_blog_article` WHERE article_id = '" . (int)$article_id . "'")->row; }
    public function getArticleDescriptions($article_id) {
        $data = array(); $q=$this->db->query("SELECT * FROM `" . DB_PREFIX . "probg_blog_article_description` WHERE article_id='".(int)$article_id."'");
        foreach($q->rows as $r) $data[$r['language_id']]=array('title'=>$r['title'],'short_description'=>$r['short_description'],'description'=>$r['description'],'meta_title'=>$r['meta_title'],'meta_description'=>$r['meta_description'],'meta_keyword'=>$r['meta_keyword'],'seo_keyword'=>'');
        $q=$this->db->query("SELECT language_id,keyword FROM `".DB_PREFIX."seo_url` WHERE store_id='0' AND query='probg_blog_article_id=".(int)$article_id."'");
        foreach($q->rows as $r){ if(!isset($data[$r['language_id']])) $data[$r['language_id']]=array('title'=>'','short_description'=>'','description'=>'','meta_title'=>'','meta_description'=>'','meta_keyword'=>'','seo_keyword'=>''); $data[$r['language_id']]['seo_keyword']=$r['keyword']; }
        return $data;
    }
    public function getArticleImages($article_id) { return $this->db->query("SELECT * FROM `".DB_PREFIX."probg_blog_article_image` WHERE article_id='".(int)$article_id."' ORDER BY sort_order ASC, article_image_id ASC")->rows; }
    public function getArticles($data=array()) {
        $sql="SELECT a.*, ad.title, cd.title AS category FROM `".DB_PREFIX."probg_blog_article` a LEFT JOIN `".DB_PREFIX."probg_blog_article_description` ad ON(a.article_id=ad.article_id AND ad.language_id='".(int)$this->config->get('config_language_id')."') LEFT JOIN `".DB_PREFIX."probg_blog_category_description` cd ON(a.category_id=cd.category_id AND cd.language_id='".(int)$this->config->get('config_language_id')."') WHERE 1";
        if(!empty($data['filter_title']))$sql.=" AND ad.title LIKE '%".$this->db->escape($data['filter_title'])."%'";
        if(isset($data['filter_category_id'])&&$data['filter_category_id']!=='')$sql.=" AND a.category_id='".(int)$data['filter_category_id']."'";
        if(isset($data['filter_status'])&&$data['filter_status']!=='')$sql.=" AND a.status='".(int)$data['filter_status']."'";
        if(!empty($data['filter_date_added_from']))$sql.=" AND DATE(a.date_added)>='".$this->db->escape($data['filter_date_added_from'])."'";
        if(!empty($data['filter_date_added_to']))$sql.=" AND DATE(a.date_added)<='".$this->db->escape($data['filter_date_added_to'])."'";
        $sorts=array('a.article_id','ad.title','cd.title','a.sort_order','a.status','a.date_added','a.date_modified');
        $sql.=" ORDER BY ".((isset($data['sort'])&&in_array($data['sort'],$sorts,true))?$data['sort']:'a.date_added').((isset($data['order'])&&$data['order']==='ASC')?' ASC':' DESC').", a.article_id DESC";
        if(isset($data['start'])||isset($data['limit'])){$start=max(0,isset($data['start'])?(int)$data['start']:0);$limit=max(1,isset($data['limit'])?(int)$data['limit']:20);$sql.=" LIMIT ".$start.",".$limit;}
        return $this->db->query($sql)->rows;
    }
    public function getTotalArticles($data=array()) {
        $sql="SELECT COUNT(*) AS total FROM `".DB_PREFIX."probg_blog_article` a LEFT JOIN `".DB_PREFIX."probg_blog_article_description` ad ON(a.article_id=ad.article_id AND ad.language_id='".(int)$this->config->get('config_language_id')."') WHERE 1";
        if(!empty($data['filter_title']))$sql.=" AND ad.title LIKE '%".$this->db->escape($data['filter_title'])."%'";
        if(isset($data['filter_category_id'])&&$data['filter_category_id']!=='')$sql.=" AND a.category_id='".(int)$data['filter_category_id']."'";
        if(isset($data['filter_status'])&&$data['filter_status']!=='')$sql.=" AND a.status='".(int)$data['filter_status']."'";
        if(!empty($data['filter_date_added_from']))$sql.=" AND DATE(a.date_added)>='".$this->db->escape($data['filter_date_added_from'])."'";
        if(!empty($data['filter_date_added_to']))$sql.=" AND DATE(a.date_added)<='".$this->db->escape($data['filter_date_added_to'])."'";
        return (int)$this->db->query($sql)->row['total'];
    }
    public function getSeoUrlByKeyword($keyword,$language_id,$article_id=0){$sql="SELECT * FROM `".DB_PREFIX."seo_url` WHERE store_id='0' AND language_id='".(int)$language_id."' AND keyword='".$this->db->escape($keyword)."'";if($article_id)$sql.=" AND query!='probg_blog_article_id=".(int)$article_id."'";return $this->db->query($sql)->row;}
    private function saveDescriptions($article_id,$data){$this->db->query("DELETE FROM `".DB_PREFIX."probg_blog_article_description` WHERE article_id='".(int)$article_id."'");if(empty($data['article_description'])||!is_array($data['article_description']))return;foreach($data['article_description'] as $language_id=>$v){$title=$this->value($v,'title');$meta=trim($this->value($v,'meta_title'))!==''?$this->value($v,'meta_title'):$title;$this->db->query("INSERT INTO `".DB_PREFIX."probg_blog_article_description` SET article_id='".(int)$article_id."', language_id='".(int)$language_id."', title='".$this->db->escape($title)."', short_description='".$this->db->escape($this->value($v,'short_description'))."', description='".$this->db->escape($this->value($v,'description'))."', meta_title='".$this->db->escape($meta)."', meta_description='".$this->db->escape($this->value($v,'meta_description'))."', meta_keyword='".$this->db->escape($this->value($v,'meta_keyword'))."'");}}
    private function saveImages($article_id,$data){$this->db->query("DELETE FROM `".DB_PREFIX."probg_blog_article_image` WHERE article_id='".(int)$article_id."'");if(empty($data['article_image'])||!is_array($data['article_image']))return;foreach($data['article_image'] as $i){$image=isset($i['image'])?trim($i['image']):'';if($image==='')continue;$this->db->query("INSERT INTO `".DB_PREFIX."probg_blog_article_image` SET article_id='".(int)$article_id."', image='".$this->db->escape($image)."', sort_order='".(int)(isset($i['sort_order'])?$i['sort_order']:0)."'");}}
    private function saveSeoUrls($article_id,$data){$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE query='probg_blog_article_id=".(int)$article_id."'");if(empty($data['article_description'])||!is_array($data['article_description']))return;require_once(DIR_SYSTEM.'library/probg_blog_seo.php');$seo=new ProbgBlogSeo($this->db);$query_key='probg_blog_article_id='.(int)$article_id;foreach($data['article_description'] as $language_id=>$v){$keyword=isset($v['seo_keyword'])?trim($v['seo_keyword']):'';if($keyword==='')$keyword=(int)$article_id.'-'.$seo->slugify($this->value($v,'title'));$keyword=$seo->uniqueKeyword($keyword,$language_id,$query_key);$this->db->query("INSERT INTO `".DB_PREFIX."seo_url` SET store_id='0', language_id='".(int)$language_id."', query='".$this->db->escape($query_key)."', keyword='".$this->db->escape($keyword)."'");}}
    private function clearCache(){ $this->cache->delete('probg_blog'); }
    private function value($d,$k){return isset($d[$k])?$d[$k]:'';}
}
