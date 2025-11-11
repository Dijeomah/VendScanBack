# Code Quality Improvements TODO

## High Priority

### 1. Error Handling & Logging
- [ ] Remove generic try-catch blocks that catch all exceptions
- [ ] Implement specific exception handling for different error types
- [ ] Create custom exception classes for business logic errors
- [ ] Use Laravel's exception handler for centralized error handling
- [ ] Remove debug logging (e.g., `Log::debug()`) from production code
- [ ] Standardize error messages and response formats
- [ ] Add context to all log messages (user_id, request_id, etc.)

### 2. Code Duplication
- [ ] Extract repeated validation logic into FormRequest classes
- [ ] Create service classes for business logic (separate from repositories)
- [ ] Consolidate duplicate controller methods across Admin/Vendor controllers
- [ ] Create traits for shared functionality (media upload, QR generation)
- [ ] Extract common query patterns into Eloquent scopes

### 3. Controller Cleanup
- [ ] Remove commented-out code from controllers (e.g., VendorController.php:23-24)
- [ ] Split large controllers into smaller, focused controllers
- [ ] Move business logic from controllers to service classes
- [ ] Implement single responsibility principle for each controller method
- [ ] Remove inline validation, use FormRequest classes instead
- [ ] Standardize method naming conventions across controllers

### 4. Repository Pattern Improvements
- [ ] Create interfaces for all repositories (currently only VendorInterface exists)
- [ ] Bind interfaces to implementations in service provider
- [ ] Split VendorRepository into smaller, focused repositories
- [ ] Remove direct model calls from controllers (use repositories)
- [ ] Add return type hints to all repository methods
- [ ] Document complex repository methods with PHPDoc

### 5. Database Queries
- [ ] Add eager loading to prevent N+1 query problems
- [ ] Review and optimize queries with multiple joins
- [ ] Add database indexes for frequently queried columns
- [ ] Use query scopes for reusable query logic
- [ ] Implement query result caching for expensive queries
- [ ] Review use of `firstOrFail()` vs `first()` for appropriate error handling

## Medium Priority

### 6. Code Documentation
- [ ] Add PHPDoc blocks to all public methods
- [ ] Document complex business logic and algorithms
- [ ] Add parameter and return type documentation
- [ ] Create inline comments for non-obvious code sections
- [ ] Document all model relationships
- [ ] Add examples in PHPDoc for complex methods

### 7. Type Hinting
- [ ] Add strict types declaration to all PHP files
- [ ] Add return type hints to all methods
- [ ] Add parameter type hints to all methods
- [ ] Use union types where appropriate (PHP 8+)
- [ ] Replace mixed types with specific types where possible

### 8. Naming Conventions
- [ ] Standardize variable naming (camelCase vs snake_case)
- [ ] Use descriptive names for variables (avoid `$data`, `$payload`)
- [ ] Rename methods to reflect their actions clearly
- [ ] Follow Laravel naming conventions for models and relationships
- [ ] Use consistent naming for similar operations across controllers

### 9. Response Handling
- [ ] Create API Resource classes for consistent JSON responses
- [ ] Use HTTP status code constants instead of magic numbers
- [ ] Standardize success/error response structure
- [ ] Add pagination to list endpoints
- [ ] Implement API versioning for future compatibility

### 10. Configuration Management
- [ ] Move validation rules to FormRequest classes (not config files)
- [ ] Create config files for business logic constants
- [ ] Use config values instead of hardcoded strings/numbers
- [ ] Organize config files by feature/domain
- [ ] Add comments to explain complex configuration values

## Low Priority

### 11. Code Style
- [ ] Run Laravel Pint to fix code style issues
- [ ] Configure PHPStan for static analysis
- [ ] Set up pre-commit hooks for code quality checks
- [ ] Follow PSR-12 coding standards consistently
- [ ] Remove trailing whitespace and fix indentation

### 12. Model Improvements
- [ ] Add fillable/guarded properties to all models
- [ ] Use casts for date/boolean/JSON fields
- [ ] Add model events (creating, created, etc.) for audit logging
- [ ] Implement model observers for complex model events
- [ ] Add accessors and mutators for data transformation

### 13. Service Layer
- [ ] Create dedicated service classes for complex operations
- [ ] Move QR generation logic to a service class (already done ✓)
- [ ] Create MediaService for all media operations
- [ ] Create BusinessService for business link operations
- [ ] Separate concerns: Controllers → Services → Repositories → Models

### 14. Helper Functions
- [ ] Move helper functions to dedicated classes
- [ ] Create Facades for frequently used helpers
- [ ] Add type hints to helper functions
- [ ] Consider replacing global helpers with dependency injection
- [ ] Document all helper functions

### 15. Route Organization
- [ ] Group related routes together
- [ ] Use route model binding where applicable
- [ ] Add route names to all routes for easier maintenance
- [ ] Consider extracting route groups to separate files
- [ ] Use route caching for production (`php artisan route:cache`)

## Specific Issues to Fix

### Found Issues:
1. **VendorController.php:23-26**: Remove commented middleware code
2. **authUser() helper**: Inconsistent error handling (returns JsonResponse or User)
3. **VendorRepository.php:103**: Updates auth()->user() directly - consider using dedicated method
4. **Multiple controllers**: Catching `\Exception` is too broad - catch specific exceptions
5. **QrCodeService.php:20**: Commented-out log statement should be removed
6. **Duplicate routes**: `/api/vendor/media` and `/api/vendor/media/upload` (line 114-115 in routes/api.php)
7. **Missing validation**: Several controller methods lack input validation

## Refactoring Candidates

### Classes that need refactoring:
- `VendorController`: Too many responsibilities (profile, business, media, menu)
- `VendorRepository`: Split into ProfileRepository, BusinessRepository, MediaRepository
- `AuthController`: Add proper validation and error handling
- Helper functions: Convert to proper classes with dependency injection
