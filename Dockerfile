FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public ./public
RUN npm run build

FROM php:8.3-apache AS app
RUN apt-get update && apt-get install -y --no-install-recommends libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libxml2-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_sqlite mbstring dom xml gd opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/* \
    && php -r 'foreach (["ctype", "dom", "fileinfo", "filter", "gd", "hash", "iconv", "json", "libxml", "mbstring", "openssl", "PDO", "pdo_mysql", "pdo_sqlite", "pcre", "session", "tokenizer", "xml"] as $extension) { if (! extension_loaded($extension)) { fwrite(STDERR, "Required PHP extension missing: {$extension}\n"); exit(1); } }'
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY lang ./lang
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage
COPY artisan .
COPY --from=assets /app/public/build ./public/build
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R u+rwX,g+rwX storage bootstrap/cache \
    && printf '%s\n' '<VirtualHost *:80>' 'DocumentRoot /var/www/html/public' '<Directory /var/www/html/public>' 'AllowOverride All' 'Require all granted' '</Directory>' '</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/apikawal-entrypoint
RUN chmod +x /usr/local/bin/apikawal-entrypoint
EXPOSE 80
ENTRYPOINT ["apikawal-entrypoint"]
CMD ["apache2-foreground"]
