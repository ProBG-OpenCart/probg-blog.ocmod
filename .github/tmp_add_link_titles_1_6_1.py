from pathlib import Path
import re

TWIG_FILES = sorted(Path('upload').rglob('*.twig'))
ANCHOR_RE = re.compile(r'<a\b(?P<attrs>[^>]*)>(?P<body>.*?)</a>', re.S)
HREF_RE = re.compile(r'\bhref\s*=\s*"([^"]*)"', re.S)
ANCHOR_OPEN_RE = re.compile(r'<a\b[^>]*>', re.S)


def body_exprs(body):
    return [x.strip() for x in re.findall(r'{{\s*([^}]+?)\s*}}', body)]


def infer_title(path, attrs, body):
    match = HREF_RE.search(attrs)
    href = match.group(1) if match else ''
    name = path.name
    full = str(path)

    if 'breadcrumb.href' in href:
        return '{{ breadcrumb_text }}' if name == 'probg_blog_article.twig' else '{{ breadcrumb.text }}'
    if 'article.edit' in href or 'category.edit' in href:
        return '{{ button_edit }}'
    if 'settings_url' in href:
        return '{{ text_settings }}' if full.endswith('/module/probg_blog.twig') else '{{ tab_settings }}'
    if 'categories_url' in href:
        if '{{ button_categories }}' in body:
            return '{{ button_categories }}'
        return '{{ text_categories }}' if full.endswith('/module/probg_blog.twig') else '{{ tab_categories }}'
    if 'articles_url' in href:
        if '{{ button_articles }}' in body:
            return '{{ button_articles }}'
        return '{{ text_articles }}' if full.endswith('/module/probg_blog.twig') else '{{ tab_articles }}'
    if 'blog_url' in href:
        return '{{ text_blog_home }}' if name == 'probg_blog_menu.twig' else '{{ heading_title }}'
    if 'image.popup' in href:
        return '{{ heading_title }} — {{ loop.index }} / {{ images|length }}'
    if 'product.href' in href:
        return '{{ product.name }}'
    if 'article.category_href' in href:
        return '{{ article.category_title }}'
    if 'category.href' in href:
        return '{{ category.title }}'
    if 'article.href' in href:
        if '{{ text_read_more }}' in body:
            return '{{ text_read_more }}: {{ article.title }}'
        return '{{ article.title }}'
    if href == '' and 'data-toggle="image"' in attrs:
        if full.endswith('/extension/module/probg_blog.twig'):
            return '{{ entry_default_image }}'
        if name == 'article_form.twig':
            if re.search(r'\bid\s*=\s*"thumb-image"', attrs):
                return '{{ text_main_image }}'
            return '{{ entry_image }}'

    if href.startswith('#'):
        exprs = body_exprs(body)
        if 'language.name' in exprs:
            return '{{ language.name }}'
        for expr in exprs:
            if expr.startswith(('text_', 'tab_', 'entry_', 'button_')):
                return '{{ ' + expr + ' }}'

    exprs = body_exprs(body)
    for expr in exprs:
        if expr.endswith(('.title', '.name')) or expr.startswith(('text_', 'tab_', 'button_')):
            return '{{ ' + expr + ' }}'

    return '{{ heading_title }}'


changed = []
for path in TWIG_FILES:
    text = path.read_text(encoding='utf-8')

    def replace_anchor(match):
        attrs = match.group('attrs')
        body = match.group('body')
        if re.search(r'\btitle\s*=', attrs):
            return match.group(0)
        title = infer_title(path, attrs, body)
        return '<a' + attrs + ' title="' + title + '">' + body + '</a>'

    new_text, inspected = ANCHOR_RE.subn(replace_anchor, text)
    if new_text != text:
        path.write_text(new_text, encoding='utf-8')
        changed.append((str(path), inspected))

# Every explicit anchor in module Twig templates must have a title.
missing = []
for path in TWIG_FILES:
    text = path.read_text(encoding='utf-8')
    for tag in ANCHOR_OPEN_RE.findall(text):
        if not re.search(r'\btitle\s*=', tag):
            missing.append((str(path), tag[:180]))
if missing:
    for item in missing:
        print('Missing title:', item)
    raise SystemExit('Some anchors still have no title attribute')

# Guard against hand-built HTML anchors outside Twig templates as well.
for pattern in ('*.php', '*.js'):
    for path in Path('upload').rglob(pattern):
        text = path.read_text(encoding='utf-8')
        for tag in ANCHOR_OPEN_RE.findall(text):
            if not re.search(r'\btitle\s*=', tag):
                raise SystemExit(f'Anchor without title outside Twig: {path}: {tag[:180]}')

# Patch release metadata.
install = Path('install.xml')
install_text = install.read_text(encoding='utf-8')
install_text = install_text.replace('<version>1.6.0</version>', '<version>1.6.1</version>', 1)
install.write_text(install_text, encoding='utf-8')

for filename in (
    'upload/admin/controller/extension/module/probg_blog.php',
    'upload/admin/model/extension/module/probg_blog.php',
):
    path = Path(filename)
    text = path.read_text(encoding='utf-8').replace("'1.6.0'", "'1.6.1'")
    if filename.endswith('/controller/extension/module/probg_blog.php'):
        text = text.replace("$data['stage'] = '28';", "$data['stage'] = '29';")
    path.write_text(text, encoding='utf-8')

readme = Path('README.md')
text = readme.read_text(encoding='utf-8')
text = text.replace('**Current version:** `1.6.0`', '**Current version:** `1.6.1`', 1)
text = text.replace('probg-blog-1.6.0.ocmod.zip', 'probg-blog-1.6.1.ocmod.zip')
readme.write_text(text, encoding='utf-8')

changelog = Path('CHANGELOG.md')
text = changelog.read_text(encoding='utf-8')
section = """## [1.6.1] - 2026-08-30

### fix — Български

- Добавен е `title` атрибут към всички експлицитни линкове (`<a>`) в административните и frontend Twig шаблони на ProBG Blog.
- Breadcrumbs, табове, категории, статии, изображения, свързани продукти, менюта и бутоните „Прочети още“ вече имат описателни title стойности.
- Добавена е автоматична проверка при разработката, че в шаблоните не остава `<a>` елемент без `title`.

### fix — English

- Added a `title` attribute to every explicit link (`<a>`) in the ProBG Blog administration and storefront Twig templates.
- Breadcrumbs, tabs, categories, articles, images, related products, menus, and Read More actions now have descriptive title values.
- Added a development validation guard so no template `<a>` element is left without a `title` attribute.

"""
marker = '# Changelog\n\n'
if '## [1.6.1]' not in text:
    text = text.replace(marker, marker + section, 1)
changelog.write_text(text, encoding='utf-8')

stages = Path('docs/STAGES.md')
text = stages.read_text(encoding='utf-8')
line = '- Stage 29 / 1.6.1 — added descriptive title attributes to every explicit administration and storefront link, including dynamic image-picker links, with validation coverage.\n'
if 'Stage 29 / 1.6.1' not in text:
    text = text.rstrip() + '\n' + line
stages.write_text(text, encoding='utf-8')

Path('docs/1.6.1.md').write_text("""# ProBG Blog 1.6.1

## Български

Версия 1.6.1 добавя описателен HTML `title` атрибут към всички експлицитни линкове в административните и frontend шаблони на модула. Покрити са breadcrumbs, навигационни табове, категории, статии, изображения и lightbox линкове, свързани продукти, frontend менюта и бутоните за прочитане на статия. Динамично генерираните image-picker линкове в администрацията също са покрити.

## English

Version 1.6.1 adds a descriptive HTML `title` attribute to every explicit link in the module's administration and storefront templates. Coverage includes breadcrumbs, navigation tabs, categories, articles, image/lightbox links, related products, frontend menus, Read More actions, and dynamically generated administration image-picker links.
""", encoding='utf-8')

print('Updated Twig files:')
for path, count in changed:
    print(f'  {path}: {count} anchor(s) inspected')
