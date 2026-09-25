#!/bin/sh
# Refreshes the code in the persistent web root on every container start, then
# hands over to the official WordPress entrypoint.
#
# /var/www/html is a volume so that uploads and wp-config.php survive
# redeploys. The official entrypoint only copies WordPress into it when it is
# empty, so without this step every deploy after the first would keep serving
# the old core, themes and plugins. The image is the single source of truth for
# code; wp-content (uploads) and wp-config.php are left alone.
#
# Each copy is a find -exec ... + so that a failed cp fails find, and set -e
# stops the start instead of serving a partly copied site.
set -eu

src=/usr/src/wordpress
dest=/var/www/html

# WordPress core: everything except wp-content. The old copy goes first, so a
# file that a newer WordPress no longer ships cannot linger and stay reachable
# (WordPress's own updater deletes those too). Only wp-content and
# wp-config.php are kept; the rest of the web root always matches the image.
find "$dest" -mindepth 1 -maxdepth 1 ! -name wp-content ! -name wp-config.php -exec rm -rf {} +
find "$src" -mindepth 1 -maxdepth 1 ! -name wp-content -exec cp -a -t "$dest" {} +

# Themes and plugins, replaced as a whole for the same reason. wp-admin cannot
# install or update either (DISALLOW_FILE_MODS), so the image holds them all.
mkdir -p "$dest/wp-content"
for dir in themes plugins; do
	rm -rf "${dest:?}/wp-content/$dir"
	cp -a "$src/wp-content/$dir" "$dest/wp-content/$dir"
done

# The rest of the image's wp-content (the index.php guard). Uploads and anything
# else already in wp-content stay.
find "$src/wp-content" -mindepth 1 -maxdepth 1 ! -name themes ! -name plugins -exec cp -a -t "$dest/wp-content" {} +

exec docker-entrypoint.sh "$@"
