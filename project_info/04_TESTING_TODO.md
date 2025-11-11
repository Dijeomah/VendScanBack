# Testing Improvements TODO

## High Priority

### 1. Test Coverage
- [ ] Achieve minimum 80% code coverage
- [ ] Write tests for all controller endpoints
- [ ] Write tests for all repository methods
- [ ] Write tests for all service classes
- [ ] Write tests for helper functions
- [ ] Write tests for middleware
- [ ] Write tests for model relationships

### 2. Feature Tests (API Endpoints)
- [ ] **Authentication Tests**:
  - [ ] Test user registration with valid data
  - [ ] Test user registration with invalid data
  - [ ] Test login with valid credentials
  - [ ] Test login with invalid credentials
  - [ ] Test logout functionality
  - [ ] Test token refresh
  - [ ] Test unauthorized access attempts

- [ ] **Vendor Tests**:
  - [ ] Test vendor dashboard access
  - [ ] Test vendor profile retrieval and update
  - [ ] Test business info creation
  - [ ] Test business link creation with unique subdomain
  - [ ] Test duplicate business name prevention
  - [ ] Test media upload (logo, hero)
  - [ ] Test QR code generation
  - [ ] Test full vendor profile with menu

- [ ] **Admin Tests**:
  - [ ] Test admin dashboard access
  - [ ] Test vendor CRUD operations
  - [ ] Test business link management
  - [ ] Test category management
  - [ ] Test item management
  - [ ] Test QR code generation for vendors

- [ ] **Menu Tests**:
  - [ ] Test category creation
  - [ ] Test subcategory creation
  - [ ] Test item CRUD operations
  - [ ] Test items by category retrieval
  - [ ] Test item status toggle

- [ ] **Public Routes**:
  - [ ] Test vendor menu retrieval by subdomain
  - [ ] Test vendor menu retrieval by link
  - [ ] Test 404 for non-existent vendors
  - [ ] Test menu data structure

### 3. Unit Tests
- [ ] Test VendorRepository methods
- [ ] Test QrCodeService QR generation
- [ ] Test CloudinaryStorage upload methods
- [ ] Test helper functions (authUser, success, error)
- [ ] Test model factories
- [ ] Test model relationships
- [ ] Test model scopes and accessors
- [ ] Test custom validation rules

### 4. Integration Tests
- [ ] Test complete vendor registration flow
- [ ] Test complete menu creation flow
- [ ] Test QR code generation and storage flow
- [ ] Test media upload and Cloudinary integration
- [ ] Test subdomain routing and resolution
- [ ] Test JWT token lifecycle
- [ ] Test role-based access control

### 5. Database Tests
- [ ] Test migrations run without errors
- [ ] Test seeders populate data correctly
- [ ] Test foreign key constraints
- [ ] Test cascade deletes work as expected
- [ ] Test unique constraints
- [ ] Test default values

## Medium Priority

### 6. Test Organization
- [ ] Organize tests by feature (Vendor, Menu, Auth, Admin)
- [ ] Create base test classes for common setup
- [ ] Use traits for reusable test methods
- [ ] Implement test helpers for authentication
- [ ] Create factories for all models
- [ ] Use database transactions for test isolation

### 7. Edge Cases & Error Handling
- [ ] Test validation errors for all endpoints
- [ ] Test unauthorized access to protected routes
- [ ] Test role mismatch (vendor accessing admin routes)
- [ ] Test SQL injection attempts
- [ ] Test XSS attempts in input fields
- [ ] Test file upload limits
- [ ] Test invalid file types
- [ ] Test duplicate subdomain handling
- [ ] Test special characters in business names

### 8. Performance Tests
- [ ] Test response times for list endpoints
- [ ] Test N+1 query prevention with eager loading
- [ ] Test database query count for critical paths
- [ ] Test memory usage for large data sets
- [ ] Test concurrent user access
- [ ] Benchmark QR code generation time

### 9. Security Tests
- [ ] Test JWT token expiration
- [ ] Test token refresh security
- [ ] Test CSRF protection
- [ ] Test SQL injection prevention
- [ ] Test XSS prevention
- [ ] Test file upload security
- [ ] Test rate limiting effectiveness
- [ ] Test authorization bypass attempts

### 10. Mock & Stub External Services
- [ ] Mock Cloudinary API calls in tests
- [ ] Mock QR code generation
- [ ] Mock email sending
- [ ] Mock external API calls
- [ ] Create test doubles for external dependencies

## Low Priority

### 11. Browser/E2E Tests (If Frontend Exists)
- [ ] Set up Laravel Dusk for browser testing
- [ ] Test complete user registration flow
- [ ] Test login and logout flow
- [ ] Test menu creation workflow
- [ ] Test QR code download
- [ ] Test responsive design

### 12. Test Data Management
- [ ] Create comprehensive model factories
- [ ] Create seeders for test data
- [ ] Implement test data builders for complex scenarios
- [ ] Create fixture files for test data
- [ ] Document test data scenarios

### 13. Continuous Testing
- [ ] Set up PHPUnit code coverage reporting
- [ ] Integrate code coverage in CI/CD pipeline
- [ ] Set up mutation testing (Infection PHP)
- [ ] Add test execution to pre-commit hooks
- [ ] Create test reports and dashboards

### 14. Test Documentation
- [ ] Document testing strategy
- [ ] Create test writing guidelines
- [ ] Document how to run specific tests
- [ ] Add examples of good test structure
- [ ] Document mock and stub usage

## Test Infrastructure Setup

### Required Setup:
- [ ] Configure PHPUnit with proper database setup
- [ ] Set up in-memory SQLite for faster tests
- [ ] Configure Pest PHP properly
- [ ] Set up test environment variables
- [ ] Create test helper traits
- [ ] Set up code coverage reporting
- [ ] Configure parallel test execution

### Test Utilities:
- [ ] Create `actingAsVendor()` helper for authenticated vendor tests
- [ ] Create `actingAsAdmin()` helper for authenticated admin tests
- [ ] Create `createVendorWithMenu()` helper
- [ ] Create `createBusinessLink()` helper
- [ ] Create assertion helpers for API responses

## Missing Test Files

### Tests to Create:
```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── RegistrationTest.php
│   │   ├── LoginTest.php
│   │   ├── LogoutTest.php
│   │   └── TokenRefreshTest.php
│   ├── Vendor/
│   │   ├── VendorProfileTest.php
│   │   ├── BusinessInfoTest.php
│   │   ├── MenuManagementTest.php
│   │   ├── MediaUploadTest.php
│   │   └── QrCodeGenerationTest.php
│   ├── Admin/
│   │   ├── VendorManagementTest.php
│   │   ├── BusinessManagementTest.php
│   │   └── CategoryManagementTest.php
│   └── Public/
│       ├── SubdomainRoutingTest.php
│       └── VendorMenuTest.php
├── Unit/
│   ├── Repositories/
│   │   └── VendorRepositoryTest.php
│   ├── Services/
│   │   ├── QrCodeServiceTest.php
│   │   └── CloudinaryStorageTest.php
│   ├── Helpers/
│   │   └── HelperFunctionsTest.php
│   └── Models/
│       ├── UserTest.php
│       ├── VendorTest.php
│       ├── ItemTest.php
│       └── CategoryTest.php
└── Integration/
    ├── VendorRegistrationFlowTest.php
    ├── MenuCreationFlowTest.php
    └── SubdomainAccessFlowTest.php
```

## Test Examples to Implement

### Example Test Structure:
```php
// tests/Feature/Vendor/BusinessInfoTest.php
it('creates business info with valid data', function () {
    $vendor = actingAsVendor();

    $response = $this->postJson('/api/vendor/business-info', [
        'business_name' => 'Test Restaurant',
        'business_address' => '123 Main St',
        'city_id' => 1,
        'state_id' => 1,
        'country_id' => 1,
    ]);

    $response->assertStatus(201)
        ->assertJson(['message' => 'Business data created successfully.']);

    $this->assertDatabaseHas('user_data', [
        'business_name' => 'Test Restaurant',
    ]);
});

it('prevents duplicate business names', function () {
    $vendor = actingAsVendor();
    createBusinessInfo(['business_name' => 'Existing Business']);

    $response = $this->postJson('/api/vendor/business-info', [
        'business_name' => 'Existing Business',
        // ... other fields
    ]);

    $response->assertStatus(400)
        ->assertJson(['message' => 'Business data already exist.']);
});
```

## Testing Priorities

### Immediate Testing Needs:
1. Authentication flow (login, register, logout)
2. Vendor business creation with subdomain
3. QR code generation
4. Menu CRUD operations
5. Role-based access control
6. Subdomain routing

### Critical Paths to Test:
- Vendor registration → Business setup → Menu creation → QR generation
- Public subdomain access → Menu display
- Admin vendor management
- Media upload and retrieval
