FROM dunglas/frankenphp

RUN apt-get update && apt-get install -y \
    git \
    libpq-dev \
    postgresql-client \
    unzip

RUN docker-php-ext-install pdo_pgsql
RUN pecl install pcov \
    && docker-php-ext-enable pcov

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./Caddyfile /etc/frankenphp/Caddyfile
COPY ./entrypoint.sh /usr/local/bin/app-entrypoint

RUN chmod +x /usr/local/bin/app-entrypoint

RUN echo 'alias c="composer"' >> /root/.bashrc

WORKDIR /app
ENTRYPOINT ["app-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
