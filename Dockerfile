FROM php:8.2-apache

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Change AllowOverride None to AllowOverride All in apache2.conf to make .htaccess work
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
