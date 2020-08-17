#!/bin/bash
set -e
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '572cb359b56ad9ae52f9c23d29d4b19a040af10d6635642e646a7caa7b96de717ce683bd797a92ce99e5929cc51e7d5f') { echo 'Installer verified'; } else { echo 'Installer corrupt. Check the sha284 here: https://getcomposer.org/download/'; unlink('composer-setup.php'); } echo PHP_EOL;"
mkdir dep
php composer-setup.php --install-dir=dep --filename=composer
php -r "unlink('composer-setup.php');"
./dep/composer i