# ProBG Blog for OpenCart 3

[![Buy me a coffee](https://img.shields.io/badge/Buy%20me%20a%20coffee-Revolut-191C1F?logo=revolut&logoColor=white)](https://revolut.me/vtotev)

**Current version:** `1.6.1`  
**Status:** Stable  
**OpenCart:** `3.0.2.x` / `3.0.3.x`

---

# Български

## Кратко описание

**ProBG Blog** е цялостен многоезичен блог модул за OpenCart 3, който добавя категории, статии, SEO URL адреси, Open Graph/Twitter Cards, JSON-LD, sitemap, галерия с lightbox, свързани продукти и неограничен брой frontend менюта със списък или слайдър.

## Описание

**ProBG Blog** превръща OpenCart 3 в пълноценна платформа за публикуване на новини, статии, ръководства, полезно съдържание и SEO текстове, без необходимост от външна CMS система. Модулът е интегриран директно в администрацията на OpenCart и използва стандартните механизми за езици, магазини, layouts, SEO URL адреси, изображения и продукти.

Категориите и статиите са многоезични и разполагат със собствени мета данни и SEO адреси. Статиите могат да съдържат кратко и пълно HTML описание, основно изображение, галерия с неограничени допълнителни изображения и свързани продукти от каталога. Галерията се отваря в responsive lightbox с навигация между изображенията.

Модулът предоставя собствена начална страница на блога, страници за категории и статии, breadcrumbs, пагинация, canonical адреси, Open Graph, Twitter Cards и структурирани JSON-LD данни. SEO URL адресите могат да се генерират автоматично с транслитерация и се съхраняват в стандартната OpenCart таблица `seo_url`.

За OpenCart Layouts могат да се създават **неограничен брой отделни ProBG Blog менюта**. Всяко меню има собствени настройки за заглавие, показване на линк към блога, категории и статии, филтър по категория, лимит, подредба и режим **Списък** или **Слайдър**. И двата режима използват еднакви article cards, а slider режимът добавя responsive навигация, dots, autoplay и touch swipe.

## Основни възможности

- Пълноценен блог за OpenCart 3 без външна CMS.
- Избор **Grid / List** за основната блог страница и категориите.
- Grid изглед с 3 статии на ред на desktop.
- Контрол върху показването и дължината на описанията в навигацията на категориите.
- Многоезични категории и статии.
- HTML редактор за описанията.
- Кратко и пълно описание на статия.
- Основно изображение и неограничена галерия.
- Lightbox за галерията с предишно/следващо изображение и клавиатурна навигация.
- Свързани продукти чрез стандартния OpenCart autocomplete.
- Автоматични и ръчни SEO URL адреси по език и магазин.
- Българска транслитерация и защита от конфликт на SEO URL.
- Meta Title, Meta Description и Meta Keywords.
- Canonical URL и 301 пренасочване към правилния йерархичен адрес.
- Open Graph и Twitter Cards.
- JSON-LD `Blog`, `BlogPosting`, `BreadcrumbList` и `CollectionPage`.
- Интеграция със стандартния OpenCart Google Sitemap.
- Самостоятелен blog sitemap endpoint.
- Multi-store поддръжка за категории и SEO записи.
- Layout Override за категории и статии.
- Catalog кеширане по магазин, език и тип заявка.
- Неограничен брой frontend menu instances.
- Отделни настройки за всяко меню.
- Включване/изключване на линка към блога, категориите и статиите.
- Филтриране на статиите в меню по избрана категория.
- Режим **Списък** с responsive article cards.
- Режим **Слайдър** със същите article cards.
- Responsive multi-item slider със стрелки, dots, autoplay и touch swipe.
- Системен instance **ProBG Blog - Latest Articles** за блок с последни статии.
- Bootstrap 5 ориентирани storefront шаблони с cards, breadcrumbs и responsive grid.
- Стандартна OpenCart 3 административна интеграция.

## Документация

### Инсталация

1. Изтеглете `probg-blog-1.6.1.ocmod.zip` от GitHub Release или директорията `dist/`.
2. Отворете **Extensions → Installer** и качете OCMOD ZIP пакета.
3. Отворете **Extensions → Modifications** и натиснете **Refresh**.
4. Отворете **Extensions → Extensions → Modules** и инсталирайте **ProBG Blog**.
5. Отворете **ProBG Blog → Настройки** и конфигурирайте основните данни, SEO, изображенията и интеграциите.
6. Създайте категории от **ProBG Blog → Категории**.
7. Създайте статии от **ProBG Blog → Статии**.
8. При необходимост създайте едно или повече менюта от **ProBG Blog → Настройки → Меню** и ги добавете в **Design → Layouts**.
9. Уверете се, че SEO URL и `.htaccess` са активирани в OpenCart.

### Настройки

Основните настройки на модула са организирани в секции:

- **Общи** — статус, подредба, брой статии на страница, Grid/List изглед и настройки за описанията в навигацията на категориите;
- **Данни** — многоезично заглавие, описание и SEO данни за основната блог секция;
- **Меню** — управление на неограничен брой frontend менюта;
- **Изображения** — размери за списък, статия и галерия;
- **Интеграции и производителност** — sitemap и кеширане.

### Категории

Всяка категория поддържа:

- многоезично заглавие;
- HTML описание;
- многоезични SEO URL, Meta Title, Meta Description и Meta Keywords;
- тагове;
- статус и подредба;
- присвояване към магазини;
- Layout Override.

Ако SEO URL е празно, модулът го генерира автоматично чрез транслитерация. Ръчно въведен SEO URL не се презаписва. При конфликт автоматично генерираният адрес получава числов суфикс.

### Статии

Всяка статия поддържа:

- задължителна основна категория;
- многоезично заглавие;
- многоезично кратко HTML описание;
- многоезично пълно HTML описание;
- основно изображение;
- неограничени допълнителни изображения;
- lightbox галерия;
- многоезични SEO URL, Meta Title, Meta Description и Meta Keywords;
- свързани продукти;
- статус и подредба;
- автоматични дати на добавяне и обновяване.

Свързаните продукти се избират чрез стандартния OpenCart autocomplete и се визуализират под съдържанието на статията.

### Менюта и Layouts

В таб **Меню** се показва компактен списък с всички създадени менюта. Всяко меню е стандартен OpenCart module instance и може да бъде добавено независимо в **Design → Layouts**.

Всяко меню поддържа:

- административно име;
- многоезично заглавие;
- статус;
- **Покажи линк към блога**;
- **Покажи категории**;
- **Покажи статии**;
- категория за филтриране на статиите;
- лимит;
- подредба по дата или `sort_order`;
- изглед **Списък** или **Слайдър**;
- при Slider — брой елементи, autoplay и интервал.

При **Списък** статиите се визуализират като статичен responsive grid със същите cards, използвани от слайдъра. При **Слайдър** се добавят стрелки, dots, autoplay, responsive брой елементи и touch swipe.

За блок с последни статии се използва системният instance **ProBG Blog - Latest Articles**.

### Галерия и Lightbox

Допълнителните изображения към статия се показват в responsive gallery grid. При клик оригиналното изображение се отваря в lightbox с:

- предишно/следващо изображение;
- брояч;
- затваряне с `Esc`;
- навигация с клавиатура;
- responsive поведение за мобилни устройства.

### SEO и URL структура

Публичната URL структура е йерархична:

```text
/blog
/blog/category
/blog/category/article
```

Модулът поддържа:

- автоматична българска транслитерация;
- ръчно зададени SEO URL адреси;
- проверка за конфликт по език и магазин;
- canonical адреси;
- 301 пренасочване към правилния йерархичен URL;
- Open Graph;
- Twitter Cards;
- JSON-LD структурирани данни.

SEO стойностите се записват в стандартната OpenCart таблица `{DB_PREFIX}seo_url`.

### Sitemap

При включена настройка ProBG Blog се интегрира със стандартния OpenCart Google Sitemap и предоставя собствен sitemap endpoint:

```text
index.php?route=extension/feed/probg_blog_sitemap
```

### Обновяване

При обновяване **не деинсталирайте модула**, защото деинсталацията премахва блог таблиците.

1. Качете новия OCMOD пакет през **Extensions → Installer**.
2. Отворете **Extensions → Modifications** и натиснете **Refresh**.
3. Отворете **ProBG Blog → Настройки** поне веднъж, за да се изпълнят необходимите migration стъпки.
4. Изчистете theme/cache при необходимост.

### Деинсталация

Деинсталацията премахва блог таблиците, настройките и SEO записите на модула.

**Внимание:** всички въведени блог данни се изтриват.

---

# English

## Short description

**ProBG Blog** is a complete multilingual blog extension for OpenCart 3 that adds categories, articles, SEO URLs, Open Graph/Twitter Cards, JSON-LD, sitemap integration, a lightbox gallery, related products, and unlimited frontend menus with List or Slider article layouts.

## Description

**ProBG Blog** turns OpenCart 3 into a full content publishing platform for news, articles, guides, educational content, and SEO landing content without requiring an external CMS. The extension is integrated directly into the OpenCart administration and uses the platform's standard language, store, layout, SEO URL, image, and product systems.

Categories and articles are multilingual and have their own metadata and SEO URLs. Articles support short and full HTML descriptions, a main image, an unlimited additional-image gallery, and related catalog products. Gallery images open in a responsive lightbox with previous/next navigation.

The extension provides a dedicated blog home page, category pages, article pages, breadcrumbs, pagination, canonical URLs, Open Graph, Twitter Cards, and structured JSON-LD data. SEO URLs can be generated automatically with transliteration and are stored in OpenCart's standard `seo_url` table.

For OpenCart Layouts you can create **unlimited independent ProBG Blog menu instances**. Every menu has its own title, blog-link/category/article visibility options, category filter, article limit, sorting, and **List** or **Slider** display mode. Both modes use the same article cards, while Slider mode adds responsive navigation, dots, autoplay, and touch swipe.

## Main features

- Complete blog functionality for OpenCart 3 without an external CMS.
- **Grid / List** archive display for blog and category pages.
- Three article cards per row on desktop in Grid mode.
- Configurable category-navigation description visibility and character limit.
- Multilingual categories and articles.
- HTML editor for content fields.
- Short and full article descriptions.
- Main image and unlimited article gallery.
- Gallery lightbox with previous/next and keyboard navigation.
- Related products through the standard OpenCart autocomplete.
- Automatic and manual SEO URLs per language and store.
- Bulgarian transliteration and SEO URL conflict protection.
- Meta Title, Meta Description, and Meta Keywords.
- Canonical URLs and 301 redirects to the correct hierarchical URL.
- Open Graph and Twitter Cards.
- JSON-LD `Blog`, `BlogPosting`, `BreadcrumbList`, and `CollectionPage` data.
- OpenCart Google Sitemap integration.
- Dedicated blog sitemap endpoint.
- Multi-store support for categories and SEO records.
- Layout Override support for categories and articles.
- Catalog caching by store, language, and query type.
- Unlimited frontend menu instances.
- Independent settings for every menu.
- Toggle blog link, categories, and articles independently.
- Filter menu articles by a selected category.
- **List** mode with responsive article cards.
- **Slider** mode using the same article cards.
- Responsive multi-item slider with arrows, dots, autoplay, and touch swipe.
- Dedicated **ProBG Blog - Latest Articles** system instance for Layouts.
- Bootstrap 5-oriented storefront templates with responsive cards, breadcrumbs, and grids.
- Native OpenCart 3 administration integration.

## Documentation

### Installation

1. Download `probg-blog-1.6.1.ocmod.zip` from the GitHub Release or the `dist/` directory.
2. Open **Extensions → Installer** and upload the OCMOD ZIP package.
3. Open **Extensions → Modifications** and click **Refresh**.
4. Open **Extensions → Extensions → Modules** and install **ProBG Blog**.
5. Open **ProBG Blog → Settings** and configure the general data, SEO, images, and integrations.
6. Create categories under **ProBG Blog → Categories**.
7. Create articles under **ProBG Blog → Articles**.
8. When needed, create one or more menus under **ProBG Blog → Settings → Menu** and place them in **Design → Layouts**.
9. Make sure OpenCart SEO URLs and `.htaccess` are enabled.

### Settings

The module settings are organized into sections:

- **General** — status, article sorting, articles per page, Grid/List archive display, and category-navigation description settings;
- **Data** — multilingual blog-section title, description, and SEO data;
- **Menu** — unlimited frontend menu management;
- **Images** — image dimensions for lists, articles, and galleries;
- **Integrations & Performance** — sitemap and caching.

### Categories

Each category supports:

- multilingual title;
- HTML description;
- multilingual SEO URL, Meta Title, Meta Description, and Meta Keywords;
- tags;
- status and sort order;
- store assignments;
- Layout Override.

When the SEO URL is empty, the extension generates it automatically using transliteration. Manually entered URLs are preserved. Automatic URL conflicts receive a numeric suffix.

### Articles

Each article supports:

- required primary category;
- multilingual title;
- multilingual short HTML description;
- multilingual full HTML description;
- main image;
- unlimited additional images;
- lightbox gallery;
- multilingual SEO URL, Meta Title, Meta Description, and Meta Keywords;
- related products;
- status and sort order;
- automatic created/modified dates.

Related products are selected using the standard OpenCart autocomplete and are displayed below the article content.

### Menus and Layouts

The **Menu** tab provides a compact list of all created menu instances. Every menu is a standard OpenCart module instance and can be placed independently in **Design → Layouts**.

Each menu supports:

- administration name;
- multilingual title;
- status;
- **Show blog link**;
- **Show categories**;
- **Show articles**;
- article category filter;
- limit;
- date or `sort_order` sorting;
- **List** or **Slider** article display;
- Slider item count, autoplay, and interval.

In **List** mode, articles are rendered as a static responsive grid using the same cards as Slider mode. **Slider** mode adds arrows, dots, autoplay, responsive item count, and touch swipe.

Use the dedicated **ProBG Blog - Latest Articles** system instance for a latest-articles layout block.

### Gallery and Lightbox

Additional article images are displayed in a responsive gallery grid. Clicking an image opens the original image in a lightbox with:

- previous/next navigation;
- image counter;
- `Esc` close support;
- keyboard navigation;
- responsive mobile behavior.

### SEO and URL structure

The public URL structure is hierarchical:

```text
/blog
/blog/category
/blog/category/article
```

The extension supports:

- automatic Bulgarian transliteration;
- manually defined SEO URLs;
- conflict detection by language and store;
- canonical URLs;
- 301 redirects to the correct hierarchical URL;
- Open Graph;
- Twitter Cards;
- JSON-LD structured data.

SEO data is stored in OpenCart's standard `{DB_PREFIX}seo_url` table.

### Sitemap

When enabled, ProBG Blog integrates with the standard OpenCart Google Sitemap and provides a dedicated sitemap endpoint:

```text
index.php?route=extension/feed/probg_blog_sitemap
```

### Updating

When upgrading, **do not uninstall the extension**, because uninstalling removes the blog database tables.

1. Upload the new OCMOD package through **Extensions → Installer**.
2. Open **Extensions → Modifications** and click **Refresh**.
3. Open **ProBG Blog → Settings** at least once so required migration steps can run.
4. Clear theme/cache if necessary.

### Uninstalling

Uninstalling removes the blog tables, module settings, and SEO records.

**Warning:** all stored blog content is deleted.

---

## Database tables

Main module tables:

- `{DB_PREFIX}probg_blog_category`
- `{DB_PREFIX}probg_blog_category_description`
- `{DB_PREFIX}probg_blog_category_to_store`
- `{DB_PREFIX}probg_blog_category_to_layout`
- `{DB_PREFIX}probg_blog_article`
- `{DB_PREFIX}probg_blog_article_description`
- `{DB_PREFIX}probg_blog_article_image`
- `{DB_PREFIX}probg_blog_article_related`

SEO URLs use OpenCart's standard `{DB_PREFIX}seo_url` table.

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md) for the full version history.

## Support development / Подкрепете разработката

Ако модулът ви е полезен, можете да подкрепите разработката му чрез Revolut.  
If this module is useful to you, you can support its development through Revolut.

[![Buy me a coffee](https://img.shields.io/badge/Buy%20me%20a%20coffee-Revolut-0075EB?style=for-the-badge&logo=revolut&logoColor=white)](https://revolut.me/vtotev)
