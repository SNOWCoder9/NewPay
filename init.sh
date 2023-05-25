#!/bin/bash

rm -rf composer.phar
wget https://github.com/composer/composer/releases/latest/download/composer.phar -O composer.phar
php composer.phar && php composer.phar install
php artisan newpay:install
php artisan passport:install
php artisan horizon:install
