# Seema Cabs Goa — single image serving the static marketing site and the
# TaxiGo Laravel app (admin panel, customer PWA at /app, REST API at /api).
#
# The build context is the REPOSITORY ROOT, not taxigo/, because the marketing
# pages live at the root:
#
#   docker build -f taxigo/Dockerfile -t seemacabsgoa .
#
# See taxigo/DEPLOYMENT.md for the Coolify setup.

FROM serversideup/php:8.2-fpm-nginx

# phpoffice/phpspreadsheet (Excel export) and Intervention Image (WebP
# conversion) need GD; zip, pdo_mysql, opcache etc. ship with the base image.
USER root
RUN install-php-extensions gd
USER www-data

WORKDIR /var/www/html

# Composer dependencies first so this layer is cached between code changes.
COPY --chown=www-data:www-data taxigo/composer.json taxigo/composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

# Application code. public/build is committed, so no Node build is needed.
COPY --chown=www-data:www-data taxigo/ ./
RUN composer dump-autoload --optimize --no-dev

# Private ad files (licences, original images) live on their own Coolify
# volume. Creating the folders here, owned by www-data, means a new empty
# volume mounted at storage/app/ads starts with the right owner.
RUN mkdir -p storage/app/ads/originals storage/app/ads/licences

# Static marketing site. The pages go to resources/marketing (not the web
# root) and are served by Laravel, so suspension for unpaid invoices covers
# them instantly; their CSS, images and the error pages stay public.
COPY --chown=www-data:www-data *.html resources/marketing/
COPY --chown=www-data:www-data 404.html 502.html style.css robots.txt sitemap.xml Seema_Cabs_Goa_Logo.png Seema_Cabs_Goa_Logo_black.png public/
COPY --chown=www-data:www-data Faviocn public/Faviocn
COPY --chown=www-data:www-data __files public/__files
COPY --chown=www-data:www-data mail public/mail

# serversideup "autorun" on container start: storage:link plus config/route/
# view/event caching. Migrations stay manual because the database is the live
# production database — run them deliberately (see DEPLOYMENT.md).
ENV AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_MIGRATION=false \
    PHP_OPCACHE_ENABLE=1 \
    LOG_CHANNEL=stderr

# nginx listens on 8080; the base image provides a /healthcheck endpoint.
EXPOSE 8080
