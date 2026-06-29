# Changelog

All notable changes to the **AEON Digital Marketing** theme are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
