# BookStore Repository Structure

This repository has been reorganized to improve maintainability and code organization.

## Directory Structure

```
BookStore/
├── src/                    # Main application source code
│   ├── controllers/        # API endpoints and request handlers
│   ├── models/            # Data models and schemas (to be developed)
│   ├── services/          # Business logic and service layer
│   ├── routes/            # Route definitions (to be developed)
│   └── utils/             # Utility functions and helpers
├── config/                # Configuration files
├── assets/                # Static assets (CSS, images, fonts)
├── js/                    # JavaScript files
├── tests/                 # Test files and scripts
├── scripts/               # Build and migration scripts
├── vendor/                # Composer dependencies
├── vnpay_php/            # VNPay payment integration
├── admin/                # Admin-related files
├── index.php             # Main application entry point
├── admin.php             # Admin panel entry point
└── composer.json         # PHP dependencies
```

## File Organization

### Controllers (`src/controllers/`)
Contains all API endpoints and request handlers:
- `add_*.php` - Create operations
- `get_*.php` - Read operations  
- `update_*.php` - Update operations
- `delete_*.php` - Delete operations
- Authentication and user management files

### Services (`src/services/`)
Contains business logic and service layer:
- `send_mail.php` - Email service
- `order_mail_helper.php` - Order email handling

### Utils (`src/utils/`)
Contains utility functions:
- `main.php` - Common utility functions
- `config.php` - Configuration wrapper

### Configuration (`config/`)
- `config.php` - Database and application configuration

### Scripts (`scripts/`)
- `database_schema.sql` - Database schema and structure

## Running the Application

1. Ensure PHP is installed
2. Set up MySQL database using `scripts/database_schema.sql`
3. Update database configuration in `config/config.php`
4. Start web server: `php -S localhost:8000`
5. Access application at `http://localhost:8000`

## Testing

Run structure tests:
```bash
php tests/test_structure.php
./tests/test_js_paths.sh
```