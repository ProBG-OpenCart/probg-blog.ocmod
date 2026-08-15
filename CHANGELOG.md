# Changelog

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
