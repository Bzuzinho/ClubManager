#!/bin/bash

################################################################################
# ClubManager - Rollback Script
# Este script reverte o deploy para uma versão anterior
################################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

APP_PATH=${APP_PATH:-/var/www/clubmanager}
PHP_BIN=${PHP_BIN:-php}
GIT_REF=${1:-HEAD~1}

echo -e "${RED}================================${NC}"
echo -e "${RED}ClubManager - Rollback${NC}"
echo -e "${RED}================================${NC}"
echo ""

echo -e "${YELLOW}⚠️  WARNING: This will rollback the application!${NC}"
echo -e "${YELLOW}Target: ${GIT_REF}${NC}"
echo ""
read -p "Are you sure? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo -e "${YELLOW}Rollback cancelled${NC}"
    exit 0
fi

cd $APP_PATH

echo -e "${YELLOW}[1/7] Putting application in maintenance mode...${NC}"
cd backend
$PHP_BIN artisan down --message="Rolling back application..." --retry=60
echo -e "${GREEN}✓ Maintenance mode enabled${NC}"

echo -e "${YELLOW}[2/7] Rolling back code...${NC}"
cd ..
git fetch origin
git reset --hard $GIT_REF
echo -e "${GREEN}✓ Code rolled back${NC}"

echo -e "${YELLOW}[3/7] Reinstalling dependencies...${NC}"
cd backend
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
echo -e "${GREEN}✓ Backend dependencies installed${NC}"

echo -e "${YELLOW}[4/7] Running migrations rollback (if needed)...${NC}"
# Careful: this might need manual intervention
echo -e "${YELLOW}Skipping automatic migration rollback (manual verification recommended)${NC}"

echo -e "${YELLOW}[5/7] Clearing caches...${NC}"
$PHP_BIN artisan cache:clear
$PHP_BIN artisan config:clear
$PHP_BIN artisan route:clear
$PHP_BIN artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

echo -e "${YELLOW}[6/7] Rebuilding frontend...${NC}"
cd ../frontend
npm ci
npm run build
echo -e "${GREEN}✓ Frontend rebuilt${NC}"

echo -e "${YELLOW}[7/7] Bringing application back up...${NC}"
cd ../backend
$PHP_BIN artisan up
echo -e "${GREEN}✓ Application is live${NC}"

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}Rollback completed!${NC}"
echo -e "${GREEN}================================${NC}"
echo ""

exit 0
