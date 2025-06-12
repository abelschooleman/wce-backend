FROM php:8.4-fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update \
    && apt-get install -y \
        build-essential \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libtidy-dev \
        libzip-dev \
        locales \
        curl \
        git \
        gifsicle \
        jpegoptim \
        optipng \
        pngquant

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install redis \
  && docker-php-ext-enable redis

# Install extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install exif intl pcntl tidy gd iconv zip
RUN docker-php-ext-enable tidy gd zip iconv intl exif

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www
RUN usermod -u 1000 www-data;

# Change current user to www-data
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
EXPOSE 9009
CMD ["php-fpm"]
