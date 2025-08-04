FROM php:8.4-fpm

# Install system dependencies including netcat-openbsd
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    nginx \
    supervisor \
    netcat-openbsd \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY . /var/www/html

RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

COPY ./docker-setup/docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker-setup/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN composer install --ignore-platform-req=php --no-dev --optimize-autoloader --verbose

RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY ./docker-setup/docker/script/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

EXPOSE 10000

CMD ["bash", "/usr/local/bin/startup.sh"]
