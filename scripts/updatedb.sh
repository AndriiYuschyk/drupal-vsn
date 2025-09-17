#!/bin/bash

# Оновлення бази даних
docker-compose exec drupal ./vendor/bin/drush updatedb -y

echo "✅ DB updated successfully!"