# Use an official Python runtime as a parent image
FROM php:7.2-fpm

# Set the working directory to /app
WORKDIR /var/www

# Copy the current directory contents into the container at /app
COPY . /var/www

RUN apt-get update \
 && apt-get install nano

# php.ini generate
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && echo 'error_reporting=E_ALL & ~E_USER_DEPRECATED' >> /usr/local/etc/php/php.ini

# install locale
RUN apt-get install --yes zlib1g-dev libicu-dev g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# install redis
RUN yes '' | pecl install redis \
    && echo 'extension=redis.so' >> /usr/local/etc/php/php.ini

# Make port 80 available to the world outside this container
EXPOSE 80

# Run index.php when the container launches
CMD ["php", "-S", "0.0.0.0:80", "-t", "./public", "./public/index.php"]
