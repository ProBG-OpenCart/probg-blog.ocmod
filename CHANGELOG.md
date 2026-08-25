# Changelog

## [1.2.1] - 2026-08-25

### fix — Български

- Коригирано е критично рекурсивно зареждане при добавяне на ProBG Blog **Меню** или **Последни статии** в `Design → Layouts`, включително на началната страница.
- Коригиран е PHP `Allowed memory size exhausted`, причинен от стария `$setting`-базиран source controller flow.
- Layout/full-page detection вече е директно в catalog controller source.
- Добавен е static request-level recursion guard преди зареждане на layout позициите.
- Старият OCMOD workaround за recursion detection е премахнат.

### fix — English

- Fixed critical recursive rendering when ProBG Blog **Menu** or **Latest Articles** is assigned in `Design → Layouts`, including the home page.
- Fixed PHP `Allowed memory size exhausted` caused by the legacy `$setting`-based source controller flow.
- Layout/full-page detection now lives directly in the catalog controller source.
- Added a static request-level recursion guard before layout positions are loaded.
- Removed the obsolete OCMOD recursion-detection workaround.

## [1.2.0] - 2026-08-24

### feat — Български

- Добавен е lightbox преглед на допълнителните изображения в галерията на статия, подобен на Fancybox.
- Използва се стандартният за OpenCart 3 Magnific Popup, без външен CDN или допълнителна библиотека.
- Добавени са предишно/следващо изображение, брояч на позицията и клавиатурна навигация.
- Thumbnail изображенията получават zoom индикатор и responsive hover/focus състояния.
- Добавен е отделен `probg_blog_lightbox.css` за fullscreen overlay, transitions и mobile оптимизация.
- При липса на JavaScript lightbox компонент оригиналните image links продължават да работят като нормални линкове.
- OCMOD версията е обновена до `1.2.0`, а административният етап до Stage 21.

### feat — English

- Added a Fancybox-style lightbox viewer for additional article gallery images.
- Uses OpenCart 3's bundled Magnific Popup with no external CDN or extra library installation.
- Added previous/next navigation, current/total image counter and keyboard controls.
- Gallery thumbnails now include a zoom indicator and responsive hover/focus states.
- Added dedicated `probg_blog_lightbox.css` for fullscreen overlay styling, transitions and mobile optimization.
- If the JavaScript lightbox component is unavailable, original image links continue to work as normal links.
- Updated the OCMOD version to `1.2.0` and administration stage to Stage 21.

## [1.1.1] - 2026-08-24

### fix — Български

- Коригирано е рекурсивното зареждане на ProBG Blog при добавяне на модула в **Design → Layouts**, включително на началната страница.
- OpenCart 3 може да извика неинстанциран модул без `$setting`; досегашната логика погрешно стартираше пълния blog `listing()` renderer.
- Layout извикванията вече се разпознават по текущия OpenCart route и показват само избрания output: **Меню** или **Последни статии**.
- Добавена е request-level защита `probg_blog_page_rendering`, която предотвратява рекурсия и когато модулът е поставен в layout, използван от самата блог секция.
- OCMOD версията е обновена до `1.1.1`, а административният етап до Stage 20.

### fix — English

- Fixed recursive ProBG Blog rendering when the module is assigned in **Design → Layouts**, including the home page.
- OpenCart 3 can invoke a non-instance module without a `$setting` array; the previous logic incorrectly started the full blog `listing()` renderer.
- Layout calls are now identified by the current OpenCart route and render only the selected output: **Menu** or **Latest Articles**.
- Added a request-level `probg_blog_page_rendering` guard to prevent recursion when the module is assigned to a layout used by the blog section itself.
- Updated the OCMOD version to `1.1.1` and administration stage to Stage 20.

## [1.1.0] - 2026-08-24

### feat — Български

- Преработени са storefront шаблоните на блога с Bootstrap 5 компоненти.
- Обновени са основната блог страница, категорията, статията, блокът с последни статии и вграденото блог меню.
- Галерията и свързаните продукти използват responsive Bootstrap 5 card/grid структура.
- Добавен е scoped `probg_blog.css` с fallback стилове за OpenCart 3 теми, които все още използват Bootstrap 3.
- Добавено е локализирано заглавие **Галерия / Gallery**.

### fix — Български

- Коригиран е липсващият линк към категорията в breadcrumb навигацията на статия.
- Breadcrumb структурата вече е **Начало → Блог → Категория → Статия**.

### feat — English

- Redesigned the blog storefront templates using Bootstrap 5 components.
- Updated the blog index, category, article, latest-articles block and built-in blog menu.
- Gallery and related products now use responsive Bootstrap 5 card/grid layouts.
- Added scoped `probg_blog.css` fallbacks for OpenCart 3 themes that still load Bootstrap 3.
- Added localized **Gallery** text.

### fix — English

- Fixed the missing category link in article breadcrumb navigation.
- Breadcrumb hierarchy is now **Home → Blog → Category → Article**.

## [1.0.6] - 2026-08-24

### fix — Български

- Коригирано е показването на буквални `\n` и escaped кавички над storefront съдържанието.
- Social meta стойностите се нормализират преди запис в config и отново преди подаване към header Twig.
- Запазени са Open Graph, Twitter Cards и JSON-LD метаданните.

### fix — English

- Fixed literal `\n` and escaped quotes being rendered above storefront content.
- Social metadata is normalized before config storage and again before it is passed to the header Twig.
- Open Graph, Twitter Cards and JSON-LD metadata remain enabled.

## [1.0.5] - 2026-08-24

### fix — Български

- Коригирана е storefront SEO rewrite логиката, която премахваше `route` твърде рано и причиняваше `Undefined index: route` в `startup/seo_url.php`.
- Запазена е йерархичната URL структура `/blog/category/article`.

### fix — English

- Fixed storefront SEO rewrite logic that removed `route` too early and caused `Undefined index: route` in `startup/seo_url.php`.
- Preserved the hierarchical `/blog/category/article` URL structure.

## [1.0.4] - 2026-08-24

### fix — Български

- Коригирана е фаталната SQL грешка `Unknown column 'module' in 'WHERE'` при migration cleanup-а.
- Стандартната OpenCart 3 таблица `layout_module` използва колоната `code`, а не `module`.
- Cleanup логиката вече открива наличната колона чрез `SHOW COLUMNS`, използва `code` при стандартен OpenCart 3 и запазва безопасен fallback за forks.
- Премахнат е грешният early return при вътрешна версия `1.0.1`, така че migration-ът е идемпотентен и може безопасно да се изпълни повторно след прекъснат upgrade.
- Вътрешната model версия е синхронизирана с `1.0.4`.

### fix — English

- Fixed the fatal migration SQL error `Unknown column 'module' in 'WHERE'`.
- Standard OpenCart 3 uses the `code` column in the `layout_module` table, not `module`.
- Cleanup now detects the available column with `SHOW COLUMNS`, uses `code` on standard OpenCart 3, and keeps a safe fallback for forks.
- Removed the incorrect early return for internal version `1.0.1`, making migrations idempotent and safe to rerun after an interrupted upgrade.
- Synchronized the internal model version with `1.0.4`.

## [1.0.3] - 2026-08-24

### fix — Български

- Коригиран е проблемът **„Разрешението е отказано“** за табовете **Категории** и **Статии**.
- Причината е OpenCart 3 startup permission логиката: routes `extension/probg_blog/category` и `extension/probg_blog/article` се проверяват като родителски route `extension/probg_blog`.
- Инсталацията и upgrade логиката вече добавят `access` и `modify` право и за `extension/probg_blog`, освен съществуващите права за основния модул и CRUD routes.
- Не се променя core permission контролерът и не се използва глобален permission bypass.
- Версията е обновена до `1.0.3`.

### fix — English

- Fixed **Permission Denied** for the **Categories** and **Articles** tabs.
- Root cause: OpenCart 3 startup permission logic checks `extension/probg_blog/category` and `extension/probg_blog/article` as the parent route `extension/probg_blog`.
- Install and upgrade permission provisioning now grants `access` and `modify` for `extension/probg_blog` in addition to the existing main-module and CRUD permissions.
- No core permission controller modification or global permission bypass is used.
- Updated the version to `1.0.3`.

## [1.0.2] - 2026-08-24

### fix — Български

- Коригиран е проблемът **„Разрешението е отказано“** при отваряне на табовете **Категории** и **Статии** след обновяване на вече инсталиран модул.
- При отваряне на **ProBG Blog → Настройки** потребителска група, която вече има `modify` право за `extension/module/probg_blog`, автоматично получава `access` и `modify` права за `extension/probg_blog/category` и `extension/probg_blog/article`.
- Правата не се разширяват за групи, които нямат право да променят основния ProBG Blog модул.
- Добавен е отделен admin stylesheet за коректно визуализиране на обзорните блокове Категории/Статии при admin теми, в които OpenCart `.tile` стиловете дават бял текст и икони върху бял фон.
- Версията на OCMOD и административната версия са обновени до `1.0.2`.

### fix — English

- Fixed **Permission Denied** when opening the integrated **Categories** and **Articles** tabs after upgrading an existing installation.
- When **ProBG Blog → Settings** is opened, a user group that already has `modify` permission for `extension/module/probg_blog` automatically receives `access` and `modify` permissions for `extension/probg_blog/category` and `extension/probg_blog/article`.
- Permissions are not expanded for groups that cannot modify the main ProBG Blog module.
- Added a dedicated admin stylesheet so the Categories/Articles overview renders correctly in admin themes where OpenCart `.tile` styles produce white text/icons on a white background.
- Updated the OCMOD and administration version to `1.0.2`.

## [1.0.1] - 2026-08-24

### fix — Български

- Премахнати са legacy отделните layout модули `probg_blog_articles` и `probg_blog_categories`.
- Премахнати са техните admin/catalog controllers, language файлове и Twig шаблони.
- Основният install route вече добавя permissions само за `extension/module/probg_blog`, `extension/probg_blog/category` и `extension/probg_blog/article`.
- Добавена е upgrade миграция, която почиства старите `module` и `layout_module` записи за премахнатите модули.
- Версията на OCMOD пакета и вътрешната версия са обновени до `1.0.1`.
- Запазва се една инсталация **ProBG Blog**, без промяна или загуба на блог съдържание.

### fix — English

- Removed the legacy standalone `probg_blog_articles` and `probg_blog_categories` layout modules.
- Removed their admin/catalog controllers, language files and Twig templates.
- The main install route now grants permissions only for `extension/module/probg_blog`, `extension/probg_blog/category` and `extension/probg_blog/article`.
- Added an upgrade migration that removes stale `module` and `layout_module` rows for the deleted modules.
- Updated the OCMOD package and internal module version to `1.0.1`.
- Keeps a single **ProBG Blog** installation without changing or deleting existing blog content.

## [1.0.0] - 2026-08-15

### beta

- First consolidated beta release of ProBG Blog for OpenCart 3.
- Includes multilingual categories and articles, SEO URLs, canonical redirects, Open Graph, Twitter Cards, JSON-LD, sitemap integration and caching.
- Includes integrated Categories and Articles administration tabs.
- Includes related products for articles using the standard OpenCart product autocomplete.
- Includes the built-in configurable frontend blog menu without requiring a second module installation.
- Release is published as a GitHub prerelease under tag `1.0.0-beta`.

### changed

- Package and internal module version updated to `1.0.0`.
- Release status marked as **Beta**.

## [0.12.0] - 2026-08-14

### feat

- Added built-in blog navigation menu to the existing `ProBG Blog` module.
- Added a **Menu** settings tab with multilingual menu title.
- Added layout output mode selection: **Latest Articles** or **Menu**.
- Added menu options for blog home link, categories, articles, category filter, article limit and sorting.
- Added a dedicated storefront menu template rendered by the existing `extension/module/probg_blog` controller.
- Menu installation is part of the existing ProBG Blog installation; no second module install is required.

### fix

- Upgrading from 0.11.0 keeps existing layout blocks in **Latest Articles** mode, preventing unexpected storefront changes.
- Menu settings receive safe defaults during migration.

### changed

- Internal module version and OCMOD package version updated to `0.12.0`.
- Administration stage updated to Stage 12.

## [0.11.0] - 2026-08-14

### feat

- Added a **Related Products** tab to the blog article add/edit form.
- Added the standard OpenCart product autocomplete selector using `catalog/product/autocomplete`.
- Added support for selecting multiple related products and removing selected items before saving.
- Added `{DB_PREFIX}probg_blog_article_related` for article-to-product relations.
- Added automatic relation persistence on article create/update and cleanup on article deletion.
- Added duplicate protection when saving related product IDs.
- Added related product output below the public article using standard OpenCart `product-thumb` / `product-layout` classes.
- Public related products are resolved through the standard catalog product model, so disabled, unavailable or non-store products are not rendered.

### changed

- Internal module version and OCMOD package version updated to `0.11.0`.
- Administration stage updated to Stage 11.
- Article editor tabs are now **Content / Data / Images / Related Products**.
- README and development stages documentation updated.

## [0.10.0] - 2026-08-14

### feat

- Integrated **Articles** as a main ProBG Blog administration tab alongside Settings and Categories.
- Added the shared **Settings / Categories / Articles** navigation to the article list, add and edit screens.
- Kept the article editor organized in its existing Content, Data and Images sub-tabs.
- Preserved the complete article CRUD workflow, filters, pagination, image manager integration and multilingual content fields inside the new tabbed administration flow.

### fix

- The article list filter now always receives the current `user_token`, preventing filtered administration URLs from losing authentication context.
- Updated the article SEO URL help text to match the implemented automatic `ID + transliterated title` generation behavior.

### changed

- Internal module version and OCMOD package version updated to `0.10.0`.
- Administration stage updated to Stage 10.

## [0.9.0] - 2026-08-14

### feat

- Added integrated top-level **Categories** tab to the ProBG Blog administration navigation.
- Added multilingual category tags.
- Added standard OpenCart store assignment for blog categories.
- Added per-store category Layout Override support.
- Added `{DB_PREFIX}probg_blog_category_to_store` and `{DB_PREFIX}probg_blog_category_to_layout` tables.
- Added storefront filtering of categories, articles and sitemap data by current store.
- Added category Layout Override support to category and article pages.
- Added multi-store SEO URL replication for articles based on their category store assignments.

### fix

- Empty category Meta Title is populated from the multilingual Title.
- Empty category SEO URL is now generated from the transliterated Title without the legacy category ID prefix.
- Existing category SEO URLs are preserved during migration; new empty category SEO URLs are generated from the title.
- Existing categories are assigned to all current stores during migration so upgrading from 0.8.0 does not hide existing content.
- Missing category and article SEO URL rows are created for each assigned store during migration.
- SEO keyword uniqueness checks now support store-specific resolution.

### changed

- Category form reorganized into **General** and **Settings** tabs.
- Module internal version and OCMOD package version updated to `0.9.0`.
- Administration stage updated to Stage 9.

## [0.8.0] - 2026-08-05

### feat

- Added standalone blog XML sitemap and Google Sitemap integration.
- Added catalog cache by store and language.
- Added sitemap/cache settings.
- Added `ProBG Blog — Articles` and `ProBG Blog — Categories` layout modules.

### fix

- Improved canonical path normalization and layout module URL scheme handling.

## [0.7.0] - 2026-08-05

### feat

- Added Open Graph and Twitter Card metadata.
- Added social image fallback.
- Added JSON-LD `Blog`, `BlogPosting`, `BreadcrumbList`, `CollectionPage` and Organization data.

## [0.6.0] - 2026-08-05

### feat

- Added automatic Meta Title fallback.
- Added Bulgarian transliteration helper.
- Added automatic section/category/article SEO URL generation.
- Added canonical URLs and 301 redirects.
- Added SEO migration and cache invalidation.

## [0.5.0] - 2026-08-05

### feat

- Added complete storefront blog section, category pages and article pages.
- Added breadcrumbs, pagination, gallery and 404 handling.
- Added SEO routing for hierarchical blog URLs.

## [0.4.0] - 2026-08-05

### feat

- Added multilingual blog section settings and metadata.
- Added configurable ordering, limits and image sizes.

## [0.3.0] - 2026-08-05

### feat

- Added full article CRUD administration.
- Added article images, multilingual content, filters and SEO URL management.

## [0.2.0] - 2026-08-05

### feat

- Added full category CRUD administration.
- Added multilingual descriptions and metadata, filtering, sorting and deletion protection.

## [0.1.0] - 2026-08-05

### feat

- Initial OpenCart 3 module structure.
- Added install/uninstall routines, database schema, permissions, admin menu and initial storefront endpoint.
