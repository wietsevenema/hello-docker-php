FROM composer AS builder

WORKDIR /workspace

# Best practices dictate to add composer.lock as well
COPY composer.json /workspace

RUN composer install

# This resets the container image for next steps
FROM php:7.4-apache

RUN pecl install redis-5.3.1 \
    && docker-php-ext-enable redis 
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY html/ /var/www/html/ 

COPY --from=builder /workspace/vendor /var/www/vendor

ENTRYPOINT [ "apache2-foreground" ]

