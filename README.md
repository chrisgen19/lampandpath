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

- **Structure:** site title, timezone (Asia/Manila), permalinks, categories, topic tags, pages, menus and reading settings
- **Demo content:** 6 writers, the design's 11 articles (with featured images from `plugins/lampandpath-core/seed/images/`), the "Where are you today?" collections, today's verse plus 7 scheduled ones, 3 reading plans and 4 prayer requests (one awaiting approval). Dates are relative to the day you run it.

Every run resets the site title, tagline, permalink structure (`/%postname%/`), reading settings (static front page "Home", posts page "Articles") and privacy policy page to the design's values. The timezone is only set while the site is still on WordPress's default (UTC), so a timezone chosen in Settings > General is kept. The exception is "UTC+0", which WordPress saves exactly like the default: to keep a site on UTC, choose "UTC" instead. When it switches, existing verses move to midnight Manila time on their day. The timezone is saved only after every verse has moved, so if one fails the site stays on UTC and the next run tries again. Everything else is reused and never overwritten, so it is safe to run again: menus only fill empty menu locations, existing pages keep their content and (unless they are drafts) their status, and view counts are only set on new articles. WordPress's own "Hello world!" post and "Sample Page" are moved to the trash, but only while they are unedited.

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
| Homepage headings, intros, button labels and links | Appearance > Customize > Homepage | Each section can be hidden. The featured article is the latest sticky post (or the latest post). |
| Homepage filter chips and topics | Appearance > Menus | Menus in the "Homepage: article filter chips" and "Homepage: browse by topic" locations. The filter chips also appear on the Articles page and article archives. |
| Articles page intro | Pages > Articles | The page's content is shown under its title (the site tagline when empty) |
| Our writers and Prayer wall pages | Pages, "Template" setting | The "Our writers" and "Prayer wall" templates; the seed sets them on the seeded pages. The page content is the intro. |

Comments are switched off site-wide by `lampandpath-core` (`includes/comments.php`): no comment forms or pingbacks, earlier comments hidden from pages, feeds and the REST API, and no Comments screens in wp-admin. Block editor notes (editors' comments on blocks) still work. The prayer wall is where readers respond.

## Inner pages

Only the homepage has a design; the other templates reuse its tokens and components.

| URL | Template |
|---|---|
| An article | `single.php`: header like the homepage featured article (portrait images in the arched frame, landscape ones in a rounded 3:2 frame), content, topics and collections, author box, newsletter card, "Keep reading" |
| `/articles/`, categories, topics, dates | `home.php`, `archive.php`: filter chips, article cards and the "More to read" sidebar |
| `/author/<name>/` | `author.php`: writer profile and articles |
| `/collections/<name>/` | `taxonomy-lp_need.php`: icon, description, verse link, the other collections |
| `/reading-plans/` and a plan | `archive-lp_plan.php`, `single-lp_plan.php`: plan cards; overview and a Bible Gateway link per day |
| `/verses/` and a verse | `archive-lp_verse.php`, `single-lp_verse.php`: past verses; a verse page for shared links |
| Search, missing pages, other pages | `search.php`, `404.php`, `page.php` |

Lists page with numbered links, and `assets/js/feed.js` turns them into a "Load more" button that loads the next page in place.

## Forms and interactions

The prayer request and newsletter forms, "I prayed" and the article view counter are handled by `lampandpath-core`; the theme's `assets/js/interactions.js` calls them and also runs the topic filters, Load more, Copy and Share, and Save for later.

- **REST routes** (`/wp-json/lampandpath/v1/`): `GET token`, `POST prayers`, `POST prayers/<id>/prayed`, `POST subscribers`, `POST views/<id>` (plugin) and `GET articles` (theme, returns rendered cards). Write routes need a fresh token in the `X-LP-Token` header, fetched just before submitting, so cached pages keep working.
- **Without JavaScript** the forms post to `admin-post.php` and show the result after the redirect; the filters and Load more are normal links. The form nonce is only checked for logged-in users, so cached pages served to visitors never go stale.
- **Spam and abuse:** a hidden honeypot field, a 3-second time trap, per-visitor rate limits (salted IP hash, never the IP itself) and length and email validation. Rate limits and duplicate-subscriber checks run under a database lock (`GET_LOCK`), so parallel requests cannot slip past them. With JavaScript the time trap is measured in the browser; without it, it uses the page's generation time, so it only helps on uncached pages (the honeypot and rate limits always apply). Raise a limit for shared networks with the `lampandpath_rate_limit` filter.
- **Prayer requests** are saved as pending and emailed to the site admin email (change the recipient with `lampandpath_prayer_notify_email`). Publishing a request approves it; it only appears on the prayer wall if the person agreed.
- **Newsletter subscribers** are stored under Subscribers. To send them to a mailing provider, hook `lampandpath_newsletter_subscribed` (receives the email and subscriber ID).
- **Save for later, "I prayed" state and view de-duplication** are stored in the visitor's browser (`localStorage`), with no accounts.

## Theme development

The theme uses [Tailwind CSS v4](https://tailwindcss.com). Styles are written in `src/css/` and built to `assets/css/`, which is committed because the production host has no Node:

- `app.css`: the front end
- `editor.css`: the block editor canvas, so articles look the same while editing
- `tokens.css` (design tokens) and `content.css` (article and page body copy) are shared by both

```bash
cd wp-content/themes/lampandpath
pnpm install
pnpm dev     # rebuild app.css on every change
pnpm build   # minified build of both files; run before committing
```

The block editor offers the design's colors (the Season color entries follow the Customizer) and type sizes only.

Tailwind finds class names by scanning the theme's PHP and JS files, so always write complete class strings (never build them like `'bg-' . $color`).

Season color, header and footer content are edited in Appearance > Customize.
