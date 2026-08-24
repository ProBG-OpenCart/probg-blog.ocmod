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
        if ($this->config->get('module_probg_blog_version') !== '1.0.1') {
            $this->model_extension_module_probg_blog->migrate();
            $settings = $this->model_setting_setting->getSetting('module_probg_blog');
            if (!isset($settings['module_probg_blog_layout_output'])) $settings['module_probg_blog_layout_output'] = 'articles';
            if (!isset($settings['module_probg_blog_menu_description'])) $settings['module_probg_blog_menu_description'] = array();
            if (!isset($settings['module_probg_blog_menu_show_blog'])) $settings['module_probg_blog_menu_show_blog'] = 1;
            if (!isset($settings['module_probg_blog_menu_show_categories'])) $settings['module_probg_blog_menu_show_categories'] = 1;
            if (!isset($settings['module_probg_blog_menu_show_articles'])) $settings['module_probg_blog_menu_show_articles'] = 1;
            if (!isset($settings['module_probg_blog_menu_category_id'])) $settings['module_probg_blog_menu_category_id'] = 0;
            if (!isset($settings['module_probg_blog_menu_limit'])) $settings['module_probg_blog_menu_limit'] = 10;
            if (!isset($settings['module_probg_blog_menu_sort'])) $settings['module_probg_blog_menu_sort'] = 'date';
            $settings['module_probg_blog_version'] = '1.0.1';
            $this->model_setting_setting->editSetting('module_probg_blog', $settings);
        }

        if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
            $post = $this->request->post;

            if (!empty($post['module_probg_blog_description']) && is_array($post['module_probg_blog_description'])) {
                foreach ($post['module_probg_blog_description'] as &$description) {
                    if (empty($description['meta_title'])) {
                        $description['meta_title'] = isset($description['title']) ? $description['title'] : '';
                    }
                }
                unset($description);
            }

            $post['module_probg_blog_version'] = '1.0.1';
            $this->model_setting_setting->editSetting('module_probg_blog', $post);
            $this->model_extension_module_probg_blog->saveSectionSeo(isset($post['module_probg_blog_description']) ? $post['module_probg_blog_description'] : array());
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/probg_blog', 'user_token=' . $this->session->data['user_token'], true));
        }

        $keys = array(
            'heading_title', 'text_home', 'text_extension', 'text_success', 'text_edit', 'text_enabled', 'text_disabled',
            'text_categories', 'text_articles', 'text_settings', 'text_version', 'text_stage', 'text_stage_info',
            'text_general', 'text_content', 'text_images', 'text_integrations', 'text_menu', 'text_date', 'text_sort_order',
            'text_layout_articles', 'text_layout_menu', 'text_all_categories',
            'entry_status', 'entry_sort', 'entry_limit', 'entry_title', 'entry_description', 'entry_meta_title',
            'entry_meta_description', 'entry_meta_keyword', 'entry_seo_keyword', 'entry_default_image',
            'entry_image_list', 'entry_image_article', 'entry_image_gallery', 'entry_sitemap', 'entry_cache',
            'entry_layout_output', 'entry_menu_title', 'entry_menu_show_blog', 'entry_menu_show_categories',
            'entry_menu_show_articles', 'entry_menu_category', 'entry_menu_limit', 'entry_menu_sort',
            'help_seo_keyword', 'help_layout_output', 'help_menu_category', 'button_save', 'button_cancel',
            'button_categories', 'button_articles'
        );

        foreach ($keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_limit'] = isset($this->error['limit']) ? $this->error['limit'] : '';
        $data['error_menu_limit'] = isset($this->error['menu_limit']) ? $this->error['menu_limit'] : '';
        $data['error_image'] = isset($this->error['image']) ? $this->error['image'] : '';
        $data['error_title'] = isset($this->error['title']) ? $this->error['title'] : array();
        $data['error_menu_title'] = isset($this->error['menu_title']) ? $this->error['menu_title'] : array();
        $data['error_seo_keyword'] = isset($this->error['seo_keyword']) ? $this->error['seo_keyword'] : array();
        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        unset($this->session->data['success']);

        $data['breadcrumbs'] = array(
            array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)),
            array('text' => $this->language->get('text_extension'), 'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)),
            array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/module/probg_blog', 'user_token=' . $this->session->data['user_token'], true))
        );

        $data['action'] = $this->url->link('extension/module/probg_blog', 'user_token=' . $this->session->data['user_token'], true);
        $data['settings_url'] = $data['action'];
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['categories_url'] = $this->url->link('extension/probg_blog/category', 'user_token=' . $this->session->data['user_token'], true);
        $data['articles_url'] = $this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'], true);
        $data['total_categories'] = $this->model_extension_probg_blog_category->getTotalCategories();
        $data['total_articles'] = $this->model_extension_probg_blog_article->getTotalArticles();
        $data['stage'] = '13';
        $data['version'] = '1.0.1';

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $section_seo = $this->model_extension_module_probg_blog->getSectionSeo();
        if (isset($this->request->post['module_probg_blog_description'])) {
            $descriptions = $this->request->post['module_probg_blog_description'];
        } else {
            $descriptions = $this->config->get('module_probg_blog_description');
            if (!is_array($descriptions)) {
                $descriptions = array();
            }
        }

        foreach ($data['languages'] as $language) {
            $language_id = (int)$language['language_id'];
            if (!isset($descriptions[$language_id])) {
                $descriptions[$language_id] = array('title'=>'','description'=>'','meta_title'=>'','meta_description'=>'','meta_keyword'=>'','seo_keyword'=>'');
            }
            if (empty($descriptions[$language_id]['seo_keyword']) && isset($section_seo[$language_id])) {
                $descriptions[$language_id]['seo_keyword'] = $section_seo[$language_id];
            }
        }
        $data['module_probg_blog_description'] = $descriptions;

        if (isset($this->request->post['module_probg_blog_menu_description'])) {
            $menu_descriptions = $this->request->post['module_probg_blog_menu_description'];
        } else {
            $menu_descriptions = $this->config->get('module_probg_blog_menu_description');
            if (!is_array($menu_descriptions)) {
                $menu_descriptions = array();
            }
        }

        foreach ($data['languages'] as $language) {
            $language_id = (int)$language['language_id'];
            if (!isset($menu_descriptions[$language_id])) {
                $menu_descriptions[$language_id] = array('title' => '');
            }
        }
        $data['module_probg_blog_menu_description'] = $menu_descriptions;

        $defaults = array(
            'module_probg_blog_status'=>0,
            'module_probg_blog_sort'=>'date',
            'module_probg_blog_limit'=>10,
            'module_probg_blog_default_image'=>'',
            'module_probg_blog_image_list_width'=>400,
            'module_probg_blog_image_list_height'=>260,
            'module_probg_blog_image_article_width'=>900,
            'module_probg_blog_image_article_height'=>600,
            'module_probg_blog_image_gallery_width'=>300,
            'module_probg_blog_image_gallery_height'=>220,
            'module_probg_blog_sitemap'=>1,
            'module_probg_blog_cache'=>1,
            'module_probg_blog_layout_output'=>'articles',
            'module_probg_blog_menu_show_blog'=>1,
            'module_probg_blog_menu_show_categories'=>1,
            'module_probg_blog_menu_show_articles'=>1,
            'module_probg_blog_menu_category_id'=>0,
            'module_probg_blog_menu_limit'=>10,
            'module_probg_blog_menu_sort'=>'date'
        );

        foreach ($defaults as $key => $value) {
            $data[$key] = isset($this->request->post[$key]) ? $this->request->post[$key] : (($this->config->get($key) !== null) ? $this->config->get($key) : $value);
        }

        $data['menu_categories'] = $this->model_extension_probg_blog_category->getCategories(array('sort' => 'cd.title', 'order' => 'ASC'));

        $this->load->model('tool/image');
        $image = $data['module_probg_blog_default_image'];
        $data['thumb'] = ($image && is_file(DIR_IMAGE . $image)) ? $this->model_tool_image->resize($image, 100, 100) : $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->document->addStyle('view/javascript/summernote/summernote.css');
        $this->document->addScript('view/javascript/summernote/summernote.js');
        $this->document->addScript('view/javascript/summernote/opencart.js');
        $data['summernote'] = $this->config->get('config_language');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/probg_blog', $data));
    }

    public function install() {
        $this->load->model('extension/module/probg_blog');
        $this->model_extension_module_probg_blog->install();
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('module_probg_blog');
        $settings['module_probg_blog_layout_output'] = 'articles';
        $settings['module_probg_blog_menu_description'] = array();
        $settings['module_probg_blog_menu_show_blog'] = 1;
        $settings['module_probg_blog_menu_show_categories'] = 1;
        $settings['module_probg_blog_menu_show_articles'] = 1;
        $settings['module_probg_blog_menu_category_id'] = 0;
        $settings['module_probg_blog_menu_limit'] = 10;
        $settings['module_probg_blog_menu_sort'] = 'date';
        $settings['module_probg_blog_version'] = '1.0.1';
        $this->model_setting_setting->editSetting('module_probg_blog', $settings);
        $this->load->model('user/user_group');
        $group_id = $this->user->getGroupId();
        $routes = array('extension/module/probg_blog','extension/probg_blog/category','extension/probg_blog/article');
        foreach ($routes as $route) {
            $this->model_user_user_group->addPermission($group_id, 'access', $route);
            $this->model_user_user_group->addPermission($group_id, 'modify', $route);
        }
    }

    public function uninstall() {
        $this->load->model('extension/module/probg_blog');
        $this->model_extension_module_probg_blog->uninstall();
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/probg_blog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $descriptions = isset($this->request->post['module_probg_blog_description']) ? $this->request->post['module_probg_blog_description'] : array();
        $menu_descriptions = isset($this->request->post['module_probg_blog_menu_description']) ? $this->request->post['module_probg_blog_menu_description'] : array();
        $this->load->model('localisation/language');

        foreach ($this->model_localisation_language->getLanguages() as $language) {
            $language_id = (int)$language['language_id'];
            $value = isset($descriptions[$language_id]) ? $descriptions[$language_id] : array();
            if (isset($value['title']) && utf8_strlen($value['title']) > 255) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }
            $keyword = isset($value['seo_keyword']) ? trim($value['seo_keyword']) : '';
            if ($keyword !== '' && !preg_match('/^[A-Za-z0-9_-]+$/', $keyword)) {
                $this->error['seo_keyword'][$language_id] = $this->language->get('error_seo_keyword');
            }

            $menu_value = isset($menu_descriptions[$language_id]) ? $menu_descriptions[$language_id] : array();
            if (isset($menu_value['title']) && utf8_strlen($menu_value['title']) > 255) {
                $this->error['menu_title'][$language_id] = $this->language->get('error_menu_title');
            }
        }

        $limit = isset($this->request->post['module_probg_blog_limit']) ? (int)$this->request->post['module_probg_blog_limit'] : 0;
        if ($limit < 1 || $limit > 100) {
            $this->error['limit'] = $this->language->get('error_limit');
        }

        $menu_limit = isset($this->request->post['module_probg_blog_menu_limit']) ? (int)$this->request->post['module_probg_blog_menu_limit'] : 10;
        if ($menu_limit < 1 || $menu_limit > 100) {
            $this->error['menu_limit'] = $this->language->get('error_menu_limit');
        }

        foreach (array('module_probg_blog_image_list_width','module_probg_blog_image_list_height','module_probg_blog_image_article_width','module_probg_blog_image_article_height','module_probg_blog_image_gallery_width','module_probg_blog_image_gallery_height') as $key) {
            if (empty($this->request->post[$key]) || (int)$this->request->post[$key] < 1) {
                $this->error['image'] = $this->language->get('error_image');
            }
        }

        return !$this->error;
    }
}
