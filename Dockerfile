FROM alpine:3.16 as php-prod

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


FROM php-prod as php-dev

COPY --from=blackfire/blackfire /usr/local/bin/blackfire /usr/local/bin/blackfire

RUN apk add --no-cache php81-session && \
	wget -O /usr/lib/php81/modules/blackfire.so https://packages.blackfire.io/binaries/blackfire-php/1.79.0/blackfire-php-alpine_amd64-php-81.so && \
	echo "extension = blackfire" >> /etc/php81/conf.d/blackfire.ini && \
	echo "opcache.jit_buffer_size = 0" >> /etc/php81/conf.d/blackfire.ini


FROM php-prod as apigen

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
