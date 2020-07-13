FROM composer AS builder

WORKDIR /workspace

COPY www/composer.json /workspace

RUN composer install

# This resets the container image for next steps
FROM php:7.4-apache

ENV NAME="[your name here]"

COPY www/ /var/www/html/ 

COPY --from=builder /workspace /var/www/html/ 

ENTRYPOINT [ "apache2-foreground" ]

