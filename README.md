# Lamp and Path

WordPress site for Lamp and Path, developed locally with [DDEV](https://ddev.com).

## What is in this repo

Only code we own is tracked (see `.gitignore`):

- `.ddev/config.yaml`: local environment (PHP 8.4, nginx-fpm, MariaDB 11.8)
- `wp-content/themes/lampandpath/`: the custom theme
- `wp-content/mu-plugins/` and whitelisted custom plugins, when added

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

or do a fresh install:

```bash
ddev wp core install --url='https://lampandpath.ddev.site' --title='Lamp and Path' \
  --admin_user=admin --admin_email=you@example.com --prompt=admin_password
ddev wp theme activate lampandpath
```

The site runs at https://lampandpath.ddev.site.
