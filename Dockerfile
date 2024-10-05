FROM php:8.3-alpine as php-base

RUN addgroup --system --gid 1000 docker && \
	adduser --system --uid 1000 --ingroup docker docker && \
	mkdir /src && \
	chown docker:docker /src

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
	docker-php-ext-install opcache && \
	docker-php-ext-install pcntl && \
	pecl install igbinary && \
	docker-php-ext-enable igbinary && \
	apk del .build-deps

COPY php.ini           /usr/local/etc/php/php.ini


FROM php-base as apigen-builder

WORKDIR /src
ARG COMPOSER_ROOT_VERSION

COPY --from=composer:2       /usr/bin/composer        /usr/bin/composer
COPY composer.json           /src/composer.json
COPY composer.lock           /src/composer.lock
RUN composer install --no-dev --no-progress --no-cache

COPY bin                     /src/bin
COPY src                     /src/src
COPY apigen.neon             /src/apigen.neon
COPY LICENSE                 /src/LICENSE
RUN composer dump-autoload --classmap-authoritative


FROM php-base as apigen

USER docker
COPY --from=apigen-builder --chown=docker:docker /src /src
ENTRYPOINT ["/src/bin/apigen"]
