	# Use the official PHP 8.1 image as the base image
FROM php:8.2
 
# Install required dependencies
RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    libldap2-dev \
    default-mysql-client
 
# Install PHP LDAP extension
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/
RUN docker-php-ext-install ldap

 
# Set the working directory
WORKDIR /var/www/html
 
# Copy the Laravel application files to the container
COPY . .

# Expose port 80 for the web server
EXPOSE 80
 
# Set the entrypoint command to run the Laravel application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
