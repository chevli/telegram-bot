FROM composer:2.5

WORKDIR /app

COPY composer.json composer.lock /app/

RUN composer install --no-scripts --no-autoloader

COPY . /app

RUN composer dump-autoload --optimize

CMD ["php", "index.php"]