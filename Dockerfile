FROM alpine:3.16

RUN addgroup --system --gid 1000 docker && \
	adduser --system --uid 1000 --ingroup docker docker && \
	mkdir /src && \
	chown docker:docker /src

RUN apk add --no-cache \
		php81 \
		php81-ctype \
		php81-curl \
		php81-json \
		php81-mbstring \
		php81-opcache \
		php81-openssl \
		php81-pcntl \
		php81-phar \
		php81-tokenizer && \
	ln -s /usr/bin/php81 /usr/bin/php

COPY php.ini           /etc/php81/php.ini
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /src
USER docker

COPY --chown=docker:docker composer.json           /src/composer.json
RUN composer install --no-dev --no-progress --no-cache --ignore-platform-req ext-session

COPY --chown=docker:docker bin                     /src/bin
COPY --chown=docker:docker src                     /src/src
COPY --chown=docker:docker stubs                   /src/stubs
COPY --chown=docker:docker apigen.neon             /src/apigen.neon
RUN composer dump-autoload --classmap-authoritative

ENTRYPOINT ["/src/bin/apigen"]
