FROM openswoole/swoole

# COPY ./bin/rootfilesystem/ /

# docker-php-extension-installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync
ARG PHP_EXT_ARGS="pdo_pgsql, pdo_mysql"
RUN install-php-extensions ${PHP_EXT_ARGS}

COPY ./config/server/swoole.conf /etc/supervisor/service.d/swoole.conf

WORKDIR "/var/www"

COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer validate
ARG COMPOSER_ARGS="install"
RUN composer ${COMPOSER_ARGS} --no-progress --no-suggest --no-scripts --no-autoloader --no-dev --ansi
RUN composer dump-autoload --classmap-authoritative --ansi