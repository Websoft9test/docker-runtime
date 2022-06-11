#!/bin/bash

PHP_ROOT_PATH=/var/www/html
PHP_APP_NAME=laravel

PHP_CLI_SERVER_WORKERS=4

installComposer(){
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar
mv composer.phar /usr/local/bin/composer
apt update -y
apt install git unzip -y
docker-php-ext-install mysqli
}

installLaravel(){
composer create-project laravel/laravel example-app
cd example-app
php artisan serve --host 0.0.0.0
}

cd $NODE_ROOT_PATH
installComposer
installLaravel

tail -f /dev/null