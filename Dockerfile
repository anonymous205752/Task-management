# Use official PHP 8.4 image with FPM
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    nginx \
    supervisor \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl libonig-dev libxml2-dev libzip-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy Laravel application files into the container
COPY . .

# Remove default nginx configuration
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

# Copy custom nginx config
COPY ./docker-setup/docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisor config
COPY ./docker-setup/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install Laravel dependencies using Composer
RUN composer install --ignore-platform-req=php --no-dev --optimize-autoloader --verbose

# Set correct permissions for Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy startup script and make it executable
COPY ./docker-setup/docker/script/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Expose port (adjust if needed for NGINX)
EXPOSE 10000

# Start the app via custom startup script
CMD ["/usr/local/bin/startup.sh"]
