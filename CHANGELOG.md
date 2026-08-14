# Changelog

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
