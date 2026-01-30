#!/bin/bash

# Configuration
PROJECT_DIR=/var/www/SisfoKK-Sentani
BACKUP_DIR=$PROJECT_DIR/backups
DATE=$(date +%Y-%m-%d_%H-%M-%S)
DB_NAME=sisfokk_sentani_db
DB_USER=sisfokk_user
DB_PASS=sisfokk_pass_2024

echo 📂 Starting Backup ($DATE)...

# Create backup dir if not exists
mkdir -p $BACKUP_DIR

# 1. Database Backup
echo 🗄️ Backing up Database...
docker exec sisfokk_mysql mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# 2. Storage Backup
echo 📁 Backing up Storage (Uploads)...
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz -C $PROJECT_DIR/backend/storage app/public

# 3. Cleanup old backups (Keep last 7 days)
echo 🧹 Cleaning up old backups...
find $BACKUP_DIR -type f -mtime +7 -delete

echo ✅ Backup Completed!
