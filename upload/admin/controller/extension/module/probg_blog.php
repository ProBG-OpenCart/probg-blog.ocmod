<?php
class ControllerExtensionModuleProbgBlog extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/probg_blog');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('extension/module/probg_blog');
        $this->load->model('extension/probg_blog/category');
        $this->load->model('extension/probg_blog/article');
        $this->model_extension_module_probg_blog->migrate();

        if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
            $post = $this->request->post;
            if (!empty($post['module_probg_blog_description']) && is_array($post['module_probg_blog_description'])) {
                foreach ($post['module_probg_blog_description'] as $language_id => &$description) {
                    if (empty($description['meta_title'])) $description['meta_title'] = isset($description['title']) ? $description['title'] : '';
                }
                unset($description);
            }
            $post['module_probg_blog_version'] = '0.8.0';
            $this->model_setting_setting->editSetting('module_probg_blog', $post);
            $this->model_extension_module_probg_blog->saveSectionSeo(isset($post['module_probg_blog_description']) ? $post['module_probg_blog_description'] : array());
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/probg_blog', 'user_token=' . $this->session->data['user_token'], true));
        }

        $keys=array('heading_title','text_home','text_extension','text_success','text_edit','text_enabled','text_disabled','text_categories','text_articles','text_version','text_stage','text_stage_info','text_general','text_content','text_images','text_integrations','text_date','text_sort_order','entry_status','entry_sort','entry_limit','entry_title','entry_description','entry_meta_title','entry_meta_description','entry_meta_keyword','entry_seo_keyword','entry_default_image','entry_image_list','entry_image_article','entry_image_gallery','entry_sitemap','entry_cache','help_seo_keyword','button_save','button_cancel','button_categories','button_articles');
        foreach($keys as $k)$data[$k]=$this->language->get($k);
        $data['error_warning']=isset($this->error['warning'])?$this->error['warning']:'';
        $data['error_limit']=isset($this->error['limit'])?$this->error['limit']:'';
        $data['error_image']=isset($this->error['image'])?$this->error['image']:'';
        $data['error_title']=isset($this->error['title'])?$this->error['title']:array();
        $data['error_seo_keyword']=isset($this->error['seo_keyword'])?$this->error['seo_keyword']:array();
        $data['success']=isset($this->session->data['success'])?$this->session->data['success']:''; unset($this->session->data['success']);
        $data['breadcrumbs']=array(
            array('text'=>$this->language->get('text_home'),'href'=>$this->url->link('common/dashboard','user_token='.$this->session->data['user_token'],true)),
            array('text'=>$this->language->get('text_extension'),'href'=>$this->url->link('marketplace/extension','user_token='.$this->session->data['user_token'].'&type=module',true)),
            array('text'=>$this->language->get('heading_title'),'href'=>$this->url->link('extension/module/probg_blog','user_token='.$this->session->data['user_token'],true))
        );
        $data['action']=$this->url->link('extension/module/probg_blog','user_token='.$this->session->data['user_token'],true);
        $data['cancel']=$this->url->link('marketplace/extension','user_token='.$this->session->data['user_token'].'&type=module',true);
        $data['categories_url']=$this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'],true);
        $data['articles_url']=$this->url->link('extension/probg_blog/article','user_token='.$this->session->data['user_token'],true);
        $data['total_categories']=$this->model_extension_probg_blog_category->getTotalCategories();
        $data['total_articles']=$this->model_extension_probg_blog_article->getTotalArticles();
        $data['stage']='8'; $data['version']='0.8.0';

        $this->load->model('localisation/language'); $data['languages']=$this->model_localisation_language->getLanguages();
        $section_seo=$this->model_extension_module_probg_blog->getSectionSeo();
        if(isset($this->request->post['module_probg_blog_description']))$descriptions=$this->request->post['module_probg_blog_description']; else {$descriptions=$this->config->get('module_probg_blog_description'); if(!is_array($descriptions))$descriptions=array();}
        foreach($data['languages'] as $language){$id=(int)$language['language_id'];if(!isset($descriptions[$id]))$descriptions[$id]=array('title'=>'','description'=>'','meta_title'=>'','meta_description'=>'','meta_keyword'=>'','seo_keyword'=>'');if(empty($descriptions[$id]['seo_keyword'])&&isset($section_seo[$id]))$descriptions[$id]['seo_keyword']=$section_seo[$id];}
        $data['module_probg_blog_description']=$descriptions;
        $defaults=array('module_probg_blog_status'=>0,'module_probg_blog_sort'=>'date','module_probg_blog_limit'=>10,'module_probg_blog_default_image'=>'','module_probg_blog_image_list_width'=>400,'module_probg_blog_image_list_height'=>260,'module_probg_blog_image_article_width'=>900,'module_probg_blog_image_article_height'=>600,'module_probg_blog_image_gallery_width'=>300,'module_probg_blog_image_gallery_height'=>220,'module_probg_blog_sitemap'=>1,'module_probg_blog_cache'=>1);
        foreach($defaults as $k=>$v)$data[$k]=isset($this->request->post[$k])?$this->request->post[$k]:(($this->config->get($k)!==null)?$this->config->get($k):$v);
        $this->load->model('tool/image');
        $image=$data['module_probg_blog_default_image'];$data['thumb']=($image&&is_file(DIR_IMAGE.$image))?$this->model_tool_image->resize($image,100,100):$this->model_tool_image->resize('no_image.png',100,100);$data['placeholder']=$this->model_tool_image->resize('no_image.png',100,100);
        $this->document->addStyle('view/javascript/summernote/summernote.css');$this->document->addScript('view/javascript/summernote/summernote.js');$this->document->addScript('view/javascript/summernote/opencart.js');$data['summernote']=$this->config->get('config_language');
        $data['header']=$this->load->controller('common/header');$data['column_left']=$this->load->controller('common/column_left');$data['footer']=$this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/probg_blog',$data));
    }

    public function install(){ $this->load->model('extension/module/probg_blog');$this->model_extension_module_probg_blog->install();$this->load->model('user/user_group');$gid=$this->user->getGroupId();$routes=array('extension/module/probg_blog','extension/probg_blog/category','extension/probg_blog/article','extension/module/probg_blog_articles','extension/module/probg_blog_categories');foreach($routes as $r){$this->model_user_user_group->addPermission($gid,'access',$r);$this->model_user_user_group->addPermission($gid,'modify',$r);} }
    public function uninstall(){ $this->load->model('extension/module/probg_blog');$this->model_extension_module_probg_blog->uninstall(); }

    protected function validate(){
        if(!$this->user->hasPermission('modify','extension/module/probg_blog'))$this->error['warning']=$this->language->get('error_permission');
        $d=isset($this->request->post['module_probg_blog_description'])?$this->request->post['module_probg_blog_description']:array();
        $this->load->model('localisation/language');
        foreach($this->model_localisation_language->getLanguages() as $lang){$id=$lang['language_id'];$v=isset($d[$id])?$d[$id]:array();if(isset($v['title'])&&utf8_strlen($v['title'])>255)$this->error['title'][$id]=$this->language->get('error_title');$kw=isset($v['seo_keyword'])?trim($v['seo_keyword']):'';if($kw!==''&&!preg_match('/^[A-Za-z0-9_-]+$/',$kw))$this->error['seo_keyword'][$id]=$this->language->get('error_seo_keyword');}
        $limit=isset($this->request->post['module_probg_blog_limit'])?(int)$this->request->post['module_probg_blog_limit']:0;if($limit<1||$limit>100)$this->error['limit']=$this->language->get('error_limit');
        foreach(array('module_probg_blog_image_list_width','module_probg_blog_image_list_height','module_probg_blog_image_article_width','module_probg_blog_image_article_height','module_probg_blog_image_gallery_width','module_probg_blog_image_gallery_height') as $k){if(empty($this->request->post[$k])||(int)$this->request->post[$k]<1)$this->error['image']=$this->language->get('error_image');}
        return !$this->error;
    }
}
