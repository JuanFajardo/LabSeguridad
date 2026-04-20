FROM php:8.2-apache

# Instalar extensiones necesarias para MariaDB/MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instalar phpMyAdmin (descargar y mover a su ubicación)
RUN apt-get update && apt-get install -y wget unzip && \
    wget https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-all-languages.zip && \
    unzip phpMyAdmin-5.2.1-all-languages.zip -d /var/www/html/ && \
    mv /var/www/html/phpMyAdmin-5.2.1-all-languages /var/www/html/phpmyadmin && \
    ln -s /var/www/html/phpMyAdmin-5.2.1-all-languages /var/www/html/phpmyadmin && \
    apt-get remove -y wget unzip && apt-get autoremove -y

# Copiar tu código de la aplicación
COPY ./webBasico/ /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/