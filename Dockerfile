# Production image: the official WordPress image with this repo's theme and
# plugin built in. Deployed on Coolify with docker-compose.yml; see the README,
# "Deploying with Coolify".
#
# No WordPress version in the tag: the official image is only built for the
# latest release, so a pinned version would stop getting security fixes once
# the next release is out. Coolify pulls the tag on every build, and the cron
# scheduled task applies any database update (README, "WordPress updates").
FROM wordpress:php8.4-apache

# WP-CLI, for installing and seeding from Coolify's terminal and for scheduled
# tasks. docker/wp runs it as www-data.
COPY --from=wordpress:cli-2-php8.4 /usr/local/bin/wp /usr/local/bin/wp-cli.phar
COPY --chmod=0755 docker/wp /usr/local/bin/wp

# The MariaDB client, which WP-CLI's database commands (wp db import, export) run.
RUN apt-get update \
	&& apt-get install -y --no-install-recommends mariadb-client \
	&& rm -rf /var/lib/apt/lists/*

# Long browser caching for static files (the official image enables mod_expires)
# and upload limits that fit phone photos.
COPY docker/apache-cache.conf /etc/apache2/conf-enabled/lampandpath-cache.conf
COPY docker/uploads.ini /usr/local/etc/php/conf.d/lampandpath-uploads.ini

# The code goes where the official image keeps WordPress; docker/entrypoint.sh
# copies it into the web root on every start. Akismet and Hello Dolly come with
# WordPress but the site does not use them, so they are left out.
RUN rm -rf /usr/src/wordpress/wp-content/plugins/akismet /usr/src/wordpress/wp-content/plugins/hello.php
COPY --chown=www-data:www-data wp-content/themes/lampandpath /usr/src/wordpress/wp-content/themes/lampandpath
COPY --chown=www-data:www-data wp-content/plugins/lampandpath-core /usr/src/wordpress/wp-content/plugins/lampandpath-core

COPY --chmod=0755 docker/entrypoint.sh /usr/local/bin/lampandpath-entrypoint.sh
ENTRYPOINT ["lampandpath-entrypoint.sh"]
CMD ["apache2-foreground"]
