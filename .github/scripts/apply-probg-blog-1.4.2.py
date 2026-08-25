from pathlib import Path
import re

catalog = Path('upload/catalog/controller/extension/module/probg_blog.php')
s = catalog.read_text()
old = """        if ($data['display'] === 'slider' && $data['articles']) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/probg_blog_menu_slider.css');
            $this->document->addScript('catalog/view/javascript/probg_blog_menu_slider.js');
        }
"""
new = """        if ($data['articles']) {
            // List and slider use the same article card styling.
            $this->document->addStyle('catalog/view/theme/default/stylesheet/probg_blog_menu_slider.css');

            if ($data['display'] === 'slider') {
                $this->document->addScript('catalog/view/javascript/probg_blog_menu_slider.js');
            }
        }
"""
if old not in s:
    raise SystemExit('catalog article asset block not found')
catalog.write_text(s.replace(old, new, 1))

admin = Path('upload/admin/controller/extension/module/probg_blog.php')
s = admin.read_text().replace('1.4.1', '1.4.2')
s = s.replace("$data['stage'] = '25';", "$data['stage'] = '26';")
admin.write_text(s)

model = Path('upload/admin/model/extension/module/probg_blog.php')
model.write_text(model.read_text().replace('1.4.1', '1.4.2'))

install = Path('install.xml')
s = install.read_text()
if '<version>1.4.1</version>' not in s:
    raise SystemExit('install.xml 1.4.1 version marker not found')
install.write_text(s.replace('<version>1.4.1</version>', '<version>1.4.2</version>', 1))

stage_texts = {
    'upload/admin/language/bg-bg/extension/module/probg_blog.php': 'Версия 1.4.2 показва статиите в режим Списък със същите визуални карти като в режим Слайдър.',
    'upload/admin/language/en-gb/extension/module/probg_blog.php': 'Version 1.4.2 renders List-mode articles with the same visual cards used by Slider mode.'
}
for filename, text in stage_texts.items():
    p = Path(filename)
    data = p.read_text()
    data, count = re.subn(r"\$_\['text_stage_info'\]\s*=\s*'[^']*';", "$_['text_stage_info']='" + text + "';", data, count=1)
    if count != 1:
        raise SystemExit('stage text not found: ' + filename)
    p.write_text(data)

changelog = Path('CHANGELOG.md')
data = changelog.read_text()
entry = """## [1.4.2] - 2026-08-25

### fix — Български

- Режимът **Изглед на статиите → Списък** вече използва същите article cards като режим **Слайдър**.
- Картите показват изображение, категория, заглавие и дата със същия стил, размери и hover поведение.
- В режим Списък картите се подреждат в responsive grid без slider track, стрелки, dots или autoplay.
- Общият card stylesheet се зарежда и за List режим, докато slider JavaScript се зарежда само при Slider.

### fix — English

- **Article display → List** now uses the same article cards as **Slider** mode.
- Cards share the same image, category, title, date, dimensions and hover treatment.
- List mode uses a responsive static grid without slider track, arrows, dots or autoplay.
- Shared card CSS loads for both modes, while slider JavaScript remains Slider-only.

"""
if '## [1.4.2] - 2026-08-25' not in data:
    data = data.replace('# Changelog\n\n', '# Changelog\n\n' + entry, 1)
changelog.write_text(data)

docs = Path('docs/1.4.2.md')
docs.write_text("""# ProBG Blog 1.4.2

## Български

Версия **1.4.2** уеднаквява визуализацията на статиите в менюто. При **Изглед на статиите → Списък** всяка статия вече използва същата card структура като в режим **Слайдър** — изображение, категория, заглавие и дата.

List режимът остава статичен: картите се подреждат в responsive grid и не зареждат slider JavaScript, стрелки, dots или autoplay.

## English

Version **1.4.2** unifies menu article presentation. **Article display → List** now renders the same article card structure used by **Slider** mode, while remaining a static responsive grid without slider controls or JavaScript behavior.
""")

stages = Path('docs/STAGES.md')
data = stages.read_text()
line = '- 1.4.2 — unified List and Slider article cards in blog menu instances; List remains a static responsive grid.\n'
if line not in data:
    data += ('\n' if not data.endswith('\n') else '') + line
stages.write_text(data)
