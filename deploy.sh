#!/bin/bash

################################################################################
# ClubManager - Deploy Script
# Este script automatiza o deploy da aplicação em produção/staging
################################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
APP_PATH=${APP_PATH:-/var/www/clubmanager}
PHP_BIN=${PHP_BIN:-php}
COMPOSER_BIN=${COMPOSER_BIN:-composer}
NPM_BIN=${NPM_BIN:-npm}

echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}ClubManager - Deploy to ${ENVIRONMENT}${NC}"
echo -e "${GREEN}================================${NC}"
echo ""

# Validate environment
if [ "$ENVIRONMENT" != "production" ] && [ "$ENVIRONMENT" != "staging" ]; then
    echo -e "${RED}Error: Environment must be 'production' or 'staging'${NC}"
    exit 1
fi

# Check if running as correct user
if [ "$(whoami)" == "root" ]; then
    echo -e "${YELLOW}Warning: Running as root. Consider using deploy user.${NC}"
fi

echo -e "${YELLOW}[1/10] Checking prerequisites...${NC}"
# Check if required binaries exist
command -v $PHP_BIN >/dev/null 2>&1 || { echo -e "${RED}PHP not found${NC}"; exit 1; }
command -v $COMPOSER_BIN >/dev/null 2>&1 || { echo -e "${RED}Composer not found${NC}"; exit 1; }
command -v $NPM_BIN >/dev/null 2>&1 || { echo -e "${RED}NPM not found${NC}"; exit 1; }
command -v git >/dev/null 2>&1 || { echo -e "${RED}Git not found${NC}"; exit 1; }

echo -e "${GREEN}✓ Prerequisites OK${NC}"

cd $APP_PATH

echo -e "${YELLOW}[2/10] Pulling latest code...${NC}"
git fetch origin
git pull origin main
echo -e "${GREEN}✓ Code updated${NC}"

echo -e "${YELLOW}[3/10] Installing backend dependencies...${NC}"
cd backend
$COMPOSER_BIN install --no-interaction --prefer-dist --optimize-autoloader --no-dev
echo -e "${GREEN}✓ Backend dependencies installed${NC}"

echo -e "${YELLOW}[4/10] Putting application in maintenance mode...${NC}"
$PHP_BIN artisan down --message="Updating application..." --retry=60 --refresh=5
echo -e "${GREEN}✓ Application in maintenance mode${NC}"

echo -e "${YELLOW}[5/10] Running database migrations...${NC}"
$PHP_BIN artisan migrate --force
echo -e "${GREEN}✓ Migrations completed${NC}"

echo -e "${YELLOW}[6/10] Clearing and caching configuration...${NC}"
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache
echo -e "${GREEN}✓ Caches updated${NC}"

echo -e "${YELLOW}[7/10] Restarting queue workers...${NC}"
$PHP_BIN artisan queue:restart
echo -e "${GREEN}✓ Queue workers restarted${NC}"

echo -e "${YELLOW}[8/10] Building frontend...${NC}"
cd ../frontend
$NPM_BIN ci
$NPM_BIN run build
echo -e "${GREEN}✓ Frontend built${NC}"

echo -e "${YELLOW}[9/10] Bringing application back up...${NC}"
cd ../backend
$PHP_BIN artisan up
echo -e "${GREEN}✓ Application is live${NC}"

echo -e "${YELLOW}[10/10] Reloading web server...${NC}"
if command -v systemctl >/dev/null 2>&1; then
    sudo systemctl reload nginx || sudo systemctl reload apache2 || echo -e "${YELLOW}Warning: Could not reload web server${NC}"
fi
echo -e "${GREEN}✓ Web server reloaded${NC}"

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}Deploy completed successfully!${NC}"
echo -e "${GREEN}================================${NC}"
echo ""

# Health check
echo -e "${YELLOW}Running health check...${NC}"
sleep 5
curl --fail http://localhost/health || echo -e "${YELLOW}Warning: Health check failed${NC}"

exit 0
