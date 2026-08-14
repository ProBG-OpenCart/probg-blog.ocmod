<?php
class ControllerExtensionProbgBlogArticle extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/probg_blog/article');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/probg_blog/article');
        $this->getList();
    }

    public function add() {
        $this->load->language('extension/probg_blog/article');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/probg_blog/article');

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validateForm()) {
            $this->model_extension_probg_blog_article->addArticle($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'] . $this->listUrl(), true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/probg_blog/article');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/probg_blog/article');

        $article_id = isset($this->request->get['article_id']) ? (int)$this->request->get['article_id'] : 0;

        if (!$article_id || !$this->model_extension_probg_blog_article->getArticle($article_id)) {
            $this->response->redirect($this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'], true));
            return;
        }

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validateForm()) {
            $this->model_extension_probg_blog_article->editArticle($article_id, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'] . $this->listUrl(), true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/probg_blog/article');
        $this->load->model('extension/probg_blog/article');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ((array)$this->request->post['selected'] as $article_id) {
                $this->model_extension_probg_blog_article->deleteArticle((int)$article_id);
            }

            $this->session->data['success'] = $this->language->get('text_success_delete');
            $this->response->redirect($this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'] . $this->listUrl(), true));
        }

        $this->getList();
    }

    protected function getList() {
        $filter_title = isset($this->request->get['filter_title']) ? $this->request->get['filter_title'] : '';
        $filter_category_id = isset($this->request->get['filter_category_id']) ? $this->request->get['filter_category_id'] : '';
        $filter_status = isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : '';
        $filter_date_added_from = isset($this->request->get['filter_date_added_from']) ? $this->request->get['filter_date_added_from'] : '';
        $filter_date_added_to = isset($this->request->get['filter_date_added_to']) ? $this->request->get['filter_date_added_to'] : '';
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'a.date_added';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

        $limit = (int)$this->config->get('config_limit_admin');
        if ($limit < 1) {
            $limit = 20;
        }

        $filter_data = array(
            'filter_title' => $filter_title,
            'filter_category_id' => $filter_category_id,
            'filter_status' => $filter_status,
            'filter_date_added_from' => $filter_date_added_from,
            'filter_date_added_to' => $filter_date_added_to,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $article_total = $this->model_extension_probg_blog_article->getTotalArticles($filter_data);
        $results = $this->model_extension_probg_blog_article->getArticles($filter_data);

        $this->load->model('tool/image');
        $data['articles'] = array();

        foreach ($results as $result) {
            if (!empty($result['image']) && is_file(DIR_IMAGE . $result['image'])) {
                $thumb = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $thumb = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            $data['articles'][] = array(
                'article_id' => $result['article_id'],
                'thumb' => $thumb,
                'title' => $result['title'],
                'category' => $result['category'],
                'sort_order' => $result['sort_order'],
                'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                'edit' => $this->url->link('extension/probg_blog/article/edit', 'user_token=' . $this->session->data['user_token'] . '&article_id=' . (int)$result['article_id'] . $this->listUrl(), true)
            );
        }

        $this->baseData($data);

        foreach (array(
            'text_list', 'text_no_results', 'text_confirm', 'text_all', 'text_enabled', 'text_disabled',
            'column_image', 'column_id', 'column_title', 'column_category', 'column_sort_order', 'column_status',
            'column_date_added', 'column_date_modified', 'column_action', 'entry_title', 'entry_category',
            'entry_status', 'entry_date_added_from', 'entry_date_added_to', 'button_add', 'button_delete',
            'button_edit', 'button_filter'
        ) as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['add'] = $this->url->link('extension/probg_blog/article/add', 'user_token=' . $this->session->data['user_token'] . $this->listUrl(), true);
        $data['delete'] = $this->url->link('extension/probg_blog/article/delete', 'user_token=' . $this->session->data['user_token'] . $this->listUrl(), true);
        $data['filter_title'] = $filter_title;
        $data['filter_category_id'] = $filter_category_id;
        $data['filter_status'] = $filter_status;
        $data['filter_date_added_from'] = $filter_date_added_from;
        $data['filter_date_added_to'] = $filter_date_added_to;
        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['selected'] = isset($this->request->post['selected']) ? (array)$this->request->post['selected'] : array();

        $this->load->model('extension/probg_blog/category');
        $data['categories'] = $this->model_extension_probg_blog_category->getCategories(array('sort' => 'cd.title', 'order' => 'ASC'));

        $pagination = new Pagination();
        $pagination->total = $article_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'] . $this->listUrl(array('page')) . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $start = $article_total ? (($page - 1) * $limit) + 1 : 0;
        $end = min($article_total, $page * $limit);
        $data['results'] = sprintf($this->language->get('text_pagination'), $start, $end, $article_total, ceil($article_total / $limit));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/probg_blog/article_list', $data));
    }

    protected function getForm() {
        $data = array();
        $this->baseData($data);

        foreach (array(
            'text_add', 'text_edit', 'text_content', 'text_data', 'text_images', 'text_main_image',
            'text_additional_images', 'text_enabled', 'text_disabled', 'text_automatic', 'entry_title',
            'entry_short_description', 'entry_description', 'entry_meta_title', 'entry_meta_description',
            'entry_meta_keyword', 'entry_seo_keyword', 'entry_category', 'entry_image', 'entry_sort_order',
            'entry_status', 'entry_date_added', 'entry_date_modified', 'help_seo_keyword', 'button_save',
            'button_cancel', 'button_add_image', 'button_remove'
        ) as $key) {
            $data[$key] = $this->language->get($key);
        }

        $article_id = isset($this->request->get['article_id']) ? (int)$this->request->get['article_id'] : 0;
        $data['text_form'] = $article_id ? $this->language->get('text_edit') : $this->language->get('text_add');
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_title'] = isset($this->error['title']) ? $this->error['title'] : array();
        $data['error_seo_keyword'] = isset($this->error['seo_keyword']) ? $this->error['seo_keyword'] : array();
        $data['error_category'] = isset($this->error['category']) ? $this->error['category'] : '';

        $data['action'] = $this->url->link(
            $article_id ? 'extension/probg_blog/article/edit' : 'extension/probg_blog/article/add',
            'user_token=' . $this->session->data['user_token'] . ($article_id ? '&article_id=' . $article_id : '') . $this->listUrl(),
            true
        );
        $data['cancel'] = $this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'] . $this->listUrl(), true);

        $article_info = $article_id ? $this->model_extension_probg_blog_article->getArticle($article_id) : array();

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['article_description'] = isset($this->request->post['article_description'])
            ? $this->request->post['article_description']
            : ($article_id ? $this->model_extension_probg_blog_article->getArticleDescriptions($article_id) : array());

        $this->load->model('extension/probg_blog/category');
        $data['categories'] = $this->model_extension_probg_blog_category->getCategories(array('sort' => 'cd.title', 'order' => 'ASC'));
        $data['category_id'] = isset($this->request->post['category_id'])
            ? (int)$this->request->post['category_id']
            : (isset($article_info['category_id']) ? (int)$article_info['category_id'] : 0);

        $data['image'] = isset($this->request->post['image'])
            ? $this->request->post['image']
            : (isset($article_info['image']) ? $article_info['image'] : '');

        $this->load->model('tool/image');
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['thumb'] = ($data['image'] && is_file(DIR_IMAGE . $data['image']))
            ? $this->model_tool_image->resize($data['image'], 100, 100)
            : $data['placeholder'];

        $images = isset($this->request->post['article_image'])
            ? $this->request->post['article_image']
            : ($article_id ? $this->model_extension_probg_blog_article->getArticleImages($article_id) : array());

        $data['article_images'] = array();
        foreach ($images as $image_info) {
            $image = isset($image_info['image']) ? $image_info['image'] : '';
            $data['article_images'][] = array(
                'image' => $image,
                'thumb' => ($image && is_file(DIR_IMAGE . $image)) ? $this->model_tool_image->resize($image, 100, 100) : $data['placeholder'],
                'sort_order' => isset($image_info['sort_order']) ? (int)$image_info['sort_order'] : 0
            );
        }

        $data['sort_order'] = isset($this->request->post['sort_order'])
            ? (int)$this->request->post['sort_order']
            : (isset($article_info['sort_order']) ? (int)$article_info['sort_order'] : 0);
        $data['status'] = isset($this->request->post['status'])
            ? (int)$this->request->post['status']
            : (isset($article_info['status']) ? (int)$article_info['status'] : 1);
        $data['date_added'] = isset($article_info['date_added'])
            ? date($this->language->get('datetime_format'), strtotime($article_info['date_added']))
            : $this->language->get('text_automatic');
        $data['date_modified'] = isset($article_info['date_modified'])
            ? date($this->language->get('datetime_format'), strtotime($article_info['date_modified']))
            : $this->language->get('text_automatic');

        $data['summernote'] = $this->config->get('config_language');
        $this->document->addStyle('view/javascript/summernote/summernote.css');
        $this->document->addScript('view/javascript/summernote/summernote.js');
        $this->document->addScript('view/javascript/summernote/opencart.js');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/probg_blog/article_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/probg_blog/article')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('extension/probg_blog/category');
        $category_id = isset($this->request->post['category_id']) ? (int)$this->request->post['category_id'] : 0;
        if (!$category_id || !$this->model_extension_probg_blog_category->getCategory($category_id)) {
            $this->error['category'] = $this->language->get('error_category');
        }

        $this->load->model('localisation/language');
        $article_id = isset($this->request->get['article_id']) ? (int)$this->request->get['article_id'] : 0;

        foreach ($this->model_localisation_language->getLanguages() as $language) {
            $language_id = (int)$language['language_id'];
            $value = isset($this->request->post['article_description'][$language_id])
                ? $this->request->post['article_description'][$language_id]
                : array();
            $title = isset($value['title']) ? trim($value['title']) : '';

            if (utf8_strlen($title) < 1 || utf8_strlen($title) > 255) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }

            $keyword = isset($value['seo_keyword']) ? trim($value['seo_keyword']) : '';
            if ($keyword !== '' && (!preg_match('/^[\p{L}\p{N}_-]+$/u', $keyword) || $this->model_extension_probg_blog_article->getSeoUrlByKeyword($keyword, $language_id, $article_id))) {
                $this->error['seo_keyword'][$language_id] = $this->language->get('error_seo_keyword_exists');
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/probg_blog/article')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    private function baseData(&$data) {
        $data['heading_title'] = $this->language->get('heading_title');
        $data['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            ),
            array(
                'text' => $this->language->get('text_blog'),
                'href' => $this->url->link('extension/module/probg_blog', 'user_token=' . $this->session->data['user_token'], true)
            ),
            array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'], true)
            )
        );

        $data['settings_url'] = $this->url->link('extension/module/probg_blog', 'user_token=' . $this->session->data['user_token'], true);
        $data['categories_url'] = $this->url->link('extension/probg_blog/category', 'user_token=' . $this->session->data['user_token'], true);
        $data['articles_url'] = $this->url->link('extension/probg_blog/article', 'user_token=' . $this->session->data['user_token'], true);
        $data['tab_settings'] = $this->language->get('tab_settings');
        $data['tab_categories'] = $this->language->get('tab_categories');
        $data['tab_articles'] = $this->language->get('tab_articles');
        $data['user_token'] = $this->session->data['user_token'];
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        unset($this->session->data['success']);
    }

    private function listUrl($exclude = array()) {
        $url = '';

        foreach (array('filter_title', 'filter_category_id', 'filter_status', 'filter_date_added_from', 'filter_date_added_to', 'sort', 'order', 'page') as $parameter) {
            if (!in_array($parameter, $exclude, true) && isset($this->request->get[$parameter]) && $this->request->get[$parameter] !== '') {
                $url .= '&' . $parameter . '=' . urlencode($this->request->get[$parameter]);
            }
        }

        return $url;
    }
}
