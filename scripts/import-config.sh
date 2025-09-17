#!/bin/bash

echo "🔄 Importing Drupal configuration..."

# Перевірка наявності конфігураційних файлів
if [ ! -d "./config" ]; then
    echo "❌ Config directory not found!"
    exit 1
fi

# Копіювання конфігурації в контейнер
docker cp ./config drupal_main:/opt/drupal/web/

# Імпорт конфігурації
docker-compose exec drupal ./vendor/bin/drush config-import -y

# Очищення кешу
docker-compose exec drupal ./vendor/bin/drush cache-rebuild

echo "✅ Configuration imported successfully!"
echo "💡 Your Drupal site is now configured"