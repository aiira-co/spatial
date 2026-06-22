FROM openswoole/openswoole:php8.5-alpine

# Install Git and Unzip using Alpine's package manager
RUN apk add --no-cache git unzip supervisor

# docker-php-extension-installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync
ARG PHP_EXT_ARGS="pdo_pgsql redis apcu intl pcntl bcmath zip opentelemetry sockets"
RUN install-php-extensions ${PHP_EXT_ARGS}

COPY ./config/server/swoole.conf /etc/supervisor/service.d/swoole.conf

WORKDIR "/var/www"

# Copy only composer files first to leverage Docker cache
COPY composer.json composer.lock ./

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies (update if lock file is outdated)
RUN composer update spatial/core --no-progress --prefer-dist --ansi || composer install --prefer-dist --no-progress --no-scripts --no-autoloader --no-dev --ansi

# Copy the rest of the application
COPY . .

# Dump autoloader
RUN composer dump-autoload --optimize --apcu --ansi


# Tell the container to start supervisor using your config
CMD ["supervisord", "-c", "/etc/supervisor/service.d/swoole.conf", "-n"]