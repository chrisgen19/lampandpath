# Production image: the official WordPress image with this repo's theme and
# plugin built in. Deployed on Coolify with docker-compose.yml; see the README,
# "Deploying with Coolify".
FROM wordpress:7.1-php8.4-apache

# WP-CLI, for installing and seeding from Coolify's terminal and for scheduled
# tasks. docker/wp runs it as www-data.
COPY --from=wordpress:cli-2-php8.4 /usr/local/bin/wp /usr/local/bin/wp-cli.phar
COPY --chmod=0755 docker/wp /usr/local/bin/wp

# Long browser caching for static files.
RUN a2enmod expires headers
COPY docker/apache-cache.conf /etc/apache2/conf-enabled/lampandpath-cache.conf

# The code goes where the official image keeps WordPress; docker/entrypoint.sh
# copies it into the web root on every start.
COPY --chown=www-data:www-data wp-content/themes/lampandpath /usr/src/wordpress/wp-content/themes/lampandpath
COPY --chown=www-data:www-data wp-content/plugins/lampandpath-core /usr/src/wordpress/wp-content/plugins/lampandpath-core

COPY --chmod=0755 docker/entrypoint.sh /usr/local/bin/lampandpath-entrypoint.sh
ENTRYPOINT ["lampandpath-entrypoint.sh"]
CMD ["apache2-foreground"]
