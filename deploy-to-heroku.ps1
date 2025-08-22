# Script tu dong deploy Bookstore_DATN len Heroku
# Chay script nay trong PowerShell voi quyen Administrator

Write-Host "=== DEPLOY BOOKSTORE_DATN LEN HEROKU ===" -ForegroundColor Green
Write-Host ""

# Kiem tra Heroku CLI
Write-Host "Kiem tra Heroku CLI..." -ForegroundColor Yellow
try {
    $herokuVersion = heroku --version
    Write-Host "✓ Heroku CLI da cai dat: $herokuVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Heroku CLI chua cai dat!" -ForegroundColor Red
    Write-Host "Vui long cai dat Heroku CLI tu: https://devcenter.heroku.com/articles/heroku-cli" -ForegroundColor Yellow
    Write-Host "Hoac chay: choco install heroku" -ForegroundColor Yellow
    exit 1
}

# Kiem tra Git
Write-Host "Kiem tra Git..." -ForegroundColor Yellow
try {
    $gitVersion = git --version
    Write-Host "✓ Git da cai dat: $gitVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Git chua cai dat!" -ForegroundColor Red
    Write-Host "Vui long cai dat Git tu: https://git-scm.com/" -ForegroundColor Yellow
    exit 1
}

# Kiem tra Composer
Write-Host "Kiem tra Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version
    Write-Host "✓ Composer da cai dat: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Composer chua cai dat!" -ForegroundColor Red
    Write-Host "Vui long cai dat Composer tu: https://getcomposer.org/" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# Hoi ten ung dung Heroku
$appName = Read-Host "Nhap ten ung dung Heroku (de trong de tu dong tao)"
if ([string]::IsNullOrWhiteSpace($appName)) {
    Write-Host "Tao ung dung Heroku moi..." -ForegroundColor Yellow
    $appName = "bookstore-datn-" + (Get-Random -Minimum 1000 -Maximum 9999)
    Write-Host "Ten ung dung: $appName" -ForegroundColor Cyan
}

# Hoi ten database
$dbName = Read-Host "Nhap ten database (mac dinh: websach)"
if ([string]::IsNullOrWhiteSpace($dbName)) {
    $dbName = "websach"
}

Write-Host ""

# Cai dat dependencies
Write-Host "Cai dat dependencies..." -ForegroundColor Yellow
try {
    composer install --no-dev --optimize-autoloader
    Write-Host "✓ Dependencies da cai dat" -ForegroundColor Green
} catch {
    Write-Host "✗ Loi cai dat dependencies!" -ForegroundColor Red
    exit 1
}

# Khoi tao Git repository
Write-Host "Khoi tao Git repository..." -ForegroundColor Yellow
if (-not (Test-Path ".git")) {
    git init
    Write-Host "✓ Git repository da khoi tao" -ForegroundColor Green
} else {
    Write-Host "✓ Git repository da ton tai" -ForegroundColor Green
}

# Them va commit files
Write-Host "Commit files..." -ForegroundColor Yellow
git add .
git commit -m "Deploy to Heroku - $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
Write-Host "✓ Files da commit" -ForegroundColor Green

# Tao ung dung Heroku
Write-Host "Tao ung dung Heroku..." -ForegroundColor Yellow
try {
    heroku create $appName
    Write-Host "✓ Ung dung Heroku da tao: $appName" -ForegroundColor Green
} catch {
    Write-Host "✗ Loi tao ung dung Heroku!" -ForegroundColor Red
    exit 1
}

# Them buildpack PHP
Write-Host "Them buildpack PHP..." -ForegroundColor Yellow
try {
    heroku buildpacks:set heroku/php
    Write-Host "✓ Buildpack PHP da them" -ForegroundColor Green
} catch {
    Write-Host "✗ Loi them buildpack!" -ForegroundColor Red
    exit 1
}

# Them PostgreSQL database
Write-Host "Them PostgreSQL database..." -ForegroundColor Yellow
try {
    heroku addons:create heroku-postgresql:mini
    Write-Host "✓ PostgreSQL database da them" -ForegroundColor Green
} catch {
    Write-Host "✗ Loi them database!" -ForegroundColor Red
    exit 1
}

# Cau hinh bien moi truong
Write-Host "Cau hinh bien moi truong..." -ForegroundColor Yellow
try {
    $dbUrl = heroku config:get DATABASE_URL
    Write-Host "✓ Database URL: $dbUrl" -ForegroundColor Green
    
    # Parse DATABASE_URL de lay thong tin
    $pattern = "postgres://([^:]+):([^@]+)@([^:]+):(\d+)/(.+)"
    if ($dbUrl -match $pattern) {
        $dbUser = $matches[1]
        $dbPass = $matches[2]
        $dbHost = $matches[3]
        $dbPort = $matches[4]
        $dbName = $matches[5]
        
        heroku config:set DB_HOST=$dbHost
        heroku config:set DB_USERNAME=$dbUser
        heroku config:set DB_PASSWORD=$dbPass
        heroku config:set DB_NAME=$dbName
        heroku config:set DB_PORT=$dbPort
        
        Write-Host "✓ Bien moi truong da cau hinh" -ForegroundColor Green
    }
} catch {
    Write-Host "✗ Loi cau hinh bien moi truong!" -ForegroundColor Red
    exit 1
}

# Deploy ung dung
Write-Host "Deploy ung dung..." -ForegroundColor Yellow
try {
    git push heroku main
    Write-Host "✓ Ung dung da deploy thanh cong!" -ForegroundColor Green
} catch {
    Write-Host "✗ Loi deploy!" -ForegroundColor Red
    exit 1
}

# Mo ung dung
Write-Host "Mo ung dung..." -ForegroundColor Yellow
try {
    heroku open
    Write-Host "✓ Ung dung da mo trong trinh duyet" -ForegroundColor Green
} catch {
    Write-Host "✗ Khong the mo ung dung tu dong" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== DEPLOY THANH CONG! ===" -ForegroundColor Green
Write-Host "URL ung dung: https://$appName.herokuapp.com" -ForegroundColor Cyan
Write-Host ""
Write-Host "Cac lenh huu ich:" -ForegroundColor Yellow
Write-Host "  heroku logs --tail          # Xem logs" -ForegroundColor White
Write-Host "  heroku ps                   # Kiem tra trang thai" -ForegroundColor White
Write-Host "  heroku config               # Xem cau hinh" -ForegroundColor White
Write-Host "  heroku restart              # Khoi dong lai" -ForegroundColor White
Write-Host ""
Write-Host "Nhan Enter de thoat..." -ForegroundColor Gray
Read-Host
