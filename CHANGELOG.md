# Changelog

All notable changes to the **AEON Digital Marketing** theme are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **صفحة الخدمات** admin menu (`inc/content/service-galleries.php`), seated
  directly beneath محتوى الموقع, with one screen per service. Each screen manages
  that service's work-samples strip: add images from the media library, replace a
  single image in place, remove one, and reorder by dragging or with the
  per-tile move buttons. Each also exposes the strip's tile aspect ratio and
  column count, which were previously only editable in code.
- Media Library alt text now drives the `alt` on dashboard-managed work samples,
  and a tile whose image has none links straight to the attachment editor to add
  it.
- **The Services page is now entirely dashboard-managed.** Each service gained
  three fields on محتوى الموقع → الخدمات — the intro paragraph, the key
  highlights and the "what's included" list — alongside the name, description and
  icon it already had. The page's own banner and its repeated headings moved to
  صفحة الخدمات → نصوص الصفحة (`inc/content/services-page.php`). No copy on that
  page is rendered from code any more.
- Two field types for the section editors (`inc/content/fields.php`): `textarea`,
  and `lines` — a repeatable list whose rows can be added, deleted, reordered by
  drag or moved with buttons, stored one item per line.
- The content snapshot now carries every service's intro, highlights and
  includes, so the copy the theme shipped with arrives pre-filled in the
  dashboard rather than as an empty form (revision
  `2026-08-03-services-page-content`).
- A sync step that publishes the 28 bundled work samples into the media library
  and seeds each service's gallery with them, so the images the page has always
  shown are now ordinary attachments an administrator can replace, reorder or
  delete. It reuses an image already in the library rather than uploading it
  twice, and skips any gallery the client has already filled, so it is safe to
  repeat. Tracked separately (`aeon_content_gallery_revision`) so a read-only
  uploads folder retries without replaying the term rebuild.

- A **video gallery** for المونتاج وصناعة الفيديو. That service's screen now takes
  uploaded reels (MP4/MOV) instead of stills, with the same add, replace, delete
  and reorder controls, and the front end plays them in place: `<video controls
  playsinline preload="metadata">` in the existing responsive strip, with a
  video's featured image used as its poster frame. Starting one reel pauses
  whichever was already playing. Which services hold video is filterable
  (`aeon_service_gallery_kinds`); every other service is unchanged.
- Galleries now validate medium as well as attachment: an image cannot be saved
  into the video gallery, or a video into an image gallery, whichever route it
  arrives by.

### Fixed
- A gallery item whose file could not be previewed was rendered as nothing while
  staying in the saved list — and since the next save is built from the rendered
  tiles, it disappeared as soon as anything else on the screen was touched. Such
  an item now renders a marked "missing file" tile that has to be dealt with
  deliberately.

### Changed
- `aeon_service_gallery()` now reads nothing but media-library attachments —
  there is no theme-file fallback left in the render path, and each shot carries
  a resolved `url` instead of a theme-relative `file`. Attachments are served at
  `full` size so the four 1280px marketing banners are not swapped for resized
  copies; every rendered file is byte-identical to what the page showed before.
  The image manifest moved out of `template-functions.php` and into the sync
  module as seed data. The files themselves stay in `assets/images/services/`:
  only the theme folder is deployed, so a fresh install has no database to
  inherit them from — the same reason the logo ships bundled.
- `aeon_services_list()` now carries `intro`, `features` and `includes` for each
  service, read from term meta.
- The Services page detail copy is Arabic only, matching the service names and
  descriptions that already came from the dashboard. The English page previously
  paired Arabic titles with English body text; it is now consistent.

### Removed
- The "نظرة عامة" overview screen under صفحة الخدمات. The menu's first entry is
  now نصوص الصفحة, which the parent menu item also opens.
- `aeon_service_details()` and `aeon_service_features()`, and the five
  `svc_*` i18n strings behind the Services page headings — all replaced by
  dashboard content.

## [1.11.0] — 2026-07-28

### Added
- Content parity sync (`inc/content/sync.php`). Only the theme folder is
  deployed, so the database never travels and a live site keeps whatever its
  sections were seeded with months earlier. The theme now carries an
  authoritative snapshot of every section taxonomy plus the settings that drive
  the front end, and reconciles the site to it on the first request after a
  deploy. Bump `AEON_CONTENT_REVISION` to publish new content; a taxonomy that
  already matches is left untouched, and posts, pages, portfolio items and media
  are never touched.
- Transparent `assets/images/logo-wordmark.png`, published to the media library
  and selected as the custom logo by the sync. The previous JPEG sat on an opaque
  white rectangle that showed against the header.

### Changed
- The Hostinger Reach subscription-block CSS and view script are dequeued on
  pages that do not embed the block — the plugin is pre-installed on the host and
  loaded them site-wide.
- `style.css` version realigned with `AEON_VERSION`; the two had drifted apart.

## [1.0.0] — 2026-06-30

### Added
- Initial release of the AEON Digital Marketing WordPress theme.
- Self-contained bilingual layer (Arabic-first RTL / English LTR) with a
  cookie-persisted `ع / EN` toggle and full RTL↔LTR mirroring via CSS logical
  properties — no multilingual plugin required.
- Modular homepage composed of sections: hero, partners, about, services,
  why-us, stats, portfolio, testimonials, and contact.
- Custom post types — Services, Work/Portfolio (with categories and a front-end
  filter), Testimonials — plus a private Leads store for form submissions.
- Nonce-protected AJAX contact form with honeypot, `wp_mail` delivery, and a
  Leads fallback so submissions are never lost.
- Customizer panels for stats, contact info, and social links.
- GPU-friendly animations (GSAP, ScrollTrigger, Lenis, Swiper), all disabled
  under `prefers-reduced-motion`. Third-party libraries are vendored locally.
- Cross-platform packaging script (`tools/package-theme.sh` /
  `tools/package-theme.ps1`) that builds a clean, upload-ready `dist/aeon.zip`.

[1.0.0]: https://www.aeondm.com
