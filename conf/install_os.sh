#!/bin/bash

sudo su

apt-get install vim mysql-server apache2 libapache2-mod-php php-mysql php-xml php-xdebug -y
mysql fakenews < struct.sql

cat vhost.conf > /etc/apache2/sites-enabled/tp.conf

cat xdebug.ini > /etc/php/7.0/mods-available/xdebug.ini

a2ensite tp.conf
a2enmod rewrite
systemctl restart apache2
