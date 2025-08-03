FROM php:8.4-fpm

# Install system dependencies including netcat
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    nginx \
    supervisor \
    netcat \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy Laravel project files
COPY . /var/www/html

# Remove default nginx config
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

# Copy custom nginx config
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisor config
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Run composer install (production mode)
RUN composer install --ignore-platform-req=php --no-dev --optimize-autoloader --verbose

# Set correct permissions for Laravel storage and cache
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script and give it execute permissions
COPY ./docker/script/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Expose HTTP port
EXPOSE 10000

# Start the app with the startup script
CMD ["/usr/local/bin/startup.sh"]
