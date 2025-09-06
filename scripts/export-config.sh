#!/bin/bash

echo "🔄 Exporting Drupal configuration..."

# Експорт конфігурації
docker-compose exec drupal ./vendor/bin/drush config-export -y

# Копіювання на хост
docker cp drupal_main:/opt/drupal/web/config ./

# Очищення кешу
docker-compose exec drupal ./vendor/bin/drush cache-rebuild

echo "✅ Configuration exported successfully!"
echo "💡 Run 'git status' to see changes"