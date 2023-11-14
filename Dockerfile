
FROM  webdevops/php-nginx-dev:8.2-alpine

COPY ./ /app
RUN mkdir -m 777 -p /app/tmp

# RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/
# RUN docker-php-ext-install ldap

# Expose port 80 for the web server
WORKDIR /app

RUN composer install --no-interaction --optimize-autoloader --no-dev
# Set the entrypoint command to run the Laravel application
#CMD php artisan serve --host=0.0.0.0 --port=80

# Ensure all of our files are owned by the same user and group.
RUN chown -R application:application .
