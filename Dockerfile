FROM php:7.4-zts-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install parallel \
    && docker-php-ext-enable parallel \
    && apk del $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*
