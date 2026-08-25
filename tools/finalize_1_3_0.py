from pathlib import Path
import re

admin = Path('upload/admin/controller/extension/module/probg_blog.php')
s = admin.read_text()

s = s.replace('$this->ensureLegacyMenuInstance();', '$this->ensureModuleInstances();')
s = s.replace("            $this->mirrorLegacyMenuSettings($post, $menus);\n\n            $post['module_probg_blog_version'] = '1.3.0';", "            $this->mirrorLegacyMenuSettings($post, $menus);\n            $post['module_probg_blog_instances_migrated'] = 1;\n\n            $post['module_probg_blog_version'] = '1.3.0';", 1)

old_uninstall = '''    public function uninstall() {
        $this->load->model('setting/module');
        foreach ($this->getMenuInstances() as $menu) {
            if (!empty($menu['module_id'])) {
                $this->cleanupLayoutModuleReference((int)$menu['module_id']);
                $this->model_setting_module->deleteModule((int)$menu['module_id']);
            }
        }
        $this->load->model('extension/module/probg_blog');
        $this->model_extension_module_probg_blog->uninstall();
    }
'''
new_uninstall = '''    public function uninstall() {
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
'''
if old_uninstall not in s:
    raise SystemExit('uninstall block not found')
s = s.replace(old_uninstall, new_uninstall, 1)

pattern = re.compile(r"    private function ensureLegacyMenuInstance\(\) \{.*?\n    \}\n\n    private function mirrorLegacyMenuSettings", re.S)
replacement = '''    private function ensureModuleInstances() {
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

    private function mirrorLegacyMenuSettings'''
s, n = pattern.subn(replacement, s, count=1)
if n != 1:
    raise SystemExit('legacy instance method not found')
admin.write_text(s)

catalog = Path('upload/catalog/controller/extension/module/probg_blog.php')
s = catalog.read_text()
needle = '''        if (is_array($setting) && isset($setting['probg_blog_type']) && $setting['probg_blog_type'] === 'menu') {
            if (isset($setting['status']) && !(int)$setting['status']) return '';
            return $this->menuModule($setting);
        }

        $mode = $this->config->get('module_probg_blog_layout_output');'''
replacement = '''        if (is_array($setting) && isset($setting['probg_blog_type']) && $setting['probg_blog_type'] === 'menu') {
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

        $mode = $this->config->get('module_probg_blog_layout_output');'''
if needle not in s:
    raise SystemExit('catalog instance dispatch marker not found')
s = s.replace(needle, replacement, 1)
catalog.write_text(s)

twig = Path('upload/admin/view/template/extension/module/probg_blog.twig')
s = twig.read_text()
layout_field = '''            <div class="form-group"><label class="col-sm-2 control-label"><span data-toggle="tooltip" title="{{ help_layout_output }}">{{ entry_layout_output }}</span></label><div class="col-sm-10"><select name="module_probg_blog_layout_output" class="form-control"><option value="articles"{% if module_probg_blog_layout_output == 'articles' %} selected{% endif %}>{{ text_layout_articles }}</option><option value="menu"{% if module_probg_blog_layout_output == 'menu' %} selected{% endif %}>{{ text_layout_menu }}</option></select></div></div>
'''
if layout_field not in s:
    raise SystemExit('legacy layout output field not found')
s = s.replace(layout_field, '', 1)
twig.write_text(s)

# Documentation refinement.
for path in ['docs/1.3.0.md', 'README.md']:
    p = Path(path)
    text = p.read_text()
    marker = 'Legacy menu settings are migrated automatically.'
    if path.endswith('1.3.0.md'):
        text = text.replace('Данните на категории и статии не се променят.', 'Допълнително се създава системен instance `ProBG Blog - Latest Articles`, за да остане наличен досегашният блок с последни статии в Design → Layouts. След първата миграция всички менюта могат да бъдат изтрити умишлено и няма да бъдат създавани повторно автоматично. Данните на категории и статии не се променят.')
    else:
        text = text.replace('- Всеки menu instance има собствено име и може да бъде добавян независимо в **Design → Layouts**.', '- Всеки menu instance има собствено име и може да бъде добавян независимо в **Design → Layouts**.\n- Запазен е отделен системен instance **ProBG Blog - Latest Articles** за досегашния блок с последни статии.')
        text = text.replace('- Every menu instance has its own admin name and can be selected independently in **Design → Layouts**.', '- Every menu instance has its own admin name and can be selected independently in **Design → Layouts**.\n- A dedicated **ProBG Blog - Latest Articles** system instance preserves the existing latest-articles layout block.')
    p.write_text(text)

p = Path('CHANGELOG.md')
text = p.read_text()
text = text.replace('- Всяко меню се записва като стандартен OpenCart `module` instance и се появява независимо в **Design → Layouts**.', '- Всяко меню се записва като стандартен OpenCart `module` instance и се появява независимо в **Design → Layouts**.\n- Добавен е системен `ProBG Blog - Latest Articles` instance, за да се запази досегашният блок с последни статии при наличие на menu instances.')
text = text.replace('- Each menu is stored as a standard OpenCart `module` instance and appears independently in **Design → Layouts**.', '- Each menu is stored as a standard OpenCart `module` instance and appears independently in **Design → Layouts**.\n- Added a `ProBG Blog - Latest Articles` system instance to preserve the existing latest-articles layout block when menu instances exist.')
p.write_text(text)
