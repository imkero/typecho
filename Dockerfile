FROM php:8.3-apache

COPY . /var/www/html

ENV TZ=Asia/Shanghai

RUN a2enmod rewrite \
  && a2enmod headers \
  && chown root:root /var/www/html \
  && touch /var/www/html/usr/themes/ueno/style.css \
  && chown www-data:www-data /var/www/html/usr/themes/ueno/style.css \
  && echo "[Date]\ndate.timezone=\"$TZ\"" > /usr/local/etc/php/conf.d/tzone.ini \
  && docker-php-ext-enable opcache && \
  { \
      echo 'opcache.memory_consumption=128'; \
      echo 'opcache.interned_strings_buffer=8'; \
      echo 'opcache.max_accelerated_files=4000'; \
      echo 'opcache.revalidate_freq=60'; \
      echo 'opcache.enable_cli=1'; \
  } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
