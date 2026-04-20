#!/bin/bash

# Iniciar MariaDB
service mariadb start

# Esperar a que MariaDB esté lista
sleep 5

# Importar la base de datos
mysql -u wendoline -pwendoline ucb < /var/www/html/database/schema.sql

sleep 2
sed -i "s/localhost/127.0.0.1/" /var/www/html/phpmyadmin/config.inc.php

# Iniciar Apache en primer plano
apache2-foreground
