	# Use the official PHP 8.2 image as the base image
FROM php:8.2
 
# Install required dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install PHP LDAP extension
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/
RUN docker-php-ext-install ldap

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
# Set the working directory
WORKDIR /var/www/html
 
# Copy the Laravel application files to the container
COPY . .

RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache



# Expose port 80 for the web server
EXPOSE 80
 
# Set the entrypoint command to run the Laravel application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
