FROM php:8.3-apache

COPY . /var/www/html

RUN a2enmod rewrite \
  && a2enmod headers \
  && chown root:root /var/www/html \
  && touch /var/www/html/usr/themes/ueno/style.css \
  && chown www-data:www-data /var/www/html/usr/themes/ueno/style.css

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
