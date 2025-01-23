FROM php:8.1-fpm-alpine3.21

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2.8.4 /usr/bin/composer /usr/bin/composer

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini
COPY ./conf.d /usr/local/etc/php/conf.d

RUN install-php-extensions \
      pdo pdo mysqli pdo_mysql zip

WORKDIR /app
