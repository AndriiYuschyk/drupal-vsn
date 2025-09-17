FROM drupal:11-php8.3-apache

RUN apt-get update

RUN cd /opt/drupal/web/ \
    && composer require --no-interaction drush/drush \
    && composer require drupal/better_exposed_filters \
    && composer composer require drupal/colorbox \
    && composer require drupal/views_infinite_scroll \
    && composer require drupal/token drupal/pathauto \
    && composer clear-cache