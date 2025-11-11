# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Behaviour Rules
- You have one mission: execute *exactly* what is requested.
- Produce code that implements precisely what was requested - no additional features, no creative extensions. Follow instructions to the letter.
- Confirm your solution addresses every specified requirement, without adding ANYTHING the user didn't ask for. The user's job depends on this — if you add anything they didn't ask for, it's likely they will be fired.
- Your value comes from precision and reliability.
- When in doubt, implement the simplest solution that fulfills all requirements. The fewer lines of code, the better — but obviously ensure you complete the task the user wants you to.
- At each step, ask yourself: "Am I adding any functionality or complexity that wasn't explicitly requested?". This will force you to stay on track.


## Project Overview

This is a QR-based menu management system (SaaS application) built with Laravel 12. It allows businesses (vendors) to create and manage digital menus accessible via QR codes and unique subdomains. The system supports role-based access for Admins and Vendors, with subdomain-based public menu pages.

**Tech Stack:**
- Laravel 12 (PHP 8.2+)
- JWT Authentication (tymon/jwt-auth)
- Cloudinary for media storage
- SimpleSoftwareIO/simple-qrcode for QR generation
- Pest for testing
- Repository pattern for business logic

## Development Commands

### Setup and Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database (if applicable)
php artisan db:seed
```

### Development Server
```bash
# Start Laravel development server
php artisan serve

# Watch frontend assets
npm run watch
```

### Testing
```bash
# Run all tests with Pest
php artisan test

# Run specific test file
php artisan test --filter=VendorTest

# Run tests with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check code style without fixing
./vendor/bin/pint --test
```

### Database
```bash
# Fresh migration (drops all tables)
php artisan migrate:fresh

# Refresh with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback
```

### Artisan Commands
```bash
# Clear various caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate JWT secret
php artisan jwt:secret
```

## Architecture

### Authentication & Authorization

**JWT-based authentication** is used throughout the API:
- Tokens are issued at `/api/auth/login` and `/api/auth/register`
- All authenticated routes require `Authorization: Bearer <token>` header
- Token refresh available at `/api/auth/refresh`
- Role-based middleware: `role:admin` and `role:vendor`

**Middleware:**
- `authCheck`: Validates JWT token
- `role:{role}`: Checks user role (admin/vendor)
- `subdomain`: Handles subdomain routing (app/Http/Middleware/SubdomainMiddleware.php:8)
- `VendorAccess`: Vendor-specific access control

### Repository Pattern

Business logic is abstracted into repositories (app/Repositories/):
- **VendorRepository** (app/Repositories/VendorRepository.php:16): Handles all vendor-related operations including profile, business info, media, and menu data
- Repositories are injected via constructor dependency injection
- Interface-based design (app/Interfaces/VendorInterface.php)

### Subdomain Architecture

The application supports dynamic subdomains for each vendor:

1. **Routing** (app/Providers/RouteServiceProvider.php:33):
   - Pattern: `{subdomain}.{APP_DOMAIN}` (e.g., `restaurant.qr-app.test`)
   - Subdomain routes defined in `routes/subdomain.php`
   - Handled by SubdomainController

2. **Subdomain Creation** (app/Repositories/VendorRepository.php:95):
   - Generated from business name using `Str::slug()`
   - Ensures uniqueness by appending counter if needed
   - Stored in both `users.subdomain` and `business_links.subdomain` fields
   - Also creates corresponding Vendor model entry

3. **Public Menu Access**:
   - Subdomain requests resolve to vendor's public menu JSON
   - Middleware validates subdomain and attaches vendor to request
   - Legacy routes: `/api/menu/{vendor_link}` and `/api/qr/{vendor_link}`

### Models & Relationships

**User Model** (app/Models/User.php):
- Has one `user_data` (UserData)
- Has one `vendor_media` (VendorMedia)
- Has many `business_links` (BusinessLink)

**Vendor Model** (app/Models/Vendor.php):
- Represents vendor subdomain configuration
- Stores subdomain and business_link

**BusinessLink Model** (app/Models/BusinessLink.php):
- Links vendors to their business configuration
- Has many `items` (menu items)
- Contains QR code URL and subdomain

**Item Model** (app/Models/Item.php):
- Belongs to `business_link` (not `business_link_id` directly)
- Belongs to `category`
- Fields: title, description, price, status

**Category Model** (app/Models/Category.php):
- Has many `items`
- Has many `sub_categories`

### Services

**CloudinaryStorage** (app/Services/CloudinaryStorage.php):
- Handles image/video uploads to Cloudinary
- Used for vendor logos, hero images, and item media
- Provides `upload()` and `uploadQr()` methods

**QrCodeService** (app/Services/QrCodeService.php:9):
- Generates QR codes linking to vendor menus
- Creates PNG QR codes and uploads to Cloudinary
- URL format: `{APP_URL}/reach/{business_link}`

### Helper Functions

Global helper functions are auto-loaded via composer.json (app/Helpers/):
- `authUser()` (app/Helpers/functions.php:9): Returns authenticated user or error response
- `success()`, `error()`: Standardized API response helpers (app/Helpers/response.php)

### API Routes Structure

**Public Routes:**
- `/api/menu/{vendor_link}`: Public vendor menu (legacy)
- `/api/qr/{vendor_link}`: QR redirect (legacy)
- `{subdomain}.{domain}`: Subdomain-based public menu

**Auth Routes** (`/api/auth/*`):
- register, login, logout, refresh
- countries, states, cities (for location dropdowns)

**Vendor Routes** (`/api/vendor/*`):
- Requires: `authCheck` + `role:vendor` middleware
- dashboard, profile, business-info, business-links
- Categories, subcategories, items (CRUD)
- Media uploads, QR generation

**Admin Routes** (`/api/admin/*`):
- Requires: `authCheck` + `role:admin` middleware
- Vendor management (CRUD)
- Business & link management
- Category & item oversight
- QR generation for vendors

## Environment Configuration

Key environment variables (see .env.example):
- `APP_DOMAIN`: Base domain for subdomain routing (e.g., `qr-app.test`)
- `APP_URL`: Full application URL
- `DB_*`: Database credentials
- `CLOUDINARY_*`: Cloudinary API credentials (cloud_name, api_key, api_secret)
- `JWT_SECRET`: JWT token secret (generate with `php artisan jwt:secret`)

## Testing Strategy

This project uses **Pest PHP** for testing:
- Test files in `tests/Feature/` and `tests/Unit/`
- Database uses SQLite in-memory for tests (see phpunit.xml)
- Example test structure for API endpoints in tests/Feature/

When writing tests:
- Use Pest syntax: `it('description', function() {...})`
- Use factories for model creation
- Test API endpoints with JWT authentication
- Test both success and error scenarios

## Important Conventions

### API Responses
All API responses use helper functions:
```php
// Success
return success('Message', $data, Response::HTTP_OK);

// Error
return error('Error message', [], Response::HTTP_BAD_REQUEST);
```

### Authentication Access
Always use `authUser()` helper instead of `auth()->user()`:
```php
$userId = authUser()->userid; // Returns User or JsonResponse on error
```

### Validation
Validation rules are centralized in `config/validation.php`:
```php
$validated_data = $this->validate($request, config('validation.business_info'));
```

### Slug Generation
Business links and subdomains use Laravel's `Str::slug()`:
- Automatically handles uniqueness
- Appends counter if duplicate exists
- Format: `business-name` or `business-name-1`

### Media Upload Flow
1. Validate file in controller
2. Upload to Cloudinary via CloudinaryStorage service
3. Store URL in database (VendorMedia or Item model)
4. Return secure Cloudinary URL to frontend

### QR Code Generation
QR codes are generated on-demand and stored in Cloudinary:
1. Generate QR PNG with SimpleSoftwareIO
2. Convert to base64 data URI
3. Upload to Cloudinary via QrCodeService
4. Store URL in `business_links.business_qr` field

## Database Notes

The database uses a multi-table structure:
- `users`: Main user authentication
- `user_data`: Extended vendor business information
- `vendors`: Subdomain configuration (newer model)
- `business_links`: Links vendors to menus and QR codes
- `items`: Menu items linked to business_link_id
- `categories` and `sub_categories`: Menu organization
- `vendor_media`: Logo and hero images
- `countries`, `states`, `cities`: Location data

**Important:** Items use `business_link_id` (not a direct vendor relationship). This allows multiple business links per vendor.

## Local Development Setup

This project is designed to work with Laravel Herd or Valet for subdomain support:

1. Ensure your local TLD matches `APP_DOMAIN` (e.g., `.test`)
2. Configure wildcard DNS for subdomains
3. Set up database and run migrations
4. Configure Cloudinary credentials
5. Generate JWT secret
6. Test subdomain routing: `http://test-vendor.qr-app.test`

## Key Files to Reference

- **Routes:** `routes/api.php`, `routes/subdomain.php`
- **Main Controllers:**
  - `app/Http/Controllers/Vendor/VendorController.php`
  - `app/Http/Controllers/Admin/Vendor/VendorController.php`
  - `app/Http/Controllers/SubdomainController.php`
- **Repository:** `app/Repositories/VendorRepository.php`
- **Services:** `app/Services/QrCodeService.php`, `app/Services/CloudinaryStorage.php`
- **Middleware:** `app/Http/Middleware/SubdomainMiddleware.php`
- **Models:** Check `app/Models/` for relationships and schema
