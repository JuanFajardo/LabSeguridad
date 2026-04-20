FROM php:8.2-apache

# Instalar MariaDB y utilidades
RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    wget \
    unzip \
    libmariadb-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar phpMyAdmin
ARG PMA_VERSION=5.2.1
RUN cd /var/www/html && \
    wget https://files.phpmyadmin.net/phpMyAdmin/${PMA_VERSION}/phpMyAdmin-${PMA_VERSION}-all-languages.zip && \
    unzip phpMyAdmin-${PMA_VERSION}-all-languages.zip && \
    rm phpMyAdmin-${PMA_VERSION}-all-languages.zip && \
    ln -s phpMyAdmin-${PMA_VERSION}-all-languages phpmyadmin && \
    chown -R www-data:www-data phpMyAdmin-${PMA_VERSION}-all-languages phpmyadmin

# Configurar phpMyAdmin
RUN cd /var/www/html/phpmyadmin && \
    cp config.sample.inc.php config.inc.php && \
    sed -i "s/\$cfg\['Servers'\]\[\$i\]\['host'\] = '127.0.0.1';/\$cfg['Servers'][\$i]['host'] = '127.0.0.1';/" config.inc.php

# Configurar MariaDB
RUN mkdir -p /var/run/mysqld && \
    chown -R mysql:mysql /var/run/mysqld && \
    chown -R mysql:mysql /var/lib/mysql

# Inicializar base de datos y crear usuario
RUN service mariadb start && \
    sleep 3 && \
    mysql -e "CREATE DATABASE IF NOT EXISTS ucb;" && \
    mysql -e "CREATE USER IF NOT EXISTS 'wendoline'@'%' IDENTIFIED BY 'wendoline';" && \
    mysql -e "GRANT ALL PRIVILEGES ON ucb.* TO 'wendoline'@'%';" && \
    mysql -e "FLUSH PRIVILEGES;"

# Copiar tu aplicación
COPY ./webBasico/ /var/www/html/

# Script de inicio para múltiples servicios
COPY start.sh /start.sh
RUN chmod +x /start.sh && rm -rf /var/www/html/.htaccess

EXPOSE 80 3306

CMD ["/start.sh"]
