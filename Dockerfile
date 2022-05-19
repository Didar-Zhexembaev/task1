FROM php:7.4-apache
RUN docker-php-ext-install mysqli pdo_mysql \
&& docker-php-ext-enable mysqli pdo_mysql \
&& a2enmod rewrite \
&& apt-get update && apt-get upgrade -y \
&& apt-get install git zip unzip -y
# Install Composer
RUN curl -sS https://getcomposer.org/installer | \
php -- --install-dir=/usr/local/bin --filename=composer