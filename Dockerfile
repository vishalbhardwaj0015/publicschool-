FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html
COPY . .

RUN mkdir -p img/uploads img/admin \
    && chown -R www-data:www-data img user admin config

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]