
FROM  webdevops/php-nginx-dev:8.2

COPY ./ /app
RUN mkdir -m 777 -p /app/tmp

# RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/
# RUN docker-php-ext-install ldap

RUN composer2 install -d /app


# Expose port 80 for the web server
EXPOSE 80
WORKDIR /app
# Set the entrypoint command to run the Laravel application
CMD php artisan serve --host=0.0.0.0 --port=80
