# Deploy to Heroku Script
# Chạy script này sau khi đã cài đặt Heroku CLI

Write-Host "=== DEPLOY TO HEROKU ===" -ForegroundColor Green
Write-Host ""

# Kiểm tra Heroku CLI
try {
    $herokuVersion = heroku --version
    Write-Host "✓ Heroku CLI found: $herokuVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Heroku CLI not found. Please install it first." -ForegroundColor Red
    Write-Host "Download from: https://devcenter.heroku.com/articles/heroku-cli" -ForegroundColor Yellow
    exit 1
}

# Kiểm tra git status
Write-Host "Checking git status..." -ForegroundColor Yellow
$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "⚠ Uncommitted changes detected. Please commit first:" -ForegroundColor Yellow
    Write-Host $gitStatus
    exit 1
}

Write-Host "✓ Git repository is clean" -ForegroundColor Green

# Hỏi tên app
$appName = Read-Host "Enter your Heroku app name (or press Enter to generate one)"
if (-not $appName) {
    $appName = "bookstore-datn-" + (Get-Random -Minimum 1000 -Maximum 9999)
    Write-Host "Generated app name: $appName" -ForegroundColor Cyan
}

# Tạo app trên Heroku
Write-Host "Creating Heroku app: $appName" -ForegroundColor Yellow
try {
    heroku create $appName
    Write-Host "✓ Heroku app created successfully" -ForegroundColor Green
} catch {
    Write-Host "✗ Failed to create Heroku app" -ForegroundColor Red
    exit 1
}

# Thêm buildpack PHP
Write-Host "Adding PHP buildpack..." -ForegroundColor Yellow
try {
    heroku buildpacks:set heroku/php
    Write-Host "✓ PHP buildpack added" -ForegroundColor Green
} catch {
    Write-Host "✗ Failed to add PHP buildpack" -ForegroundColor Red
}

# Cài đặt PostgreSQL addon
Write-Host "Adding PostgreSQL addon..." -ForegroundColor Yellow
try {
    heroku addons:create heroku-postgresql:mini
    Write-Host "✓ PostgreSQL addon added" -ForegroundColor Green
} catch {
    Write-Host "⚠ PostgreSQL addon already exists or failed to add" -ForegroundColor Yellow
}

# Deploy code
Write-Host "Deploying to Heroku..." -ForegroundColor Yellow
try {
    git push heroku main
    Write-Host "✓ Deployment successful!" -ForegroundColor Green
} catch {
    Write-Host "✗ Deployment failed" -ForegroundColor Red
    exit 1
}

# Mở app
Write-Host "Opening your app..." -ForegroundColor Yellow
try {
    heroku open
    Write-Host "✓ App opened in browser" -ForegroundColor Green
} catch {
    Write-Host "⚠ Failed to open app automatically" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== DEPLOYMENT COMPLETE ===" -ForegroundColor Green
Write-Host "Your app URL: https://$appName.herokuapp.com" -ForegroundColor Cyan
Write-Host ""
Write-Host "Useful commands:" -ForegroundColor Yellow
Write-Host "  heroku logs --tail          # View logs"
Write-Host "  heroku run bash             # Run bash on Heroku"
Write-Host "  heroku config               # View environment variables"
Write-Host "  heroku config:set KEY=value # Set environment variables"
