FROM php:7.4-fpm
WORKDIR /var/www
RUN apt-get update && apt-get install -y \
	libpq-dev \
	libzip-dev \
	zip \
	&& docker-php-ext-install zip \
	&& docker-php-ext-install pdo pdo_pgsql \
	&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

RUN echo "alias a='php artisan'" >> ~/.bashrc
