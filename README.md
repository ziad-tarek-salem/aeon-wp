# AEON Digital Marketing — WordPress Theme

A custom, bilingual (Arabic-first RTL / English LTR), fully responsive WordPress
theme for **AEON Digital Marketing** — a UAE-based digital marketing agency.
Hand-coded — no page builder — with tasteful, GPU-friendly animations
(GSAP + Lenis smooth scroll + Swiper).

The custom theme lives at
[`app/wp-content/themes/aeon/`](app/wp-content/themes/aeon). The repository is laid
out as a real WordPress project so the theme sits at its true in-tree path; the WP
core, `wp-config.php`, uploads, and third-party plugins are intentionally **not**
tracked (see `.gitignore`) — only our theme code is.

## Repository layout

```
aeon-wp/
├── app/                                  # WordPress install (core is gitignored)
│   └── wp-content/themes/aeon/           # ← the AEON theme (the tracked code)
│       ├── style.css, functions.php
│       ├── header.php, footer.php, comments.php, searchform.php
│       ├── front-page.php                # homepage (composed of sections)
│       ├── page-about|services|contact.php
│       ├── archive-portfolio.php, single-portfolio.php, single-service.php
│       ├── index.php, single.php, page.php, 404.php
│       ├── inc/                          # setup, i18n, enqueue, cpt, customizer, ajax, helpers
│       ├── template-parts/sections/      # hero, about, services, why, stats, portfolio, …
│       ├── template-parts/components/    # page-banner
│       └── assets/ css | js | images
├── docs/                                 # design preview (preview.html)
├── tools/                                # packaging scripts (package-theme.*)
├── .github/workflows/                    # CI: deploy.yml (FTPS theme deploy)
└── wp-cli.phar                           # WP-CLI (used by .claude/launch.json)
```

## Running it locally

This repo bundles a full local WordPress under `app/` (present on disk, not on
GitHub). Two ways to bring the site up:

### Option A — Laragon (recommended on Windows)

1. Place this repo at `C:\laragon\www\aeon-wp`.
2. Point an Apache vhost's `DocumentRoot` at the `app/` subfolder, create a MySQL
   database named `aeon`, and add a `aeon-wp.test` hosts entry.
3. Start Laragon and open **http://aeon-wp.test** (admin at
   **/wp-admin**). The **AEON Digital Marketing** theme is already active.

### Option B — WP-CLI built-in server

```bash
php wp-cli.phar server --path=app --host=127.0.0.1 --port=8089
# then open http://127.0.0.1:8089
```

(The `.claude/launch.json` "wp" configuration runs exactly this.)

## Building an installable zip (for any WordPress host)

```bash
# Windows
powershell -ExecutionPolicy Bypass -File tools/package-theme.ps1
# macOS/Linux
bash tools/package-theme.sh
```

This produces `dist/aeon.zip` — a clean package containing a single top-level
`aeon/` folder with forward-slash paths, ready for
*Appearance → Themes → Add New → Upload Theme*.

## Features

- 🌐 **Self-contained bilingual layer** — Arabic-first with a one-click `ع / EN`
  toggle. Full RTL↔LTR mirroring via CSS logical properties. No paid plugin.
- 🎨 On-brand design system (purple→violet→orange gradients, Cairo + Poppins).
- 🧩 Custom post types: **Services**, **Work/Portfolio** (+ categories & filter),
  **Testimonials**, plus a private **Leads** store for form submissions.
- 📨 Custom AJAX contact form (nonce-protected, honeypot, `wp_mail`).
- ⚙️ Customizer panels for stats, contact info, and social links.
- ✨ Animations: hero reveal, scroll reveals, count-up stats, hover effects,
  partner marquee, magnetic buttons — all disabled under `prefers-reduced-motion`.
- 📱 Responsive across desktop / tablet / mobile.

## Installing on an existing WordPress site

1. Build `dist/aeon.zip` (above) and upload via
   *WP Admin → Appearance → Themes → Add New → Upload Theme*, **or** copy the
   `aeon` folder into `wp-content/themes/`.
2. Activate **AEON Digital Marketing**.
3. Go to *Settings → Permalinks* and click **Save** once (flushes rewrite rules
   so `/work/`, `/services/` URLs work).

## First-time setup (5 minutes)

1. **Pages** — create these pages and assign their *Page Template* (Page Attributes):
   | Page      | Slug        | Template          |
   |-----------|-------------|-------------------|
   | Home      | `home`      | (default)         |
   | About     | `about`     | About Page        |
   | Services  | `services`  | Services Page     |
   | Contact   | `contact`   | Contact Page      |
   | Blog      | `blog`      | (default)         |

2. **Reading settings** — *Settings → Reading*:
   - *Your homepage displays* → **A static page**
   - Homepage → **Home**, Posts page → **Blog**

3. **Menu** — *Appearance → Menus*: create a menu, add Home/About/Services/
   Work/Blog/Contact, assign to **Primary Menu**. (A sensible fallback menu shows
   automatically until you do.)

4. **Logo** — *Appearance → Customize → Site Identity → Logo* (an SVG wordmark is
   bundled as fallback at `assets/images/logo.svg`).

5. **Contact / Social / Stats** — *Appearance → Customize → AEON · Contact Info /
   Social Links / Stats*. Defaults are pre-filled; update email, phone, WhatsApp,
   address, and the Google Maps embed URL.

## Adding content

- **Services** → *Services* menu in admin. Title + description (excerpt) +
  featured image. Until you add any, the 8 brand services show automatically.
- **Work** → *Work* menu. Add a featured image and assign a *Work Category*
  (categories become the front-end filter buttons). Placeholders show until then.
- **Testimonials** → *Testimonials* menu. Quote in the body, name as title,
  role/company in the side meta box.
- **Leads** → form submissions are emailed to your Contact email **and** stored
  privately under *Leads* so nothing is lost if mail delivery fails.

## Bilingual notes

- Default language is **Arabic (RTL)**. The header toggle switches to English and
  remembers the choice via a cookie.
- All UI + section content strings live in `inc/i18n.php` (`aeon_strings()`),
  with `ar` and `en` values — edit there to tweak copy.
- For **post/page content** in both languages, either author bilingual copy or
  add Polylang/WPML (the theme's UI layer coexists with them).

## Tech / performance

- Third-party libs (GSAP, ScrollTrigger, Lenis, Swiper) are **vendored** in
  `assets/js/lib` + `assets/css/lib` and loaded locally (jsDelivr CDN only as a
  fallback if a file is missing) — no hard runtime dependency on an external CDN.
- Fonts: Google Fonts (Cairo + Poppins) with `display=swap` + preconnect.
- Images lazy-loaded; animations use only `transform`/`opacity`.

## Troubleshooting

**"There has been a critical error on this website" (or white screen).**
1. Enable logging in `wp-config.php`:
   `define('WP_DEBUG', true); define('WP_DEBUG_LOG', true); define('WP_DEBUG_DISPLAY', false);`
2. Reproduce, then read `wp-content/debug.log` — the fatal's file/line is there.
3. Requirements: **PHP ≥ 7.4** (8.0–8.3 supported) and a MySQL/MariaDB database.
   The theme has been lint-checked and runtime-smoke-tested clean on PHP 8.3.

**Theme upload says "stylesheet is missing" / "broken theme".**
The zip must contain a single top-level `aeon/` folder with `style.css` inside,
using forward-slash paths. Always build it with `tools/package-theme.ps1` /
`tools/package-theme.sh` — do **not** zip with Windows Explorer "Send to →
Compressed folder" from inside the folder, and avoid PowerShell's
`Compress-Archive` (it writes backslash paths that break extraction on Linux).

**Pages 404 (e.g. `/work/`, `/services/`).**
*Settings → Permalinks → Save Changes* once (flushes rewrite rules). On Apache,
ensure `mod_rewrite` + `AllowOverride All` so `.htaccess` works; on Nginx use the
standard WordPress `try_files` rule.

**Animations/sliders don't run.**
Check the browser console. Libraries are vendored locally; if you removed
`assets/js/lib/*`, the theme falls back to the jsDelivr CDN (needs outbound
internet / a CSP that allows it).

**Contact form doesn't email.**
`wp_mail` needs a working mail path. Install an SMTP plugin (e.g. WP Mail SMTP).
Submissions are also stored under *Leads* in wp-admin, so nothing is lost.
