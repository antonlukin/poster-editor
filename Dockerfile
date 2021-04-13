FROM php:7.4-fpm

RUN apt-get update && apt-get install -y zlib1g-dev libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev

RUN docker-php-ext-configure gd --with-webp --with-jpeg --with-freetype

RUN docker-php-ext-install gd
RUN docker-php-ext-enable gd