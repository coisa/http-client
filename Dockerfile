ARG PHP_VERSION="7.4"
FROM php:${PHP_VERSION}-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk add --update $PHPIZE_DEPS \
    && pecl install pcov  \
    && docker-php-ext-enable pcov

WORKDIR /usr/local/src

COPY composer.json .
RUN composer install --no-interaction --no-progress

COPY . .

ENTRYPOINT ["composer"]
CMD ["version"]
