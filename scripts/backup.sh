#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)

echo "📦 Creating backup: $DATE"

# Бекап БД
docker-compose exec drupal ./vendor/bin/drush sql-dump --result-file=/tmp/backup-$DATE.sql
docker cp drupal_main:/tmp/backup-$DATE.sql ./backups/

# Бекап файлів
tar -czf ./backups/files-backup-$DATE.tar.gz ./files/

echo "✅ Backup completed: backup-$DATE"