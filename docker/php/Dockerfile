FROM php:8.2-fpm

# Update package lists and install necessary dependencies
RUN apt-get update \
    && apt-get install -y libpq-dev nano \
    && apt-get install -y \
        zlib1g-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && pecl install apcu \
    && docker-php-ext-enable apcu

# Install Xdebug and APCu extensions
RUN pecl install xdebug-3.3.1 && docker-php-ext-enable xdebug

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
#    && composer self-update
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /app

# Copy composer files to the container
COPY composer.json composer.lock ./

# Copy the PHP configuration files to the container
#COPY docker/php/conf.d/* $PHP_INI_DIR/conf.d/
ADD ./docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Copy the rest of the application files to the container
COPY . .

# Allow running Composer as root
ENV COMPOSER_ALLOW_SUPERUSER=1


# Install Symfony and its dependencies using Composer
RUN composer install --ignore-platform-reqs --no-interaction

#RUN php bin/console cache:clear

# Set the default command to run PHP-FPM
CMD ["php-fpm"]