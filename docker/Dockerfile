FROM dunglas/frankenphp

RUN apt-get update && apt-get install -y \
    git \
    libpq-dev \
    unzip

RUN docker-php-ext-install pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./Caddyfile /etc/frankenphp/Caddyfile

RUN echo 'alias c="composer"' >> /root/.bashrc
