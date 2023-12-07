FROM php:8.2-apache
VOLUME /app
COPY app/ /var/www/html/