<?php
class ControllerExtensionFeedProbgBlogSitemap extends Controller {
    public function index() {
        if (!$this->config->get('module_probg_blog_status') || !$this->config->get('module_probg_blog_sitemap')) return;
        $this->response->addHeader('Content-Type: application/xml; charset=utf-8');
        $this->response->setOutput('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $this->googleSitemap() . '</urlset>');
    }
    public function googleSitemap() {
        if (!$this->config->get('module_probg_blog_status') || !$this->config->get('module_probg_blog_sitemap')) return '';
        $this->load->model('extension/probg_blog/blog');$data=$this->model_extension_probg_blog_blog->getSitemapData();$out='';
        $out.=$this->entry($this->link(),date('Y-m-d'),'weekly','0.8');
        foreach($data['categories'] as $c)$out.=$this->entry($this->link(array('probg_blog_category_id'=>$c['category_id'])),substr($c['date_modified'],0,10),'weekly','0.7');
        foreach($data['articles'] as $a)$out.=$this->entry($this->link(array('probg_blog_category_id'=>$a['category_id'],'probg_blog_article_id'=>$a['article_id'])),substr($a['date_modified'],0,10),'monthly','0.6');
        return $out;
    }
    private function link($args=array()){$q='';foreach($args as $k=>$v)$q.=($q?'&':'').$k.'='.$v;return html_entity_decode($this->url->link('extension/module/probg_blog',$q,true),ENT_QUOTES,'UTF-8');}
    private function entry($loc,$lastmod,$freq,$priority){return "\n<url><loc>".htmlspecialchars($loc,ENT_QUOTES,'UTF-8')."</loc><lastmod>".$lastmod."</lastmod><changefreq>".$freq."</changefreq><priority>".$priority."</priority></url>";}
}
