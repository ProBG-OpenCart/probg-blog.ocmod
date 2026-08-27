<?php
class ControllerExtensionModuleProbgBlog extends Controller {
    private static $full_page_rendering = false;
    private static $menu_counter = 0;

    public function index($setting=array()) {
        if (!$this->config->get('module_probg_blog_status')) return '';
        $this->load->language('extension/module/probg_blog');
        $this->load->model('extension/probg_blog/blog');
        $this->load->model('tool/image');

        $category_id = isset($this->request->get['probg_blog_category_id']) ? (int)$this->request->get['probg_blog_category_id'] : 0;
        $article_id = isset($this->request->get['probg_blog_article_id']) ? (int)$this->request->get['probg_blog_article_id'] : 0;
        $route = isset($this->request->get['route']) ? (string)$this->request->get['route'] : '';
        $is_blog_request = ($route === 'extension/module/probg_blog' || $category_id > 0 || $article_id > 0);

        if (!$is_blog_request || self::$full_page_rendering) {
            return $this->module(is_array($setting) ? $setting : array());
        }

        self::$full_page_rendering = true;

        if ($article_id) {
            $output = $this->article($article_id, $category_id);
        } elseif ($category_id) {
            $output = $this->category($category_id);
        } else {
            $output = $this->listing();
        }

        self::$full_page_rendering = false;
        return $output;
    }

    private function listing() {
        $page = max(1, isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1);
        $limit = max(1, (int)$this->config->get('module_probg_blog_limit'));
        $desc = $this->section();
        $title = !empty($desc['title']) ? $desc['title'] : $this->language->get('heading_title');
        $this->meta($desc, $title);
        $canonical = $this->url->link('extension/module/probg_blog', $page > 1 ? 'page=' . $page : '', true);
        $this->document->addLink($canonical, 'canonical');
        $this->social($title, isset($desc['meta_description']) ? $desc['meta_description'] : '', $canonical, 'website', '');

        $data = $this->layoutData();
        $data['heading_title'] = $title;
        $data['description'] = isset($desc['description']) ? html_entity_decode($desc['description'], ENT_QUOTES, 'UTF-8') : '';
        $data['text_categories'] = $this->language->get('text_categories');
        $data['text_articles'] = $this->language->get('text_articles');
        $data['text_read_more'] = $this->language->get('text_read_more');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['breadcrumbs'] = $this->breadcrumbs();
        $data['categories'] = array();
        foreach ($this->model_extension_probg_blog_blog->getCategories() as $category) {
            $data['categories'][] = array(
                'title'=>$category['title'],
                'description'=>html_entity_decode($category['description'], ENT_QUOTES, 'UTF-8'),
                'article_count'=>$category['article_count'],
                'href'=>$this->url->link('extension/module/probg_blog', 'probg_blog_category_id=' . (int)$category['category_id'], true)
            );
        }
        $total = $this->model_extension_probg_blog_blog->getTotalArticles();
        $data['articles'] = $this->articleCards($this->model_extension_probg_blog_blog->getArticles(array('start'=>($page-1)*$limit,'limit'=>$limit)));
        $this->pagination($data, $total, $page, $limit, '');
        return $this->render('probg_blog_list', $data);
    }

    private function category($id) {
        $category = $this->model_extension_probg_blog_blog->getCategory($id);
        if (!$category) return $this->notFound();
        $layout_id = $this->model_extension_probg_blog_blog->getCategoryLayoutId($id);
        if ($layout_id) $this->config->set('config_layout_id', $layout_id);
        $page = max(1, isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1);
        $limit = max(1, (int)$this->config->get('module_probg_blog_limit'));
        $this->meta($category, $category['title']);
        $canonical = $this->url->link('extension/module/probg_blog', 'probg_blog_category_id=' . $id . ($page > 1 ? '&page=' . $page : ''), true);
        $this->document->addLink($canonical, 'canonical');
        $this->social($category['title'], $category['meta_description'], $canonical, 'website', '');

        $data = $this->layoutData();
        $data['heading_title'] = $category['title'];
        $data['description'] = html_entity_decode($category['description'], ENT_QUOTES, 'UTF-8');
        $data['text_read_more'] = $this->language->get('text_read_more');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['breadcrumbs'] = $this->breadcrumbs(array(array('text'=>$category['title'],'href'=>$this->url->link('extension/module/probg_blog','probg_blog_category_id='.$id,true))));
        $total = $this->model_extension_probg_blog_blog->getTotalArticles($id);
        $data['articles'] = $this->articleCards($this->model_extension_probg_blog_blog->getArticles(array('category_id'=>$id,'start'=>($page-1)*$limit,'limit'=>$limit)));
        $this->pagination($data, $total, $page, $limit, 'probg_blog_category_id=' . $id);
        return $this->render('probg_blog_category', $data);
    }

    private function article($id, $requested_category=0) {
        $article = $this->model_extension_probg_blog_blog->getArticle($id);
        if (!$article) return $this->notFound();
        $layout_id = $this->model_extension_probg_blog_blog->getCategoryLayoutId((int)$article['category_id']);
        if ($layout_id) $this->config->set('config_layout_id', $layout_id);
        $canonical = $this->url->link('extension/module/probg_blog', 'probg_blog_category_id=' . (int)$article['category_id'] . '&probg_blog_article_id=' . $id, true);
        if ($requested_category && $requested_category != (int)$article['category_id']) {
            $this->response->redirect($canonical, 301);
            return '';
        }
        $this->meta($article, $article['title']);
        $this->document->addLink($canonical, 'canonical');
        $image = $this->image($article['image'], 1200, 630);
        $this->social($article['title'], $article['meta_description'], $canonical, 'article', $image, $article);

        $data = $this->layoutData();
        $data['heading_title'] = $article['title'];
        $data['short_description'] = html_entity_decode($article['short_description'], ENT_QUOTES, 'UTF-8');
        $data['description'] = html_entity_decode($article['description'], ENT_QUOTES, 'UTF-8');
        $data['image'] = $this->image($article['image'], max(1,(int)$this->config->get('module_probg_blog_image_article_width')), max(1,(int)$this->config->get('module_probg_blog_image_article_height')));
        $data['date_added'] = date($this->language->get('date_format_short'), strtotime($article['date_added']));
        $data['date_modified'] = date($this->language->get('date_format_short'), strtotime($article['date_modified']));
        $data['text_category'] = $this->language->get('text_category');
        $data['text_published'] = $this->language->get('text_published');
        $data['text_modified'] = $this->language->get('text_modified');
        $data['text_related'] = $this->language->get('text_related');
        $data['category'] = array('title'=>$article['category_title'],'href'=>$this->url->link('extension/module/probg_blog','probg_blog_category_id='.(int)$article['category_id'],true));
        $data['breadcrumbs'] = $this->breadcrumbs(array($data['category'],array('text'=>$article['title'],'href'=>$canonical)));
        $data['images'] = array();
        foreach ($this->model_extension_probg_blog_blog->getArticleImages($id) as $img) {
            if ($img['image'] && is_file(DIR_IMAGE . $img['image'])) {
                $data['images'][] = array(
                    'thumb'=>$this->model_tool_image->resize($img['image'],max(1,(int)$this->config->get('module_probg_blog_image_gallery_width')),max(1,(int)$this->config->get('module_probg_blog_image_gallery_height'))),
                    'popup'=>HTTP_SERVER . 'image/' . $img['image']
                );
            }
        }
        $data['products'] = $this->relatedProducts($id);
        return $this->render('probg_blog_article', $data);
    }

    private function relatedProducts($article_id) {
        $products = array();
        $this->load->model('catalog/product');
        $theme = $this->config->get('config_theme');
        $width = (int)$this->config->get('theme_' . $theme . '_image_product_width');
        $height = (int)$this->config->get('theme_' . $theme . '_image_product_height');
        $description_length = (int)$this->config->get('theme_' . $theme . '_product_description_length');
        if ($width < 1) $width = 200;
        if ($height < 1) $height = 200;
        if ($description_length < 1) $description_length = 100;
        foreach ($this->model_extension_probg_blog_blog->getArticleRelatedProducts($article_id) as $product_id) {
            $result = $this->model_catalog_product->getProduct($product_id);
            if (!$result) continue;
            $thumb = $result['image'] ? $this->model_tool_image->resize($result['image'],$width,$height) : $this->model_tool_image->resize('placeholder.png',$width,$height);
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) $price = $this->currency->format($this->tax->calculate($result['price'],$result['tax_class_id'],$this->config->get('config_tax')),$this->session->data['currency']); else $price = false;
            if (!is_null($result['special']) && (float)$result['special'] >= 0) {
                $special = $this->currency->format($this->tax->calculate($result['special'],$result['tax_class_id'],$this->config->get('config_tax')),$this->session->data['currency']);
                $tax_price = (float)$result['special'];
            } else {
                $special = false;
                $tax_price = (float)$result['price'];
            }
            $tax = $this->config->get('config_tax') ? $this->currency->format($tax_price,$this->session->data['currency']) : false;
            $rating = $this->config->get('config_review_status') ? (int)$result['rating'] : false;
            $products[] = array(
                'product_id'=>$result['product_id'],
                'thumb'=>$thumb,
                'name'=>$result['name'],
                'description'=>utf8_substr(trim(strip_tags(html_entity_decode($result['description'],ENT_QUOTES,'UTF-8'))),0,$description_length).'..',
                'price'=>$price,
                'special'=>$special,
                'tax'=>$tax,
                'rating'=>$rating,
                'href'=>$this->url->link('product/product','product_id='.$result['product_id'],true)
            );
        }
        return $products;
    }

    private function module($setting) {
        if (is_array($setting) && isset($setting['probg_blog_type']) && $setting['probg_blog_type'] === 'menu') {
            if (isset($setting['status']) && !(int)$setting['status']) return '';
            return $this->menuModule($setting);
        }

        if (is_array($setting) && isset($setting['probg_blog_type']) && $setting['probg_blog_type'] === 'articles') {
            if (isset($setting['status']) && !(int)$setting['status']) return '';
            $limit = max(1, min(100, isset($setting['limit']) ? (int)$setting['limit'] : 4));
            $data['heading_title'] = $this->language->get('heading_title');
            $data['blog_url'] = $this->url->link('extension/module/probg_blog','',true);
            $data['articles'] = $this->articleCards($this->model_extension_probg_blog_blog->getArticles(array('limit'=>$limit)));
            return $this->load->view('extension/module/probg_blog', $data);
        }

        // Since 1.3.0 all layout output is rendered by explicit module instances.
        // A leftover bare probg_blog layout row must not render a second fallback block.
        if ($this->config->get('module_probg_blog_instances_migrated')) {
            return '';
        }

        $mode = $this->config->get('module_probg_blog_layout_output');
        if ($mode === 'menu') {
            return $this->menuModule($this->legacyMenuSettings());
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['blog_url'] = $this->url->link('extension/module/probg_blog','',true);
        $data['articles'] = $this->articleCards($this->model_extension_probg_blog_blog->getArticles(array('limit'=>4)));
        return $this->load->view('extension/module/probg_blog', $data);
    }

    private function legacyMenuSettings() {
        return array(
            'probg_blog_type' => 'menu',
            'menu_description' => (array)$this->config->get('module_probg_blog_menu_description'),
            'show_blog' => (int)$this->config->get('module_probg_blog_menu_show_blog'),
            'show_categories' => (int)$this->config->get('module_probg_blog_menu_show_categories'),
            'show_articles' => (int)$this->config->get('module_probg_blog_menu_show_articles'),
            'category_id' => (int)$this->config->get('module_probg_blog_menu_category_id'),
            'limit' => max(1, (int)$this->config->get('module_probg_blog_menu_limit')),
            'sort' => $this->config->get('module_probg_blog_menu_sort') === 'sort_order' ? 'sort_order' : 'date',
            'display' => 'list',
            'items_desktop' => 3,
            'items_tablet' => 2,
            'items_mobile' => 1,
            'slider_items' => 3,
            'slider_autoplay' => 0,
            'slider_interval' => 5000,
            'status' => 1
        );
    }

    private function menuModule($menu=array()) {
        $language_id = (int)$this->config->get('config_language_id');
        $descriptions = isset($menu['menu_description']) && is_array($menu['menu_description']) ? $menu['menu_description'] : array();
        $title = '';
        if (isset($descriptions[$language_id]['title'])) {
            $title = trim($descriptions[$language_id]['title']);
        }
        if ($title === '') $title = $this->language->get('heading_title');

        $data['heading_title'] = $title;
        $data['text_blog_home'] = $this->language->get('text_blog_home');
        $data['text_categories'] = $this->language->get('text_categories');
        $data['text_articles'] = $this->language->get('text_articles');
        $data['text_previous'] = $this->language->get('text_previous');
        $data['text_next'] = $this->language->get('text_next');
        $data['show_blog'] = isset($menu['show_blog']) && (int)$menu['show_blog'] === 1;
        $data['show_categories'] = isset($menu['show_categories']) && (int)$menu['show_categories'] === 1;
        $data['show_articles'] = isset($menu['show_articles']) && (int)$menu['show_articles'] === 1;
        $data['blog_url'] = $this->url->link('extension/module/probg_blog','',true);
        $data['categories'] = array();
        $data['articles'] = array();
        $data['display'] = isset($menu['display']) && $menu['display'] === 'slider' ? 'slider' : 'list';
        $legacy_items = max(1, min(6, isset($menu['slider_items']) ? (int)$menu['slider_items'] : 3));
        $data['items_desktop'] = max(1, min(6, isset($menu['items_desktop']) && (int)$menu['items_desktop'] > 0 ? (int)$menu['items_desktop'] : $legacy_items));
        $data['items_tablet'] = max(1, min(6, isset($menu['items_tablet']) && (int)$menu['items_tablet'] > 0 ? (int)$menu['items_tablet'] : min(2, $data['items_desktop'])));
        $data['items_mobile'] = max(1, min(6, isset($menu['items_mobile']) && (int)$menu['items_mobile'] > 0 ? (int)$menu['items_mobile'] : 1));
        $data['slider_items'] = $data['items_desktop'];
        $data['slider_autoplay'] = !empty($menu['slider_autoplay']) ? 1 : 0;
        $data['slider_interval'] = max(1000, min(30000, isset($menu['slider_interval']) ? (int)$menu['slider_interval'] : 5000));
        $data['menu_uid'] = 'probg-blog-menu-' . (++self::$menu_counter);

        if ($data['show_categories']) {
            foreach ($this->model_extension_probg_blog_blog->getCategories() as $category) {
                $data['categories'][] = array(
                    'title'=>$category['title'],
                    'article_count'=>(int)$category['article_count'],
                    'href'=>$this->url->link('extension/module/probg_blog','probg_blog_category_id='.(int)$category['category_id'],true)
                );
            }
        }

        if ($data['show_articles']) {
            $limit = max(1, min(100, isset($menu['limit']) ? (int)$menu['limit'] : 10));
            $category_id = isset($menu['category_id']) ? (int)$menu['category_id'] : 0;
            $sort = isset($menu['sort']) && $menu['sort'] === 'sort_order' ? 'sort_order' : 'date';
            $filter = array('limit'=>$limit, 'sort'=>$sort);
            if ($category_id > 0) $filter['category_id'] = $category_id;
            $width = max(1, (int)$this->config->get('module_probg_blog_image_list_width'));
            $height = max(1, (int)$this->config->get('module_probg_blog_image_list_height'));
            foreach ($this->model_extension_probg_blog_blog->getArticles($filter) as $article) {
                $data['articles'][] = array(
                    'title'=>$article['title'],
                    'category_title'=>$article['category_title'],
                    'thumb'=>$this->image($article['image'],$width,$height),
                    'date_added'=>date($this->language->get('date_format_short'),strtotime($article['date_added'])),
                    'href'=>$this->url->link('extension/module/probg_blog','probg_blog_category_id='.(int)$article['category_id'].'&probg_blog_article_id='.(int)$article['article_id'],true)
                );
            }
        }

        if (!$data['show_blog'] && !$data['categories'] && !$data['articles']) return '';

        if ($data['articles']) {
            // List and slider use the same article card styling.
            $this->document->addStyle('catalog/view/theme/default/stylesheet/probg_blog_menu_slider.css');

            if ($data['display'] === 'slider') {
                $this->document->addScript('catalog/view/javascript/probg_blog_menu_slider.js');
            }
        }

        return $this->load->view('extension/module/probg_blog_menu', $data);
    }

    private function articleCards($rows) {
        $out = array();
        $width = max(1,(int)$this->config->get('module_probg_blog_image_list_width'));
        $height = max(1,(int)$this->config->get('module_probg_blog_image_list_height'));
        foreach ($rows as $row) {
            $out[] = array(
                'title'=>$row['title'],
                'short_description'=>html_entity_decode($row['short_description'],ENT_QUOTES,'UTF-8'),
                'image'=>$this->image($row['image'],$width,$height),
                'date_added'=>date($this->language->get('date_format_short'),strtotime($row['date_added'])),
                'category_title'=>$row['category_title'],
                'category_href'=>$this->url->link('extension/module/probg_blog','probg_blog_category_id='.(int)$row['category_id'],true),
                'href'=>$this->url->link('extension/module/probg_blog','probg_blog_category_id='.(int)$row['category_id'].'&probg_blog_article_id='.(int)$row['article_id'],true)
            );
        }
        return $out;
    }

    private function section() {
        $all = $this->config->get('module_probg_blog_description');
        $language_id = (int)$this->config->get('config_language_id');
        return is_array($all) && isset($all[$language_id]) ? $all[$language_id] : array();
    }

    private function meta($row, $fallback) {
        $title = !empty($row['meta_title']) ? $row['meta_title'] : $fallback;
        $this->document->setTitle($title);
        if (!empty($row['meta_description'])) $this->document->setDescription($row['meta_description']);
        if (!empty($row['meta_keyword'])) $this->document->setKeywords($row['meta_keyword']);
    }

    private function image($path, $width, $height) {
        if (!$path || !is_file(DIR_IMAGE . $path)) $path = $this->config->get('module_probg_blog_default_image');
        return ($path && is_file(DIR_IMAGE . $path)) ? $this->model_tool_image->resize($path,$width,$height) : '';
    }

    private function social($title,$description,$url,$type,$image='',$article=array()) {
        $site=$this->config->get('config_name');
        $meta='<meta property="og:type" content="'.htmlspecialchars($type,ENT_QUOTES,'UTF-8').'" />\n<meta property="og:title" content="'.htmlspecialchars($title,ENT_QUOTES,'UTF-8').'" />\n<meta property="og:description" content="'.htmlspecialchars(strip_tags($description),ENT_QUOTES,'UTF-8').'" />\n<meta property="og:url" content="'.htmlspecialchars($url,ENT_QUOTES,'UTF-8').'" />\n<meta property="og:site_name" content="'.htmlspecialchars($site,ENT_QUOTES,'UTF-8').'" />\n';
        if($image)$meta.='<meta property="og:image" content="'.htmlspecialchars($image,ENT_QUOTES,'UTF-8').'" />\n';
        $meta.='<meta name="twitter:card" content="'.($image?'summary_large_image':'summary').'" />\n<meta name="twitter:title" content="'.htmlspecialchars($title,ENT_QUOTES,'UTF-8').'" />\n<meta name="twitter:description" content="'.htmlspecialchars(strip_tags($description),ENT_QUOTES,'UTF-8').'" />\n';
        if($image)$meta.='<meta name="twitter:image" content="'.htmlspecialchars($image,ENT_QUOTES,'UTF-8').'" />\n';
        $json=array('@context'=>'https://schema.org','@type'=>$type==='article'?'BlogPosting':'Blog','name'=>$title,'url'=>$url);
        if($type==='article'){$json['headline']=$title;$json['datePublished']=$article['date_added'];$json['dateModified']=$article['date_modified'];$json['articleSection']=$article['category_title'];if($image)$json['image']=$image;}
        $meta.='<script type="application/ld+json">'.json_encode($json,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>';
        $this->config->set('probg_blog_social_meta',$meta);
    }

    private function breadcrumbs($extra=array()) {
        $breadcrumbs=array(
            array('text'=>$this->language->get('text_home'),'href'=>$this->url->link('common/home','',true)),
            array('text'=>$this->language->get('heading_title'),'href'=>$this->url->link('extension/module/probg_blog','',true))
        );
        foreach($extra as $item)$breadcrumbs[]=$item;
        return $breadcrumbs;
    }

    private function layoutData() {
        return array(
            'column_left'=>$this->load->controller('common/column_left'),
            'column_right'=>$this->load->controller('common/column_right'),
            'content_top'=>$this->load->controller('common/content_top'),
            'content_bottom'=>$this->load->controller('common/content_bottom')
        );
    }

    private function pagination(&$data,$total,$page,$limit,$query) {
        $pagination=new Pagination();
        $pagination->total=$total;
        $pagination->page=$page;
        $pagination->limit=$limit;
        $pagination->url=$this->url->link('extension/module/probg_blog',($query?$query.'&':'').'page={page}',true);
        $data['pagination']=$pagination->render();
        $start=$total?(($page-1)*$limit)+1:0;
        $end=min($total,$page*$limit);
        $data['results']=sprintf($this->language->get('text_pagination'),$start,$end,$total,ceil($total/$limit));
    }

    private function render($view,$data) {
        $data['header']=$this->load->controller('common/header');
        $data['footer']=$this->load->controller('common/footer');
        return $this->response->setOutput($this->load->view('extension/module/'.$view,$data));
    }

    private function notFound() {
        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'].' 404 Not Found');
        $this->document->setTitle($this->language->get('text_error'));
        $data=$this->layoutData();
        $data['heading_title']=$this->language->get('text_error');
        $data['breadcrumbs']=$this->breadcrumbs();
        $data['header']=$this->load->controller('common/header');
        $data['footer']=$this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('error/not_found',$data));
        return '';
    }
}
