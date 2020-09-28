FROM php
RUN pecl install swoole
RUN echo "extension=swoole.so" >> /usr/local/etc/php/conf.d/swoole-ext.ini
WORKDIR /app
COPY . .
RUN curl -s https://getcomposer.org/installer | php
RUN php /app/composer.phar install