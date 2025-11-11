# Security Improvements TODO

## High Priority

### 1. Input Validation & Sanitization
- [ ] Add comprehensive request validation to all controller methods
- [ ] Create FormRequest classes for complex validations instead of inline `$this->validate()`
- [ ] Sanitize user inputs before storing (especially business_name, descriptions)
- [ ] Implement file upload validation (file size limits, MIME type checking)
- [ ] Add XSS protection for all text fields that will be displayed

### 2. Authentication & Authorization
- [ ] Implement rate limiting on login/register endpoints to prevent brute force attacks
- [ ] Add password strength requirements in validation rules
- [ ] Implement account lockout after failed login attempts
- [ ] Add email verification requirement before account activation
- [ ] Implement 2FA (Two-Factor Authentication) for admin accounts
- [ ] Add password reset functionality with secure token generation
- [ ] Set JWT token expiration to reasonable duration (currently appears unlimited)
- [ ] Implement token blacklisting for logout (prevent token reuse)

### 3. Environment & Configuration
- [ ] Remove hardcoded credentials from `.env.example` (line 17 shows password)
- [ ] Add validation for required environment variables on application boot
- [ ] Implement secrets management (consider using Laravel's encryption for sensitive config)
- [ ] Ensure `.env` file is in `.gitignore` and never committed

### 4. SQL Injection Prevention
- [ ] Audit all raw queries for potential SQL injection
- [ ] Ensure all Eloquent queries use parameter binding
- [ ] Review VendorRepository for any unsafe query construction

### 5. CSRF Protection
- [ ] Ensure CSRF tokens are validated for state-changing operations
- [ ] Configure proper CORS headers for API consumption
- [ ] Whitelist allowed origins in CORS configuration

### 6. File Upload Security
- [ ] Validate file extensions and MIME types strictly
- [ ] Implement virus scanning for uploaded files
- [ ] Store uploaded files outside web root (Cloudinary handles this, but validate)
- [ ] Add file size limits to prevent DoS via large uploads
- [ ] Generate random filenames to prevent file enumeration

## Medium Priority

### 7. API Security
- [ ] Implement API versioning (e.g., `/api/v1/...`)
- [ ] Add request signature validation for sensitive operations
- [ ] Implement IP whitelisting for admin routes
- [ ] Add honeypot fields to prevent bot registrations
- [ ] Implement CAPTCHA on registration and login forms

### 8. Session & Cookie Security
- [ ] Set secure cookie flags (httpOnly, secure, sameSite)
- [ ] Implement session timeout for inactive users
- [ ] Add session hijacking prevention mechanisms

### 9. Logging & Monitoring
- [ ] Log all authentication attempts (success and failures)
- [ ] Log all admin actions for audit trail
- [ ] Implement intrusion detection logging
- [ ] Set up alerts for suspicious activities
- [ ] Ensure sensitive data is not logged (passwords, tokens)

### 10. Database Security
- [ ] Use separate database users with minimal privileges
- [ ] Encrypt sensitive data at rest (consider Laravel's encrypted casts)
- [ ] Regular database backups with encryption
- [ ] Implement soft deletes for critical data (vendors, items)

## Low Priority

### 11. Headers & Response Security
- [ ] Add security headers (X-Frame-Options, X-Content-Type-Options, etc.)
- [ ] Implement Content Security Policy (CSP)
- [ ] Add HSTS header for HTTPS enforcement
- [ ] Remove server version disclosure headers

### 12. Dependency Security
- [ ] Run `composer audit` regularly to check for vulnerable dependencies
- [ ] Set up automated dependency security scanning (Dependabot, Snyk)
- [ ] Keep Laravel and all packages up to date

### 13. Code Security
- [ ] Remove commented-out code and debug statements
- [ ] Disable debug mode in production (`APP_DEBUG=false`)
- [ ] Remove development tools from production dependencies
- [ ] Implement proper error handling (don't expose stack traces to users)

### 14. Subdomain Security
- [ ] Validate subdomain format to prevent subdomain takeover
- [ ] Implement subdomain reservation (prevent using reserved words)
- [ ] Add rate limiting per subdomain to prevent abuse
- [ ] Validate that subdomain resolution doesn't leak data

## Immediate Actions Needed

### Critical Issues Found:
1. **Password in .env.example** (line 17): Remove the example password
2. **authUser() helper** (app/Helpers/functions.php:9): Returns error response on exception, but this might leak sensitive info
3. **Missing Request Validation**: Many controller methods don't have proper validation
4. **No rate limiting**: Login/register endpoints are vulnerable to brute force
5. **QR Code URL**: Verify that QR code URLs can't be manipulated to redirect to malicious sites
