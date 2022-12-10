FROM alpine:edge as php-base

RUN addgroup --system --gid 1000 docker && \
	adduser --system --uid 1000 --ingroup docker docker && \
	mkdir /src && \
	chown docker:docker /src

RUN apk add --no-cache --repository https://dl-cdn.alpinelinux.org/alpine/edge/testing/ --allow-untrusted \
		php82 \
		php82-ctype \
		php82-json \
		php82-mbstring \
		php82-opcache \
		php82-pcntl \
		php82-tokenizer && \
	ln -s /usr/bin/php82 /usr/bin/php

COPY php.ini           /etc/php82/php.ini


FROM php-base as php-dev
ARG TARGETARCH

COPY --from=composer:2          /usr/bin/composer        /usr/bin/composer

RUN apk add --no-cache --repository https://dl-cdn.alpinelinux.org/alpine/edge/testing/ --allow-untrusted \
		php82-curl \
		php82-openssl \
		php82-phar


FROM php-dev as apigen-builder

WORKDIR /src
ARG COMPOSER_ROOT_VERSION

COPY composer.json           /src/composer.json
COPY composer.lock           /src/composer.lock
RUN composer install --no-dev --no-progress --no-cache

COPY bin                     /src/bin
COPY src                     /src/src
COPY stubs                   /src/stubs
COPY apigen.neon             /src/apigen.neon
COPY LICENSE                 /src/LICENSE
RUN composer dump-autoload --classmap-authoritative


FROM php-base as apigen

USER docker
COPY --from=apigen-builder --chown=docker:docker /src /src
ENTRYPOINT ["/src/bin/apigen"]
