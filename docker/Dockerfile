ARG VERSION=8.3

FROM php:${VERSION}-cli-alpine

WORKDIR /app

RUN set -eux; \
    apk add --update linux-headers; \
    apk --no-cache add icu-libs icu-data-full icu-dev; \
    apk --no-cache add pcre-dev ${PHPIZE_DEPS}; \
    PHP_MAJOR="${VERSION%%.*}"; \
    if [ "${PHP_MAJOR}" -lt 8 ]; then \
        XDEBUG_PACKAGE="xdebug-3.1.6"; \
    else \
        XDEBUG_PACKAGE="xdebug"; \
    fi; \
    pecl install "${XDEBUG_PACKAGE}"; \
    docker-php-ext-enable xdebug; \
    apk del pcre-dev ${PHPIZE_DEPS}

RUN docker-php-ext-install -j$(nproc) bcmath intl

RUN curl -sS https://getcomposer.org/installer | \
    php -- --2 --install-dir=/usr/local/bin --filename=composer