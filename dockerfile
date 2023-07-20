FROM php:8.2-apache

COPY . /var/www/html

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y --no-install-recommends libpq-dev \
  && docker-php-ext-install pdo_mysql

EXPOSE 80

CMD ["apache2-foreground"]