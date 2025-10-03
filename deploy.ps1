#######################################
# Bookstore Deployment Script (Windows)
# Deploy from Local to Production
#######################################

$SERVER = "root@178.128.127.19"
$REMOTE_PATH = "/var/www/html/Bookstore_DATN"

Write-Host "========================================" -ForegroundColor Blue
Write-Host "  🚀 Bookstore Deployment Script" -ForegroundColor Blue
Write-Host "========================================" -ForegroundColor Blue
Write-Host ""

# Step 1: Push to GitHub
Write-Host "📤 Step 1: Pushing to GitHub..." -ForegroundColor Yellow
git add .
git commit -m "Deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
if ($LASTEXITCODE -eq 0) {
    git push origin local-code
    Write-Host "  ✅ Code pushed to GitHub" -ForegroundColor Green
} else {
    Write-Host "  ℹ️  No changes to commit" -ForegroundColor Cyan
}
Write-Host ""

# Step 2: Backup on Server
Write-Host "📦 Step 2: Creating backup on server..." -ForegroundColor Yellow
ssh $SERVER @"
    BACKUP_DIR="/root/backups/`$(date +%Y%m%d_%H%M%S)"
    mkdir -p `$BACKUP_DIR
    mysqldump -u bookstore_user -p'Tan123@@' websach > `$BACKUP_DIR/db_backup.sql
    if [ -f $REMOTE_PATH/.env.production ]; then
        cp $REMOTE_PATH/.env.production `$BACKUP_DIR/.env.production.bak
    fi
    echo "✅ Backup completed: `$BACKUP_DIR"
"@
Write-Host ""

# Step 3: Pull Latest Code
Write-Host "📥 Step 3: Pulling latest code..." -ForegroundColor Yellow
ssh $SERVER @"
    cd $REMOTE_PATH
    git fetch origin
    git reset --hard origin/local-code
    echo "✅ Code updated"
"@
Write-Host ""

# Step 4: Install Dependencies
Write-Host "📦 Step 4: Installing dependencies..." -ForegroundColor Yellow
ssh $SERVER @"
    cd $REMOTE_PATH
    composer install --no-dev --optimize-autoloader --quiet
    echo "✅ Dependencies installed"
"@
Write-Host ""

# Step 5: Setup Environment
Write-Host "🔧 Step 5: Setting up environment..." -ForegroundColor Yellow
ssh $SERVER @"
    cd $REMOTE_PATH
    if [ -f .env.production ]; then
        cp .env.production .env
    fi
    mkdir -p logs
    chown -R www-data:www-data .
    chmod -R 755 .
    chmod -R 775 logs assets/img/products
    chmod 600 .env .env.production 2>/dev/null || true
    echo "✅ Environment configured"
"@
Write-Host ""

# Step 6: Restart Services
Write-Host "🔄 Step 6: Restarting services..." -ForegroundColor Yellow
ssh $SERVER "systemctl restart apache2"
Write-Host "  ✅ Apache restarted" -ForegroundColor Green
Write-Host ""

# Step 7: Test
Write-Host "🧪 Step 7: Testing deployment..." -ForegroundColor Yellow
Start-Sleep -Seconds 2

try {
    $response = Invoke-WebRequest -Uri "https://bookshop.works" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Host "  ✅ Website is UP (HTTP $($response.StatusCode))" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️ Warning: HTTP $($response.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ❌ Error testing website: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Blue
Write-Host "✅ Deployment completed!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Blue
Write-Host ""
Write-Host "🌐 Website: https://bookshop.works" -ForegroundColor Cyan
Write-Host "📊 Admin: https://bookshop.works/admin.php" -ForegroundColor Cyan
Write-Host ""
Write-Host "📝 To check logs:" -ForegroundColor Yellow
Write-Host "  ssh root@178.128.127.19 'tail -f /var/www/html/Bookstore_DATN/logs/php-errors.log'" -ForegroundColor Gray
Write-Host ""
