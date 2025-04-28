# Stage 1: Build aplikasi Laravel
FROM php:8.2-fpm-alpine as build

# Instal dependensi yang dibutuhkan untuk Laravel
RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html

# Salin seluruh kode aplikasi Laravel ke dalam container
COPY . .

# Instal Composer dan dependencies aplikasi
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Deploy aplikasi dengan Nginx
FROM nginx:alpine

# Salin konfigurasi Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/site.conf /etc/nginx/conf.d/default.conf

# Salin hasil build dari Stage 1 ke direktori web Nginx
COPY --from=build /var/www/html /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
