# Changelog

All notable changes to the **AEON Digital Marketing** theme are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
