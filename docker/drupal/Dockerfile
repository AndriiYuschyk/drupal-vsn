FROM drupal:11-php8.3-apache

RUN apt-get update

RUN composer require --no-interaction drush/drush

RUN composer clear-cache