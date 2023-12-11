FROM php:8.3-apache

COPY . /var/www/html

RUN a2enmod rewrite \
  && a2enmod headers

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
