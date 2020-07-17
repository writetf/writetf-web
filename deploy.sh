#!/bin/bash

sudo service nginx stop
sudo service php7.3-fpm stop
git pull
sudo rm -rf var vendor
composer install --no-dev
php bin/console cache:clear
php bin/console doctrine:migrations:migrate
yarn
yarn encore production
sudo chown -R www-data:www-data var
sudo service php7.3-fpm start
sudo service nginx start
