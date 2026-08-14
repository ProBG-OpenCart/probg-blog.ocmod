<?php
class ControllerExtensionProbgBlogCategory extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/probg_blog/category');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/probg_blog/category');
        $this->migrateIfNeeded();
        $this->getList();
    }

    public function add() {
        $this->load->language('extension/probg_blog/category');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/probg_blog/category');
        $this->migrateIfNeeded();
        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validateForm()) {
            $this->model_extension_probg_blog_category->addCategory($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'].$this->listUrl(),true));
        }
        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/probg_blog/category');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/probg_blog/category');
        $this->migrateIfNeeded();
        $id = isset($this->request->get['category_id']) ? (int)$this->request->get['category_id'] : 0;
        if (!$id || !$this->model_extension_probg_blog_category->getCategory($id)) {
            $this->response->redirect($this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'],true));
            return;
        }
        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validateForm()) {
            $this->model_extension_probg_blog_category->editCategory($id,$this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'].$this->listUrl(),true));
        }
        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/probg_blog/category');
        $this->load->model('extension/probg_blog/category');
        $this->migrateIfNeeded();
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ((array)$this->request->post['selected'] as $id) $this->model_extension_probg_blog_category->deleteCategory((int)$id);
            $this->session->data['success'] = $this->language->get('text_success_delete');
            $this->response->redirect($this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'].$this->listUrl(),true));
        }
        $this->getList();
    }

    protected function getList() {
        $f_title = isset($this->request->get['filter_title']) ? $this->request->get['filter_title'] : '';
        $f_status = isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : '';
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'c.sort_order';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = (int)$this->config->get('config_limit_admin');
        $filter = array('filter_title'=>$f_title,'filter_status'=>$f_status,'sort'=>$sort,'order'=>$order,'start'=>($page-1)*$limit,'limit'=>$limit);
        $total = $this->model_extension_probg_blog_category->getTotalCategories($filter);
        $results = $this->model_extension_probg_blog_category->getCategories($filter);
        $data['categories'] = array();
        foreach ($results as $row) {
            $data['categories'][] = array(
                'category_id'=>$row['category_id'],
                'title'=>$row['title'],
                'sort_order'=>$row['sort_order'],
                'status'=>$row['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'date_added'=>date($this->language->get('date_format_short'),strtotime($row['date_added'])),
                'date_modified'=>date($this->language->get('date_format_short'),strtotime($row['date_modified'])),
                'edit'=>$this->url->link('extension/probg_blog/category/edit','user_token='.$this->session->data['user_token'].'&category_id='.(int)$row['category_id'].$this->listUrl(),true)
            );
        }
        $this->baseData($data);
        foreach (array('text_list','text_no_results','text_confirm','text_all','text_enabled','text_disabled','column_title','column_sort_order','column_status','column_date_added','column_date_modified','column_action','entry_title','entry_status','button_add','button_delete','button_edit','button_filter','button_settings') as $key) $data[$key] = $this->language->get($key);
        $data['add'] = $this->url->link('extension/probg_blog/category/add','user_token='.$this->session->data['user_token'].$this->listUrl(),true);
        $data['delete'] = $this->url->link('extension/probg_blog/category/delete','user_token='.$this->session->data['user_token'].$this->listUrl(),true);
        $data['filter_title']=$f_title;$data['filter_status']=$f_status;$data['sort']=$sort;$data['order']=$order;
        $data['selected']=isset($this->request->post['selected'])?(array)$this->request->post['selected']:array();
        $pagination=new Pagination();$pagination->total=$total;$pagination->page=$page;$pagination->limit=$limit;$pagination->url=$this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'].$this->listUrl(array('page')).'&page={page}',true);$data['pagination']=$pagination->render();
        $start=$total?(($page-1)*$limit)+1:0;$end=min($total,$page*$limit);$data['results']=sprintf($this->language->get('text_pagination'),$start,$end,$total,ceil($total/$limit));
        $data['header']=$this->load->controller('common/header');$data['column_left']=$this->load->controller('common/column_left');$data['footer']=$this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/probg_blog/category_list',$data));
    }

    protected function getForm() {
        $data=array();$this->baseData($data);
        foreach(array('text_add','text_edit','text_general','text_settings','text_enabled','text_disabled','text_automatic','text_default','entry_title','entry_description','entry_tags','entry_meta_title','entry_meta_description','entry_meta_keyword','entry_seo_keyword','entry_sort_order','entry_status','entry_store','entry_layout','entry_date_added','entry_date_modified','help_seo_keyword','help_tags','button_save','button_cancel') as $key) $data[$key]=$this->language->get($key);
        $id=isset($this->request->get['category_id'])?(int)$this->request->get['category_id']:0;
        $data['text_form']=$id?$this->language->get('text_edit'):$this->language->get('text_add');
        $data['error_warning']=isset($this->error['warning'])?$this->error['warning']:'';
        $data['error_title']=isset($this->error['title'])?$this->error['title']:array();
        $data['error_seo_keyword']=isset($this->error['seo_keyword'])?$this->error['seo_keyword']:array();
        $data['action']=$this->url->link($id?'extension/probg_blog/category/edit':'extension/probg_blog/category/add','user_token='.$this->session->data['user_token'].($id?'&category_id='.$id:'').$this->listUrl(),true);
        $data['cancel']=$this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'].$this->listUrl(),true);
        $info=$id?$this->model_extension_probg_blog_category->getCategory($id):array();
        $this->load->model('localisation/language');$data['languages']=$this->model_localisation_language->getLanguages();
        $data['category_description']=isset($this->request->post['category_description'])?$this->request->post['category_description']:($id?$this->model_extension_probg_blog_category->getCategoryDescriptions($id):array());
        $data['sort_order']=isset($this->request->post['sort_order'])?(int)$this->request->post['sort_order']:(isset($info['sort_order'])?(int)$info['sort_order']:0);
        $data['status']=isset($this->request->post['status'])?(int)$this->request->post['status']:(isset($info['status'])?(int)$info['status']:1);
        $data['date_added']=isset($info['date_added'])?date($this->language->get('datetime_format'),strtotime($info['date_added'])):$this->language->get('text_automatic');
        $data['date_modified']=isset($info['date_modified'])?date($this->language->get('datetime_format'),strtotime($info['date_modified'])):$this->language->get('text_automatic');

        $this->load->model('setting/store');
        $data['stores']=array(array('store_id'=>0,'name'=>$this->config->get('config_name').' ('.$this->language->get('text_default').')'));
        foreach($this->model_setting_store->getStores() as $store)$data['stores'][]=$store;
        $data['category_store']=isset($this->request->post['category_store'])?(array)$this->request->post['category_store']:($id?$this->model_extension_probg_blog_category->getCategoryStores($id):array(0));
        if(!$data['category_store'])$data['category_store']=array(0);

        $this->load->model('design/layout');$data['layouts']=$this->model_design_layout->getLayouts();
        $data['category_layout']=isset($this->request->post['category_layout'])?(array)$this->request->post['category_layout']:($id?$this->model_extension_probg_blog_category->getCategoryLayouts($id):array());

        $this->document->addStyle('view/javascript/summernote/summernote.css');$this->document->addScript('view/javascript/summernote/summernote.js');$this->document->addScript('view/javascript/summernote/opencart.js');$data['summernote']=$this->config->get('config_language');
        $data['header']=$this->load->controller('common/header');$data['column_left']=$this->load->controller('common/column_left');$data['footer']=$this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/probg_blog/category_form',$data));
    }

    protected function validateForm() {
        if(!$this->user->hasPermission('modify','extension/probg_blog/category'))$this->error['warning']=$this->language->get('error_permission');
        $this->load->model('localisation/language');$id=isset($this->request->get['category_id'])?(int)$this->request->get['category_id']:0;
        foreach($this->model_localisation_language->getLanguages() as $language){
            $language_id=(int)$language['language_id'];$value=isset($this->request->post['category_description'][$language_id])?$this->request->post['category_description'][$language_id]:array();
            $title=isset($value['title'])?trim($value['title']):'';
            if(utf8_strlen($title)<1||utf8_strlen($title)>255)$this->error['title'][$language_id]=$this->language->get('error_title');
            $keyword=isset($value['seo_keyword'])?trim($value['seo_keyword']):'';
            if($keyword!==''&&(!preg_match('/^[A-Za-z0-9_-]+$/',$keyword)||$this->model_extension_probg_blog_category->getSeoUrlByKeyword($keyword,$language_id,$id)))$this->error['seo_keyword'][$language_id]=$this->language->get('error_seo_keyword_exists');
        }
        if($this->error&&!isset($this->error['warning']))$this->error['warning']=$this->language->get('error_warning');
        return !$this->error;
    }

    protected function validateDelete() {
        if(!$this->user->hasPermission('modify','extension/probg_blog/category'))$this->error['warning']=$this->language->get('error_permission');
        foreach((array)$this->request->post['selected'] as $id){$count=$this->model_extension_probg_blog_category->getTotalArticlesByCategoryId($id);if($count)$this->error['warning']=sprintf($this->language->get('error_article'),$count);}
        return !$this->error;
    }

    private function migrateIfNeeded() {
        if ($this->config->get('module_probg_blog_version') !== '0.9.0') {
            $this->load->model('extension/module/probg_blog');
            $this->model_extension_module_probg_blog->migrate();
        }
    }

    private function baseData(&$data) {
        $data['heading_title']=$this->language->get('heading_title');
        $data['breadcrumbs']=array(
            array('text'=>$this->language->get('text_home'),'href'=>$this->url->link('common/dashboard','user_token='.$this->session->data['user_token'],true)),
            array('text'=>$this->language->get('text_blog'),'href'=>$this->url->link('extension/module/probg_blog','user_token='.$this->session->data['user_token'],true)),
            array('text'=>$this->language->get('heading_title'),'href'=>$this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'],true))
        );
        $data['tab_settings']=$this->language->get('text_tab_settings');$data['tab_categories']=$this->language->get('text_tab_categories');$data['tab_articles']=$this->language->get('text_tab_articles');
        $data['settings_url']=$this->url->link('extension/module/probg_blog','user_token='.$this->session->data['user_token'],true);
        $data['categories_url']=$this->url->link('extension/probg_blog/category','user_token='.$this->session->data['user_token'],true);
        $data['articles_url']=$this->url->link('extension/probg_blog/article','user_token='.$this->session->data['user_token'],true);
        $data['user_token']=$this->session->data['user_token'];
        $data['error_warning']=isset($this->error['warning'])?$this->error['warning']:'';$data['success']=isset($this->session->data['success'])?$this->session->data['success']:'';unset($this->session->data['success']);
    }

    private function listUrl($exclude=array()) {
        $url='';foreach(array('filter_title','filter_status','sort','order','page') as $param)if(!in_array($param,$exclude,true)&&isset($this->request->get[$param])&&$this->request->get[$param]!=='')$url.='&'.$param.'='.urlencode($this->request->get[$param]);return $url;
    }
}
