FROM phpswoole/swoole

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

COPY . .

RUN composer install
