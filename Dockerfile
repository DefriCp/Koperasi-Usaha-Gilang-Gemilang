FROM richarvey/nginx-php-fpm:php8.2

WORKDIR /var/www/html
COPY . .
M
COPY nginx.conf /etc/nginx/sites-enabled/default
COPY php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

RUN chown -R www-data:www-data storage bootstrap/cache

RUN chmod +x /var/www/html/deploy.sh

EXPOSE 80
CMD ["/bin/bash", "-lc", "/var/www/html/deploy.sh && /start.sh"]
