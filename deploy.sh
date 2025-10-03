#!/bin/bash

#######################################
# Bookstore Deployment Script
# Deploy from Local to Production
#######################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SERVER_IP="178.128.127.19"
SERVER_USER="root"
SERVER_PATH="/var/www/html/Bookstore_DATN"
DB_NAME="websach"
DB_USER="bookstore_user"
DB_PASS="Tan123@@"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  🚀 Bookstore Deployment Script${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""
echo "Server: $SERVER_IP"
echo "Path: $SERVER_PATH"
echo ""

# Step 1: Commit and Push to GitHub
echo -e "${YELLOW}📤 Step 1: Pushing to GitHub...${NC}"
git add .
git commit -m "Deploy: $(date '+%Y-%m-%d %H:%M:%S')" || echo "No changes to commit"
git push origin local-code
echo -e "${GREEN}  ✅ Code pushed to GitHub${NC}"
echo ""

# Step 2: Backup on Server
echo -e "${YELLOW}📦 Step 2: Creating backup on server...${NC}"
ssh $SERVER_USER@$SERVER_IP << 'ENDSSH'
    # Create backup directory
    BACKUP_DIR="/root/backups/$(date +%Y%m%d_%H%M%S)"
    mkdir -p $BACKUP_DIR
    
    echo "  → Backing up database..."
    mysqldump -u bookstore_user -p'Tan123@@' websach > $BACKUP_DIR/websach_backup.sql
    
    echo "  → Backing up .env.production..."
    if [ -f /var/www/html/Bookstore_DATN/.env.production ]; then
        cp /var/www/html/Bookstore_DATN/.env.production $BACKUP_DIR/.env.production.bak
    fi
    
    echo "  ✅ Backup completed: $BACKUP_DIR"
ENDSSH
echo ""

# Step 3: Pull Latest Code
echo -e "${YELLOW}📥 Step 3: Pulling latest code on server...${NC}"
ssh $SERVER_USER@$SERVER_IP << 'ENDSSH'
    cd /var/www/html/Bookstore_DATN
    
    # Pull from GitHub
    git fetch origin
    git reset --hard origin/local-code
    
    echo "  ✅ Code updated"
ENDSSH
echo ""

# Step 4: Install Dependencies
echo -e "${YELLOW}📦 Step 4: Installing dependencies...${NC}"
ssh $SERVER_USER@$SERVER_IP << 'ENDSSH'
    cd /var/www/html/Bookstore_DATN
    
    # Install Composer dependencies
    composer install --no-dev --optimize-autoloader --quiet
    
    echo "  ✅ Dependencies installed"
ENDSSH
echo ""

# Step 5: Setup Environment
echo -e "${YELLOW}🔧 Step 5: Setting up environment...${NC}"
ssh $SERVER_USER@$SERVER_IP << 'ENDSSH'
    cd /var/www/html/Bookstore_DATN
    
    # Copy .env.production to .env
    if [ -f .env.production ]; then
        cp .env.production .env
        echo "  → Copied .env.production to .env"
    fi
    
    # Create logs directory
    mkdir -p logs
    
    # Set permissions
    chown -R www-data:www-data .
    chmod -R 755 .
    chmod -R 775 logs assets/img/products
    chmod 600 .env .env.production 2>/dev/null || true
    
    echo "  ✅ Environment configured"
ENDSSH
echo ""

# Step 6: Restart Services
echo -e "${YELLOW}🔄 Step 6: Restarting services...${NC}"
ssh $SERVER_USER@$SERVER_IP << 'ENDSSH'
    systemctl restart apache2
    echo "  ✅ Apache restarted"
ENDSSH
echo ""

# Step 7: Test Deployment
echo -e "${YELLOW}🧪 Step 7: Testing deployment...${NC}"
sleep 2  # Wait for Apache to fully restart

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://bookshop.works)
if [ $HTTP_CODE -eq 200 ]; then
    echo -e "${GREEN}  ✅ Website is UP (HTTP $HTTP_CODE)${NC}"
else
    echo -e "${RED}  ⚠️  Warning: HTTP $HTTP_CODE${NC}"
fi
echo ""

# Summary
echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""
echo "🌐 Website: https://bookshop.works"
echo "📊 Admin: https://bookshop.works/admin.php"
echo ""
echo "📝 Next steps:"
echo "  1. Test website functionality"
echo "  2. Check logs: ssh root@178.128.127.19 'tail -f /var/www/html/Bookstore_DATN/logs/php-errors.log'"
echo "  3. If issues, rollback: ssh root@178.128.127.19 'ls -la /root/backups/'"
echo ""
