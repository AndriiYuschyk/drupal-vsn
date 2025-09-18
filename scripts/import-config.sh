#!/bin/bash

echo "🔄 Importing Drupal configuration..."

if [ ! -d "./config" ]; then
    echo "❌ Config directory not found!"
    exit 1
fi

echo "⏳ Waiting for database to be ready..."
docker-compose exec drupal bash -c "
    until mysql -h database -u drupaluser -pdrupalpassword -e 'SELECT 1' drupal &> /dev/null; do
        echo 'Waiting for database connection...'
        sleep 2
    done
"

docker cp ./config drupal_main:/opt/drupal/web/

echo "🔍 Checking Drupal status..."
docker-compose exec drupal ./vendor/bin/drush status

echo "📥 Importing configuration..."
docker-compose exec drupal ./vendor/bin/drush config-import -y

echo "🧹 Clearing cache..."
docker-compose exec drupal ./vendor/bin/drush cache-rebuild

echo "✅ Configuration imported successfully!"
echo "💡 Your Drupal site is now configured"