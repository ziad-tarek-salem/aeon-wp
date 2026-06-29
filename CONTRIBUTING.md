# Contributing

Thanks for your interest in improving the **AEON Digital Marketing** theme.

## Local development

The custom theme lives at `app/wp-content/themes/aeon/`. It runs only inside a
WordPress install (PHP + MySQL + WordPress core). This repo bundles a full local
WP under `app/`; bring the site up with **Laragon** (point a vhost at `app/`,
create an `aeon` database) or the WP-CLI built-in server:

```bash
php wp-cli.phar server --path=app --host=127.0.0.1 --port=8089
# then open http://127.0.0.1:8089
```

See the [README](README.md) for the full local setup, install path, and
first-time setup.

### Previewing the design without WordPress

The static mockup in [`docs/preview.html`](docs/preview.html) renders the
homepage using the **real** theme `assets/css/main.css` and `assets/js/app.js`,
so you can iterate on styles without a running WordPress. Open it directly in a
browser or serve `docs/` with any static file server.

## Coding standards

- Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
  for PHP, CSS, and JS. Indent with **tabs** (enforced by `.editorconfig`).
- All user-facing copy goes through the bilingual layer in `inc/i18n.php`
  (`aeon_t()` / `aeon_e()`) with both `ar` and `en` values — never hard-code
  strings in templates.
- Escape on output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- Keep layout direction-agnostic: use CSS logical properties
  (`inset-inline-*`, `margin-block-*`) so RTL and LTR mirror automatically.
- Lint PHP before submitting (scoped to the theme):
  ```bash
  find app/wp-content/themes/aeon -name '*.php' -print0 | xargs -0 -n1 php -l
  ```

## Building an installable zip

```bash
bash tools/package-theme.sh                                       # macOS/Linux
powershell -ExecutionPolicy Bypass -File tools/package-theme.ps1  # Windows
```

This produces `dist/aeon.zip` (dev files excluded, forward-slash paths), ready
for *Appearance → Themes → Add New → Upload Theme*.

## Pull requests

- Keep changes focused and explain **what** changed and **why**.
- Add an entry to `CHANGELOG.md` under an `## [Unreleased]` heading.
- Verify both languages (Arabic RTL and English LTR) and check
  reduced-motion behavior where relevant.
