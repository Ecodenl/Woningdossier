FROM php:7.4-fpm-alpine

ARG PHPGROUP
ARG PHPUSER

ENV PHPGROUP=${PHPGROUP}
ENV PHPUSER=${PHPUSER}

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN adduser -g ${PHPGROUP} -s /bin/sh -D ${PHPUSER}; exit 0

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

RUN sed -i "s/user = www-data/user = ${PHPUSER}/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = ${PHPGROUP}/g" /usr/local/etc/php-fpm.d/www.conf

# Setup PDO
RUN docker-php-ext-install pdo pdo_mysql

# Setup GD
RUN apk upgrade --update && apk add \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Setup ZIP
RUN apk upgrade --update && apk add \
        zlib-dev \
        libzip-dev \
    && docker-php-ext-install zip

# Setup EXIF
RUN docker-php-ext-install exif

RUN apk update && apk add git \
    && mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]

ADD ./docker/php.ini /usr/local/etc/php/conf.d/php.ini