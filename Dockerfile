FROM php:8.3-apache

COPY . /var/www/html

RUN ln -s /mnt/volume/db /var/www/html/usr/db \
  && ln -s /mnt/volume/uploads /var/www/html/usr/uploads

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
