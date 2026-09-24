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

`ddev wp lampandpath seed` sets up the site from the design:

- **Structure:** site title, permalinks, categories, topic tags, pages, menus and reading settings
- **Demo content:** 6 writers, the design's 11 articles (with featured images from `plugins/lampandpath-core/seed/images/`), the "Where are you today?" collections, today's verse plus 7 scheduled ones, 3 reading plans and 4 prayer requests (one awaiting approval). Dates are relative to the day you run it.

Every run resets the site title, tagline, permalink structure (`/%postname%/`) and reading settings (static front page "Home", posts page "Articles") to the design's values. Everything else is reused and never overwritten, so it is safe to run again: menus only fill empty menu locations, existing pages keep their content and (unless they are drafts) their status, and view counts are only set on new articles. WordPress's own "Hello world!" post and "Sample Page" are moved to the trash, but only while they are unedited.

- `--reset-menus` rebuilds the seeded menus and reassigns their locations (this discards menu edits made in wp-admin)
- `--skip-content` only sets up the structure, with no demo writers, articles, verses, plans, prayers or images

The command exits with an error if anything fails, for example a menu linking to a page that does not exist. Seeded writers get random passwords and `@example.com` emails; reset a password in wp-admin to log in as one.

## Content model

The `lampandpath-core` plugin owns the content; the theme only renders it (via the functions in `includes/template-api.php`).

| Content | Where to edit | Notes |
|---|---|---|
| Articles | Posts | Categories, topic tags and Collections; optional manual reading time in the "Reading time" box |
| Verse of the day | Verses of the day | Title is the reference. The latest verse whose date has arrived is today's, so schedule verses ahead. |
| Reading plans | Reading plans | One reading per line; the number of lines is the plan length. "Order" sets the homepage order. |
| Prayer requests | Prayer requests (editors and admins only) | Publishing approves a request; it only appears on the prayer wall if the person agreed |
| Collections | Posts > Collections | Icon, verse reference and link, order |
| Writers | Users > Profile | Role title and avatar color; admins choose who appears in the About section |

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
