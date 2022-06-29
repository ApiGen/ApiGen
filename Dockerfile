FROM alpine:3.16 as php-base

RUN addgroup --system --gid 1000 docker && \
	adduser --system --uid 1000 --ingroup docker docker && \
	mkdir /src && \
	chown docker:docker /src

RUN apk add --no-cache \
		php81 \
		php81-ctype \
		php81-json \
		php81-mbstring \
		php81-opcache \
		php81-pcntl \
		php81-tokenizer && \
	ln -s /usr/bin/php81 /usr/bin/php

COPY php.ini           /etc/php81/php.ini


FROM php-base as php-dev

COPY --from=blackfire/blackfire /usr/local/bin/blackfire /usr/local/bin/blackfire
COPY --from=composer:2          /usr/bin/composer        /usr/bin/composer

RUN apk add --no-cache \
		bash \
		php81-curl \
		php81-openssl \
		php81-phar \
		php81-session

RUN wget -O /usr/lib/php81/modules/blackfire.so https://packages.blackfire.io/binaries/blackfire-php/1.79.0/blackfire-php-alpine_amd64-php-81.so && \
	echo "extension = blackfire" >> /etc/php81/conf.d/blackfire.ini && \
	echo "opcache.jit_buffer_size = 0" >> /etc/php81/conf.d/blackfire.ini

RUN apk add --no-cache make git php81-dev gcc g++ && \
	ln -s /usr/bin/phpize81 /usr/bin/phpize && \
	ln -s /usr/bin/php-config81 /usr/bin/php-config

RUN wget -O /tmp/meminfo.tar.gz https://github.com/BitOne/php-meminfo/archive/master.tar.gz && \
	tar zxpf /tmp/meminfo.tar.gz -C /tmp && \
	rm /tmp/meminfo.tar.gz && \
	cd /tmp/php-meminfo-master/extension && phpize && ./configure --enable-meminfo && make && make install && \
	echo "extension = meminfo" >> /etc/php81/conf.d/meminfo.ini

RUN cd /tmp/php-meminfo-master/analyzer && \
	composer install && \
	ln -s /tmp/php-meminfo-master/analyzer/bin/analyzer /usr/bin/meminfo-analyzer


FROM php-dev as apigen-builder

WORKDIR /src

COPY composer.json           /src/composer.json
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
