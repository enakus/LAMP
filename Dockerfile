FROM php:7.4-apache
RUN docker-php-ext-install mysqli
RUN apt-get update \
     && apt-get install -y libzip-dev \
     && docker-php-ext-install zip
RUN chown -R 777  /var/www/html/
