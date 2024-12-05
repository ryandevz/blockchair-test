FROM php:8.3-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN addgroup -g ${GID} --system blockchair
RUN adduser -G blockchair --system -D -s /bin/sh -u ${UID} blockchair

RUN sed -i "s/user = www-data/user = blockchair/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = blockchair/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

# For MySQL or MariaDB
# RUN docker-php-ext-install pdo pdo_mysql

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

USER blockchair

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
