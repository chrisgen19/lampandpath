#!/bin/sh
# Refreshes the code in the persistent web root on every container start, then
# hands over to the official WordPress entrypoint.
#
# /var/www/html is a volume so that uploads and wp-config.php survive
# redeploys. The official entrypoint only copies WordPress into it when it is
# empty, so without this step every deploy after the first would keep serving
# the old core, theme and plugin. The image is the single source of truth for
# code; wp-content (uploads) and wp-config.php are left alone.
set -eu

src=/usr/src/wordpress
dest=/var/www/html

# WordPress core: everything except wp-content.
tar --create --directory "$src" --exclude=./wp-content . | tar --extract --directory "$dest"

# The default wp-content files (index.php guards, default themes and plugins),
# adding what is missing and leaving everything already there alone. The image
# pre-creates empty wp-content folders, so "is wp-content there" is no test.
tar --create --directory "$src" ./wp-content | tar --extract --directory "$dest" --skip-old-files

# This repo's theme and plugin, replaced so deleted files do not linger.
for dir in themes/lampandpath plugins/lampandpath-core; do
	rm -rf "${dest:?}/wp-content/$dir"
	mkdir -p "$dest/wp-content/$(dirname "$dir")"
	cp -a "$src/wp-content/$dir" "$dest/wp-content/$dir"
done

exec docker-entrypoint.sh "$@"
