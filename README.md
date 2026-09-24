# Lamp and Path

WordPress site for Lamp and Path, developed locally with [DDEV](https://ddev.com).

The build plan (phases, content model, open questions) lives in [issue #1](https://github.com/chrisgen19/lampandpath/issues/1).

## What is in this repo

Only code we own is tracked (see `.gitignore`):

- `.ddev/config.yaml`: local environment (PHP 8.4, nginx-fpm, MariaDB 11.8)
- `wp-content/themes/lampandpath/`: the theme (presentation only)
- `wp-content/plugins/lampandpath-core/`: content model, forms and the `wp lampandpath seed` command
- `docs/design/homepage.html`: the homepage design reference the theme is built from

Not tracked: WordPress core, `wp-config.php`, `wp-content/uploads/`, third-party plugins and the database.

## Local setup

```bash
git clone https://github.com/chrisgen19/lampandpath.git
cd lampandpath
ddev start                  # also generates wp-config.php and wp-config-ddev.php
ddev wp core download
```

Then either import a database dump:

```bash
ddev import-db --file=path/to/dump.sql.gz
```

or do a fresh install and seed it:

```bash
ddev wp core install --url='https://lampandpath.ddev.site' --title='Lamp & Path' \
  --admin_user=admin --admin_email=you@example.com --prompt=admin_password
ddev wp theme activate lampandpath
ddev wp plugin activate lampandpath-core
ddev wp lampandpath seed
```

The site runs at https://lampandpath.ddev.site.

## Seeding content

`ddev wp lampandpath seed` sets the site title, permalinks, categories, pages, menus and reading settings from the design. It reuses anything that already exists, so it is safe to run again: menus only fill empty menu locations, and existing pages keep their content and (unless they are drafts) their status. Pass `--reset-menus` to rebuild the seeded menus and reassign their locations (this discards menu edits made in wp-admin). The command exits with an error if a menu links to a page or category that does not exist.

## Theme development

The theme uses [Tailwind CSS v4](https://tailwindcss.com). Styles are written in `src/css/app.css` and built to `assets/css/app.css`, which is committed because the production host has no Node.

```bash
cd wp-content/themes/lampandpath
pnpm install
pnpm dev     # rebuild on every change
pnpm build   # minified build; run before committing
```

Tailwind finds class names by scanning the theme's PHP and JS files, so always write complete class strings (never build them like `'bg-' . $color`).

Season color, header and footer content are edited in Appearance > Customize.
