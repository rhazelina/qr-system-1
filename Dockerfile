FROM dunglas/frankenphp:latest

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    pdo_sqlite

RUN groupadd -g 1000 sail \
    && useradd -m -u 1000 -g sail sail

COPY . /app

ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000"]
