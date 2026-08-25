from pathlib import Path
import re

controller = Path('upload/admin/controller/extension/module/probg_blog.php')
s = controller.read_text()
s = s.replace("module_probg_blog_version') !== '1.3.0'", "module_probg_blog_version') !== '1.4.0'")
s = s.replace("['module_probg_blog_version'] = '1.3.0'", "['module_probg_blog_version'] = '1.4.0'")
s = s.replace("$data['stage'] = '23';", "$data['stage'] = '24';")
s = s.replace("$data['version'] = '1.3.0';", "$data['version'] = '1.4.0';")
marker = "$this->document->addStyle('view/stylesheet/probg_blog.css');"
assets = marker + "\n        $this->document->addStyle('view/stylesheet/probg_blog_menu_manager.css');\n        $this->document->addScript('view/javascript/probg_blog_menu_manager.js');"
if 'probg_blog_menu_manager.css' not in s:
    if marker not in s:
        raise SystemExit('admin asset marker not found')
    s = s.replace(marker, assets, 1)
controller.write_text(s)

model = Path('upload/admin/model/extension/module/probg_blog.php')
s = model.read_text()
s = s.replace("module_probg_blog_version'=>'1.3.0'", "module_probg_blog_version'=>'1.4.0'")
s = s.replace("['module_probg_blog_version'] = '1.3.0'", "['module_probg_blog_version'] = '1.4.0'")
model.write_text(s)

install = Path('install.xml')
s = install.read_text()
if '<version>1.3.0</version>' in s:
    s = s.replace('<version>1.3.0</version>', '<version>1.4.0</version>', 1)
install.write_text(s)

langs = {
    'upload/admin/language/bg-bg/extension/module/probg_blog.php': 'Версия 1.4.0 преработва таб Меню с компактен списък на всички менюта и редакция само на избраното меню.',
    'upload/admin/language/en-gb/extension/module/probg_blog.php': 'Version 1.4.0 redesigns the Menu tab with a compact menu list and editing of only the selected menu.'
}
for path, value in langs.items():
    p = Path(path)
    s = p.read_text()
    s, n = re.subn(r"\$_\['text_stage_info'\]='[^']*';", "$_['text_stage_info']='" + value + "';", s, count=1)
    if n != 1:
        raise SystemExit('text_stage_info not found in ' + path)
    p.write_text(s)

changelog = Path('CHANGELOG.md')
s = changelog.read_text()
entry = """## [1.4.0] - 2026-08-25

### feat — Български

- Преработен е таб **Меню** като master/detail интерфейс.
- Добавен е компактен списък с всички създадени менюта вместо едновременно показване на всички дълги формуляри.
- Списъкът показва име, статус, активни секции, категория, List/Slider режим и лимит.
- Добавени са търсене, **Редакция**, **Премахване** и **Към списъка**.
- При редакция се показва само избраното меню.
- **Добави меню** отваря директно editor за новото меню.
- При validation error автоматично се отваря менюто с проблемното поле.
- Съществуващите OpenCart module instances и Layout assignments не се променят.

### feat — English

- Redesigned the **Menu** tab as a master/detail interface.
- Added a compact list of all menu instances instead of rendering every long form at once.
- The list shows name, status, enabled content sections, category, List/Slider mode and article limit.
- Added search, **Edit**, **Remove** and **Back to list** actions.
- Only the selected menu editor is displayed while editing.
- **Add menu** opens the new menu editor immediately.
- Validation errors automatically open the affected menu.
- Existing OpenCart module instances and Layout assignments are unchanged.

"""
if '## [1.4.0]' not in s:
    s = s.replace('# Changelog\n\n', '# Changelog\n\n' + entry, 1)
changelog.write_text(s)

stages = Path('docs/STAGES.md')
s = stages.read_text()
line = '- 1.4.0 — redesigned Menu administration as a searchable master/detail list with per-menu editing.'
if '1.4.0' not in s:
    s = s.rstrip() + '\n' + line + '\n'
stages.write_text(s)
