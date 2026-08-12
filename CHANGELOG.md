# Changelog

## [0.8.0] - 2026-08-05

### Added

- Standalone blog XML sitemap at `extension/feed/probg_blog_sitemap`.
- OCMOD integration that appends the blog section, active categories and active articles to the standard OpenCart Google Sitemap feed.
- Sitemap `lastmod`, `changefreq` and `priority` values using canonical blog URLs.
- Catalog caching by store and language for categories, article lists, totals, individual records, gallery images and sitemap data.
- Configurable sitemap and caching status controls in a new Integrations and Performance settings tab.
- Automatic default enablement of sitemap integration and caching when upgrading from 0.7.0.
- `ProBG Blog — Articles` layout module with multilingual title, category selection, limit, ordering and display toggles.
- `ProBG Blog — Categories` layout module with multilingual title, category limit and article count toggle.
- Automatic permissions for the two new layout module routes after installation or upgrade.
- Direct sitemap URL display in the administration settings.

### Changed

- Administration dashboard stage information updated to Stage 8.
- Internal module version and OCMOD package version updated to `0.8.0`.
- Catalog data access now uses the shared blog cache when enabled.
- README and staged development documentation finalized for the completed 0.1.0–0.8.0 roadmap.

### Fixed

- Removed a duplicated canonical path normalization statement in the storefront controller.
- Layout module links now preserve the current HTTP or HTTPS request scheme.

## [0.7.0] - 2026-08-05

### Added

- Page-specific Open Graph metadata for the blog section, category listings and article pages.
- Article Open Graph properties for publication time, modification time and category section.
- Twitter Card metadata with automatic `summary_large_image` or `summary` selection.
- Social sharing image fallback chain: article image, module default image, then store logo.
- Social image generation at 1200 × 630 pixels with image dimensions and alt text.
- JSON-LD `Blog` markup for the main blog section.
- JSON-LD `BlogPosting` markup for articles, including `datePublished`, `dateModified`, `articleSection`, body, publisher and author.
- JSON-LD `BreadcrumbList` markup for section, category and article pages.
- JSON-LD `CollectionPage` markup for category and paginated listing pages.
- Organization publisher data using the OpenCart store name, home URL and logo.
- Safe JSON encoding and escaped social metadata output in the storefront `<head>`.
- OCMOD integration with the common header controller and all compatible theme header Twig files.

### Changed

- Administration dashboard stage information updated to Stage 7.
- Internal module version and OCMOD package version updated to `0.7.0`.
- README and staged development documentation updated.

## [0.6.0] - 2026-08-05

### Added

- Automatic Meta Title population from the multilingual title when the field is empty.
- Bulgarian Cyrillic transliteration helper compatible with the OpenCart 3 PHP codebase.
- Automatic multilingual section SEO URL generation from the section title.
- Automatic category SEO URLs using `category_id-transliterated-title`.
- Automatic article SEO URLs using `article_id-transliterated-title`.
- Automatic numeric suffixes for generated SEO keywords that conflict with an existing keyword.
- One-time upgrade routine for existing 0.5.0 settings, categories and articles.
- Self-referencing canonical links for the blog section, paginated listings, categories and articles.
- Permanent 301 redirects from direct, duplicated or incorrect hierarchical blog URLs to the canonical URL.
- Category correction when an article is requested through the wrong category path.
- Blog cache invalidation after category and article create, edit and delete operations.
- Shared `ProbgBlogSeo` system library for slug generation.

### Changed

- Manual Meta Title and SEO URL values are preserved and never overwritten by automatic generation.
- SEO URL help text now describes the automatic generation behavior.
- Administration dashboard stage information updated to Stage 6.
- OCMOD package version updated to `0.6.0`.
- README and staged development documentation updated.

## [0.5.0] - 2026-08-05

### Added

- Complete storefront blog section with a paginated article list.
- Public category pages with category metadata and filtered articles.
- Public article pages with main image, HTML content and additional image gallery.
- Responsive default-theme Twig templates compatible with the OpenCart 3 Bootstrap layout.
- Breadcrumbs for the blog section, categories and articles.
- Storefront pagination using the configured article limit.
- Article ordering by date or manual sort order using the module setting.
- Default image and configured image dimensions in storefront lists and article pages.
- Storefront 404 handling for missing, disabled or untranslated blog records.
- Compact layout-module output for the latest blog articles.
- Catalog model for active multilingual categories, articles and gallery images.
- OCMOD SEO routing support for the section, category and article URL segments.

### Changed

- Section SEO URL records now use the correct custom route query `extension/module/probg_blog`; legacy records are read and replaced safely.
- Storefront links are generated in section → category → article order.
- Administration dashboard stage information updated to Stage 5.
- OCMOD package version updated to `0.5.0`.
- README and staged development documentation updated.

## [0.4.0] - 2026-08-05

### Added

- Full multilingual blog section settings.
- Multilingual section title and Summernote HTML description.
- Multilingual Meta Title, Meta Description and Meta Keywords.
- Multilingual section SEO URL stored in the standard OpenCart `seo_url` table.
- SEO URL uniqueness, format, whitespace and length validation.
- Article ordering by date or manual sort order.
- Configurable storefront article limit.
- Default article image selection using the OpenCart Image Manager.
- Configurable list, article and additional image dimensions.
- Validation for multilingual titles, metadata, ordering, limits and image dimensions.
- Default values for newly installed modules.
- Blog cache invalidation after settings changes and uninstall.

### Changed

- Administration settings screen reorganized into General, Data and Images tabs.
- Administration dashboard stage information updated to Stage 4.
- OCMOD package version updated to `0.4.0`.
- README and staged development documentation updated.

## [0.3.0] - 2026-08-05

### Added

- Full CRUD administration for blog articles.
- Required primary category selection with category existence validation.
- Multilingual title, short Summernote description and full Summernote description.
- Multilingual Meta Title, Meta Description and Meta Keywords.
- Multilingual manual SEO URL storage using the standard OpenCart `seo_url` table.
- SEO URL uniqueness, format, whitespace and length validation.
- Main article image using the standard OpenCart Image Manager.
- Unlimited additional article images with individual sort order.
- Article filters for title, category, status and date-added range.
- Sorting by ID, title, category, sort order, status, date added and date modified.
- Administration pagination and bulk deletion.
- Automatic cleanup of descriptions, additional images and SEO URLs when an article is deleted.
- Bulgarian and English article administration translations.

### Changed

- Administration dashboard stage information updated to Stage 3.
- OCMOD package version updated to `0.3.0`.
- README and staged development documentation updated.

## [0.2.0] - 2026-08-05

### Added

- Full CRUD administration for blog categories.
- Multilingual category title and Summernote HTML description.
- Multilingual Meta Title, Meta Description and Meta Keywords.
- Multilingual manual SEO URL storage using the standard OpenCart `seo_url` table.
- SEO URL uniqueness, whitespace and length validation.
- Category list filters for title and status.
- Sorting by title, sort order, status, date added and date modified.
- Administration pagination and bulk deletion.
- Automatic date added and date modified handling.
- Protection against deleting categories assigned to articles.
- Bulgarian and English category administration translations.

### Changed

- Administration dashboard stage information updated to Stage 2.
- OCMOD package version updated to `0.2.0`.

## [0.1.0] - 2026-08-05

### Added

- Initial OpenCart 3 module structure.
- Install and uninstall database routines.
- Blog category and article database schema.
- Additional article images table.
- Bulgarian and English administration language files.
- Administration routes for settings, categories and articles.
- Automatic access and modify permissions for the installing administrator group.
- OCMOD administration menu with Articles, Article Categories and Settings.
- Initial storefront module endpoint and template.
- README and staged development documentation.
