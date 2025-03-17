FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    nano \
    libpq-dev \
    libzip-dev \
    postgresql-client \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf && \
    a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

RUN docker-php-ext-install opcache

COPY storage/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 80

COPY . .

COPY storage/apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2ensite 000-default && \
    service apache2 restart

CMD ["apache2-foreground"]