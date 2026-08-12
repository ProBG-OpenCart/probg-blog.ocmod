<?php
class ControllerExtensionModuleProbgBlogCategories extends Controller {
    public function index($setting) {
        if (!$this->config->get('module_probg_blog_status') || empty($setting['status'])) return '';
        $this->load->language('extension/module/probg_blog_categories');$this->load->model('extension/probg_blog/blog');$language_id=(int)$this->config->get('config_language_id');$data['heading_title']=isset($setting['title'][$language_id])&&$setting['title'][$language_id]!==''?$setting['title'][$language_id]:$this->language->get('heading_title');$data['show_count']=!empty($setting['show_count']);$rows=$this->model_extension_probg_blog_blog->getCategories();$limit=isset($setting['limit'])?(int)$setting['limit']:0;if($limit>0)$rows=array_slice($rows,0,$limit);$data['categories']=array();foreach($rows as $row)$data['categories'][]=array('title'=>$row['title'],'article_count'=>$row['article_count'],'href'=>$this->url->link('extension/module/probg_blog','probg_blog_category_id='.(int)$row['category_id'],true));return $this->load->view('extension/module/probg_blog_categories',$data);
    }
}
