<?php
class ControllerExtensionModuleProbgBlog extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/probg_blog');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('setting/module');
        $this->load->model('extension/module/probg_blog');
        $this->load->model('extension/probg_blog/category');
        $this->load->model('extension/probg_blog/article');

        // Existing installations upgraded from older versions may not yet have
        // permissions for the integrated category/article CRUD routes.
        // Grant them only to a group that can already modify ProBG Blog.
        if ($this->user->hasPermission('modify', 'extension/module/probg_blog')) {
            $this->grantPermissions();
        }

        if ($this->config->get('module_probg_blog_version') !== '1.6.1') {
            $this->model_extension_module_probg_blog->migrate();
            $this->ensureModuleInstances();
            $settings = $this->model_setting_setting->getSetting('module_probg_blog');
            $settings['module_probg_blog_version'] = '1.6.1';
            $this->model_setting_setting->editSetting('module_probg_blog', $settings);
        } else {
            $this->ensureModuleInstances();
        }

        if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
            $post = $this->request->post;
            $post['module_probg_blog_list_display'] = isset($post['module_probg_blog_list_display']) && $post['module_probg_blog_list_display'] === 'list' ? 'list' : 'grid';
            $category_nav_limit = isset($post['module_probg_blog_category_nav_description_limit']) ? (int)$post['module_probg_blog_category_nav_description_limit'] : 160;
            if ($category_nav_limit < 1) $category_nav_limit = 160;
            $post['module_probg_blog_category_nav_description_limit'] = min(1000, $category_nav_limit);

            if (!empty($post['module_probg_blog_description']) && is_array($post['module_probg_blog_description'])) {
                foreach ($post['module_probg_blog_description'] as &$description) {
                    if (empty($description['meta_title'])) {
                        $description['meta_title'] = isset($description['title']) ? $description['title'] : '';
                    }
                }
                unset($description);
            }

            $menus = isset($post['probg_blog_menus']) && is_array($post['probg_blog_menus']) ? $post['probg_blog_menus'] : array();
            unset($post['probg_blog_menus']);
            $menus = $this->syncMenuInstances($menus);
            $this->mirrorLegacyMenuSettings($post, $menus);
            $post['module_probg_blog_instances_migrated'] = 1;

            $post['module_probg_blog_version'] = '1.6.1';
            $this->model_setting_setting->editSetting('module_probg_blog', $post);
            $this->model_extension_module_probg_blog->saveSectionSeo(isset($post['module_probg_blog_description']) ? $post['module_probg_blog_description'] : array());
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/probg_blog', 'user_token=' . $this->session->data['user_token'], true));
        }

        $keys = array(
            'heading_title', 'text_home', 'text_extension', 'text_success', 'text_edit', 'text_enabled', 'text_disabled',
            'text_categories', 'text_articles', 'text_settings', 'text_version', 'text_stage', 'text_stage_info',
            'text_general', 'text_content', 'text_images', 'text_integrations', 'text_menu', 'text_date', 'text_sort_order',
            'text_layout_articles', 'text_layout_menu', 'text_all_categories', 'text_menu_list', 'text_menu_slider', 'text_grid', 'text_list',
            'entry_status', 'entry_sort', 'entry_limit', 'entry_list_display', 'entry_category_nav_description_status', 'entry_category_nav_description_limit', 'entry_title', 'entry_description', 'entry_meta_title',
            'entry_meta_description', 'entry_meta_keyword', 'entry_seo_keyword', 'entry_default_image',
            'entry_image_list', 'entry_image_article', 'entry_image_gallery', 'entry_sitemap', 'entry_cache',
            'entry_layout_output', 'entry_menu_title', 'entry_menu_show_blog', 'entry_menu_show_categories',
            'entry_menu_show_articles', 'entry_menu_category', 'entry_menu_limit', 'entry_menu_sort', 'entry_menu_name', 'entry_menu_status',
            'entry_menu_display', 'entry_slider_items', 'entry_items_desktop', 'entry_items_tablet', 'entry_items_mobile', 'entry_slider_autoplay', 'entry_slider_interval',
            'help_seo_keyword', 'help_layout_output', 'help_menu_category', 'help_multiple_menus', 'help_slider_items', 'help_items_per_view', 'help_category_nav_description_limit', 'button_save', 'button_cancel',
            'button_add_menu', 'button_remove_menu',
            'button_categories', 'button_articles'
        );

        foreach ($keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_limit'] = isset($this->error['limit']) ? $this->error['limit'] : '';
        $data['error_category_nav_description_limit'] = isset($this->error['category_nav_description_limit']) ? $this->error['category_nav_description_limit'] : '';
        $data['error_menu_limit'] = isset($this->error['menu_limit']) ? $this->error['menu_limit'] : array();
        $data['error_menu_name'] = isset($this->error['menu_name']) ? $this->error['menu_name'] : array();
        $data['error_slider_items'] = isset($this->error['slider_items']) ? $this->error['slider_items'] : array();
        $data['error_items'] = isset($this->error['items']) ? $this->error['items'] : array();
        $data['error_slider_interval'] = isset($this->error['slider_interval']) ? $this->error['slider_interval'] : array();
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
        $data['stage'] = '29';
        $data['version'] = '1.6.1';

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


        $defaults = array(
            'module_probg_blog_status'=>0,
            'module_probg_blog_sort'=>'date',
            'module_probg_blog_limit'=>12,
            'module_probg_blog_list_display'=>'grid',
            'module_probg_blog_category_nav_description_status'=>1,
            'module_probg_blog_category_nav_description_limit'=>160,
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

        $menu_rows = isset($this->request->post['probg_blog_menus']) && is_array($this->request->post['probg_blog_menus'])
            ? $this->request->post['probg_blog_menus']
            : $this->getMenuInstances();
        $data['probg_blog_menus'] = array();
        foreach ($menu_rows as $row => $menu) {
            $module_id = isset($menu['module_id']) ? (int)$menu['module_id'] : 0;
            $data['probg_blog_menus'][] = $this->normalizeMenu($menu, $module_id);
        }

        $this->load->model('tool/image');
        $image = $data['module_probg_blog_default_image'];
        $data['thumb'] = ($image && is_file(DIR_IMAGE . $image)) ? $this->model_tool_image->resize($image, 100, 100) : $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->document->addStyle('view/javascript/summernote/summernote.css');
        $this->document->addStyle('view/stylesheet/probg_blog.css');
        $this->document->addStyle('view/stylesheet/probg_blog_menu_manager.css');
        $this->document->addScript('view/javascript/probg_blog_menu_manager.js');
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
        $this->load->model('setting/module');
        $settings = $this->model_setting_setting->getSetting('module_probg_blog');
        $settings['module_probg_blog_layout_output'] = 'articles';
        $settings['module_probg_blog_menu_description'] = array();
        $settings['module_probg_blog_menu_show_blog'] = 1;
        $settings['module_probg_blog_menu_show_categories'] = 1;
        $settings['module_probg_blog_menu_show_articles'] = 1;
        $settings['module_probg_blog_menu_category_id'] = 0;
        $settings['module_probg_blog_menu_limit'] = 10;
        $settings['module_probg_blog_menu_sort'] = 'date';
        $settings['module_probg_blog_version'] = '1.6.1';
        $this->model_setting_setting->editSetting('module_probg_blog', $settings);
        $this->ensureModuleInstances();
        $this->grantPermissions();
    }

    private function grantPermissions() {
        $this->load->model('user/user_group');

        $group_id = $this->user->getGroupId();
        $routes = array(
            'extension/module/probg_blog',
            'extension/probg_blog',
            'extension/probg_blog/category',
            'extension/probg_blog/article'
        );

        foreach ($routes as $route) {
            $this->model_user_user_group->addPermission($group_id, 'access', $route);
            $this->model_user_user_group->addPermission($group_id, 'modify', $route);
        }
    }

    public function uninstall() {
        $this->load->model('setting/module');
        foreach ($this->model_setting_module->getModulesByCode('probg_blog') as $module) {
            $module_id = isset($module['module_id']) ? (int)$module['module_id'] : 0;
            if ($module_id > 0) {
                $this->cleanupLayoutModuleReference($module_id);
                $this->model_setting_module->deleteModule($module_id);
            }
        }
        $this->load->model('extension/module/probg_blog');
        $this->model_extension_module_probg_blog->uninstall();
    }

    private function getMenuInstances() {
        $menus = array();
        foreach ($this->model_setting_module->getModulesByCode('probg_blog') as $row) {
            $setting = json_decode($row['setting'], true);
            if (!is_array($setting) || !isset($setting['probg_blog_type']) || $setting['probg_blog_type'] !== 'menu') {
                continue;
            }
            $menus[] = $this->normalizeMenu($setting, (int)$row['module_id']);
        }
        return $menus;
    }

    private function normalizeMenu($menu, $module_id = 0) {
        $defaults = array(
            'name' => 'ProBG Blog Menu',
            'probg_blog_type' => 'menu',
            'menu_description' => array(),
            'show_blog' => 1,
            'show_categories' => 1,
            'show_articles' => 1,
            'category_id' => 0,
            'limit' => 10,
            'sort' => 'date',
            'display' => 'list',
            'items_desktop' => 0,
            'items_tablet' => 0,
            'items_mobile' => 0,
            'slider_items' => 3,
            'slider_autoplay' => 1,
            'slider_interval' => 5000,
            'status' => 1
        );

        $menu = array_merge($defaults, is_array($menu) ? $menu : array());
        $menu['module_id'] = $module_id ?: (isset($menu['module_id']) ? (int)$menu['module_id'] : 0);
        $menu['name'] = trim((string)$menu['name']);
        if ($menu['name'] === '') $menu['name'] = 'ProBG Blog Menu';
        $menu['probg_blog_type'] = 'menu';
        $menu['menu_description'] = is_array($menu['menu_description']) ? $menu['menu_description'] : array();
        $menu['show_blog'] = !empty($menu['show_blog']) ? 1 : 0;
        $menu['show_categories'] = !empty($menu['show_categories']) ? 1 : 0;
        $menu['show_articles'] = !empty($menu['show_articles']) ? 1 : 0;
        $menu['category_id'] = max(0, (int)$menu['category_id']);
        $menu['limit'] = max(1, min(100, (int)$menu['limit']));
        $menu['sort'] = $menu['sort'] === 'sort_order' ? 'sort_order' : 'date';
        $menu['display'] = $menu['display'] === 'slider' ? 'slider' : 'list';

        $legacy_items = max(1, min(6, (int)$menu['slider_items']));
        $menu['items_desktop'] = (int)$menu['items_desktop'] > 0 ? max(1, min(6, (int)$menu['items_desktop'])) : $legacy_items;
        $menu['items_tablet'] = (int)$menu['items_tablet'] > 0 ? max(1, min(6, (int)$menu['items_tablet'])) : min(2, $menu['items_desktop']);
        $menu['items_mobile'] = (int)$menu['items_mobile'] > 0 ? max(1, min(6, (int)$menu['items_mobile'])) : 1;
        // Keep the legacy key in sync so older templates/installations continue to render safely.
        $menu['slider_items'] = $menu['items_desktop'];

        $menu['slider_autoplay'] = !empty($menu['slider_autoplay']) ? 1 : 0;
        $menu['slider_interval'] = max(1000, min(30000, (int)$menu['slider_interval']));
        $menu['status'] = !empty($menu['status']) ? 1 : 0;
        return $menu;
    }

    private function syncMenuInstances($menus) {
        $existing = array();
        foreach ($this->getMenuInstances() as $menu) {
            $existing[(int)$menu['module_id']] = $menu;
        }

        $keep = array();
        $saved = array();
        foreach ((array)$menus as $menu) {
            $module_id = isset($menu['module_id']) ? (int)$menu['module_id'] : 0;
            $normalized = $this->normalizeMenu($menu, $module_id);
            $data = $normalized;
            unset($data['module_id']);

            if ($module_id > 0 && isset($existing[$module_id])) {
                $this->model_setting_module->editModule($module_id, $data);
            } else {
                $this->model_setting_module->addModule('probg_blog', $data);
                $module_id = (int)$this->db->getLastId();
                $normalized['module_id'] = $module_id;
            }

            if ($module_id > 0) $keep[$module_id] = true;
            $saved[] = $normalized;
        }

        foreach ($existing as $module_id => $menu) {
            if (!isset($keep[$module_id])) {
                $this->cleanupLayoutModuleReference($module_id);
                $this->model_setting_module->deleteModule($module_id);
            }
        }
        return $saved;
    }

    private function ensureModuleInstances() {
        $settings = $this->model_setting_setting->getSetting('module_probg_blog');
        $article_module_id = 0;
        $menu_module_id = 0;

        foreach ($this->model_setting_module->getModulesByCode('probg_blog') as $module) {
            $module_id = isset($module['module_id']) ? (int)$module['module_id'] : 0;
            $module_setting = isset($module['setting']) ? json_decode($module['setting'], true) : array();
            if (!is_array($module_setting)) $module_setting = array();

            if (!$article_module_id && isset($module_setting['probg_blog_type']) && $module_setting['probg_blog_type'] === 'articles') {
                $article_module_id = $module_id;
            }
            if (!$menu_module_id && isset($module_setting['probg_blog_type']) && $module_setting['probg_blog_type'] === 'menu') {
                $menu_module_id = $module_id;
            }
        }

        if (!$article_module_id) {
            $this->model_setting_module->addModule('probg_blog', array(
                'name' => 'ProBG Blog - Latest Articles',
                'probg_blog_type' => 'articles',
                'limit' => 4,
                'status' => 1
            ));
            $article_module_id = (int)$this->db->getLastId();
        }

        $migrated = !empty($settings['module_probg_blog_instances_migrated']);
        if (!$migrated) {
            if (!$menu_module_id) {
                $legacy = array(
                    'name' => 'ProBG Blog Menu',
                    'probg_blog_type' => 'menu',
                    'menu_description' => isset($settings['module_probg_blog_menu_description']) && is_array($settings['module_probg_blog_menu_description']) ? $settings['module_probg_blog_menu_description'] : array(),
                    'show_blog' => isset($settings['module_probg_blog_menu_show_blog']) ? (int)$settings['module_probg_blog_menu_show_blog'] : 1,
                    'show_categories' => isset($settings['module_probg_blog_menu_show_categories']) ? (int)$settings['module_probg_blog_menu_show_categories'] : 1,
                    'show_articles' => isset($settings['module_probg_blog_menu_show_articles']) ? (int)$settings['module_probg_blog_menu_show_articles'] : 1,
                    'category_id' => isset($settings['module_probg_blog_menu_category_id']) ? (int)$settings['module_probg_blog_menu_category_id'] : 0,
                    'limit' => isset($settings['module_probg_blog_menu_limit']) ? (int)$settings['module_probg_blog_menu_limit'] : 10,
                    'sort' => isset($settings['module_probg_blog_menu_sort']) ? $settings['module_probg_blog_menu_sort'] : 'date',
                    'display' => 'list',
                    'items_desktop' => 3,
                    'items_tablet' => 2,
                    'items_mobile' => 1,
                    'slider_items' => 3,
                    'slider_autoplay' => 1,
                    'slider_interval' => 5000,
                    'status' => 1
                );
                $legacy = $this->normalizeMenu($legacy);
                unset($legacy['module_id']);
                $this->model_setting_module->addModule('probg_blog', $legacy);
                $menu_module_id = (int)$this->db->getLastId();
            }

            $target_module_id = isset($settings['module_probg_blog_layout_output']) && $settings['module_probg_blog_layout_output'] === 'menu'
                ? $menu_module_id
                : $article_module_id;

            if ($target_module_id > 0) {
                $column = $this->layoutModuleColumn();
                if ($column !== '') {
                    $this->db->query("UPDATE `" . DB_PREFIX . "layout_module` SET `" . $column . "`='probg_blog." . (int)$target_module_id . "' WHERE `" . $column . "`='probg_blog'");
                }
            }

            $settings['module_probg_blog_instances_migrated'] = 1;
            $this->model_setting_setting->editSetting('module_probg_blog', $settings);
        }
    }

    private function mirrorLegacyMenuSettings(&$post, $menus) {
        if ($menus) {
            $first = $this->normalizeMenu(reset($menus), isset($menus[0]['module_id']) ? (int)$menus[0]['module_id'] : 0);
            $post['module_probg_blog_menu_description'] = $first['menu_description'];
            $post['module_probg_blog_menu_show_blog'] = $first['show_blog'];
            $post['module_probg_blog_menu_show_categories'] = $first['show_categories'];
            $post['module_probg_blog_menu_show_articles'] = $first['show_articles'];
            $post['module_probg_blog_menu_category_id'] = $first['category_id'];
            $post['module_probg_blog_menu_limit'] = $first['limit'];
            $post['module_probg_blog_menu_sort'] = $first['sort'];
        } else {
            $post['module_probg_blog_menu_description'] = array();
            $post['module_probg_blog_menu_show_blog'] = 0;
            $post['module_probg_blog_menu_show_categories'] = 0;
            $post['module_probg_blog_menu_show_articles'] = 0;
            $post['module_probg_blog_menu_category_id'] = 0;
            $post['module_probg_blog_menu_limit'] = 10;
            $post['module_probg_blog_menu_sort'] = 'date';
        }
    }

    private function cleanupLayoutModuleReference($module_id) {
        $column = $this->layoutModuleColumn();
        if ($column !== '') {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `" . $column . "`='probg_blog." . (int)$module_id . "'");
        }
    }

    private function layoutModuleColumn() {
        $code = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "layout_module` LIKE 'code'");
        if ($code->num_rows) return 'code';
        $module = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "layout_module` LIKE 'module'");
        return $module->num_rows ? 'module' : '';
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/probg_blog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $descriptions = isset($this->request->post['module_probg_blog_description']) ? $this->request->post['module_probg_blog_description'] : array();
        $menus = isset($this->request->post['probg_blog_menus']) && is_array($this->request->post['probg_blog_menus']) ? $this->request->post['probg_blog_menus'] : array();
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            $language_id = (int)$language['language_id'];
            $value = isset($descriptions[$language_id]) ? $descriptions[$language_id] : array();
            if (isset($value['title']) && utf8_strlen($value['title']) > 255) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }
            $keyword = isset($value['seo_keyword']) ? trim($value['seo_keyword']) : '';
            if ($keyword !== '' && !preg_match('/^[A-Za-z0-9_-]+$/', $keyword)) {
                $this->error['seo_keyword'][$language_id] = $this->language->get('error_seo_keyword');
            }
        }

        foreach ($menus as $row => $menu) {
            $name = isset($menu['name']) ? trim($menu['name']) : '';
            if ($name === '' || utf8_strlen($name) > 64) {
                $this->error['menu_name'][$row] = $this->language->get('error_menu_name');
            }
            foreach ($languages as $language) {
                $language_id = (int)$language['language_id'];
                $title = isset($menu['menu_description'][$language_id]['title']) ? $menu['menu_description'][$language_id]['title'] : '';
                if (utf8_strlen($title) > 255) {
                    $this->error['menu_title'][$row][$language_id] = $this->language->get('error_menu_title');
                }
            }
            $menu_limit = isset($menu['limit']) ? (int)$menu['limit'] : 0;
            if ($menu_limit < 1 || $menu_limit > 100) {
                $this->error['menu_limit'][$row] = $this->language->get('error_menu_limit');
            }
            foreach (array('desktop', 'tablet', 'mobile') as $device) {
                $key = 'items_' . $device;
                $items = isset($menu[$key]) ? (int)$menu[$key] : 0;
                if ($items < 1 || $items > 6) {
                    $this->error['items'][$row][$device] = $this->language->get('error_items');
                }
            }
            $slider_interval = isset($menu['slider_interval']) ? (int)$menu['slider_interval'] : 0;
            if ($slider_interval < 1000 || $slider_interval > 30000) {
                $this->error['slider_interval'][$row] = $this->language->get('error_slider_interval');
            }
        }

        $limit = isset($this->request->post['module_probg_blog_limit']) ? (int)$this->request->post['module_probg_blog_limit'] : 0;
        if ($limit < 1 || $limit > 100) {
            $this->error['limit'] = $this->language->get('error_limit');
        }

        $category_nav_description_status = !empty($this->request->post['module_probg_blog_category_nav_description_status']);
        $category_nav_description_limit = isset($this->request->post['module_probg_blog_category_nav_description_limit']) ? (int)$this->request->post['module_probg_blog_category_nav_description_limit'] : 0;
        if ($category_nav_description_status && ($category_nav_description_limit < 1 || $category_nav_description_limit > 1000)) {
            $this->error['category_nav_description_limit'] = $this->language->get('error_category_nav_description_limit');
        }

        foreach (array('module_probg_blog_image_list_width','module_probg_blog_image_list_height','module_probg_blog_image_article_width','module_probg_blog_image_article_height','module_probg_blog_image_gallery_width','module_probg_blog_image_gallery_height') as $key) {
            if (empty($this->request->post[$key]) || (int)$this->request->post[$key] < 1) {
                $this->error['image'] = $this->language->get('error_image');
            }
        }

        return !$this->error;
    }
}
