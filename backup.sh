#!/bin/bash

################################################################################
# ClubManager - Backup Script
# Este script cria backup completo do banco de dados e uploads
################################################################################

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Configuration
BACKUP_DIR=${BACKUP_DIR:-/var/backups/clubmanager}
APP_PATH=${APP_PATH:-/var/www/clubmanager}
PHP_BIN=${PHP_BIN:-php}
RETENTION_DAYS=${RETENTION_DAYS:-30}

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="clubmanager_${TIMESTAMP}"

echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}ClubManager - Backup${NC}"
echo -e "${GREEN}================================${NC}"
echo ""

# Create backup directory
mkdir -p $BACKUP_DIR

echo -e "${YELLOW}[1/4] Backing up database...${NC}"
cd $APP_PATH/backend

# Use Laravel backup package or direct mysqldump
$PHP_BIN artisan backup:run --only-db --filename="${BACKUP_NAME}.zip" || {
    # Fallback to manual backup
    source .env
    mysqldump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} | gzip > ${BACKUP_DIR}/${BACKUP_NAME}_db.sql.gz
}

echo -e "${GREEN}✓ Database backed up${NC}"

echo -e "${YELLOW}[2/4] Backing up uploads/documents...${NC}"
tar -czf ${BACKUP_DIR}/${BACKUP_NAME}_files.tar.gz -C $APP_PATH/backend/storage/app documents/ || echo -e "${YELLOW}No files to backup${NC}"
echo -e "${GREEN}✓ Files backed up${NC}"

echo -e "${YELLOW}[3/4] Cleaning old backups (>${RETENTION_DAYS} days)...${NC}"
find $BACKUP_DIR -name "clubmanager_*" -type f -mtime +${RETENTION_DAYS} -delete
echo -e "${GREEN}✓ Old backups cleaned${NC}"

echo -e "${YELLOW}[4/4] Listing recent backups...${NC}"
ls -lh $BACKUP_DIR | tail -n 5

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}Backup completed successfully!${NC}"
echo -e "${GREEN}Backup location: ${BACKUP_DIR}${NC}"
echo -e "${GREEN}================================${NC}"

exit 0
