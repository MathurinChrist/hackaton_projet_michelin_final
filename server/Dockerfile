FROM php:8.3-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libpq-dev \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) \
        intl \
        pdo \
        pdo_pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["sh", "-lc", "test -f vendor/autoload.php || composer install --no-interaction --no-progress; php -S 0.0.0.0:8000 -t public"]
