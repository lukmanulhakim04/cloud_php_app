FROM php:8.2-apache
COPY . /var/www/html/
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y git unzip && \
    curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install
EXPOSE 80
CMD ["apache2-foreground"]
