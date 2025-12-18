# Security Policy

## Security Features

This application implements multiple layers of security to protect sensitive environment file data:

### 1. Encryption at Rest
- All env file contents are encrypted using Laravel's built-in `Crypt::encryptString()` method
- Encryption uses the `APP_KEY` from your `.env` file
- Data is automatically decrypted when retrieved through the model's accessor

### 2. Authentication & Authorization
- All env file routes require user authentication via Laravel Breeze
- Email verification is required for accessing env file management
- Policy-based authorization ensures users can only access their own files
- `EnvFilePolicy` enforces strict ownership checks on all operations

### 3. Input Validation
- All user inputs are validated using Livewire's validation attributes
- Sanitization is performed automatically by Laravel
- Maximum lengths enforced on all text fields
- Environment values restricted to predefined options

### 4. CSRF Protection
- Laravel's built-in CSRF protection is enabled on all forms
- Livewire automatically handles CSRF tokens
- All POST requests are protected

### 5. Secure Headers
- Laravel's default security headers are enabled
- Content Security Policy (CSP) can be configured in middleware
- XSS protection enabled by default

### 6. Database Security
- Foreign key constraints ensure data integrity
- Cascade deletes prevent orphaned records
- User IDs are indexed for performance

### 7. Error Handling
- Encryption/decryption failures are gracefully handled
- Errors are logged for debugging without exposing sensitive data
- Application continues to function even if decryption fails

## Security Testing

The application includes a comprehensive test suite to ensure security:

### Test Coverage
- **Encryption/Decryption Security** (13 tests)
  - Verifies content is encrypted at rest in database
  - Confirms decryption works correctly when accessed
  - Tests special characters and unicode preservation
  
- **Authorization Policies** (12 tests)
  - Ensures users can only access their own files
  - Verifies all CRUD operations respect ownership
  - Tests unauthorized access is blocked
  
- **Component Security** (14 tests)
  - XSS protection through proper escaping
  - SQL injection prevention in search
  - Input validation for all fields
  - CSRF protection on forms
  
- **Model Behavior** (10 tests)
  - Cascade delete on user removal
  - Content re-encryption on updates
  - Relationship integrity

### Running Security Tests
```bash
# Run all security tests
php artisan test --filter=EnvFile

# Run specific test suites
php artisan test --filter=EnvFileSecurity
php artisan test --filter=EnvFilePolicy
php artisan test --filter=EnvFileComponent
php artisan test --filter=EnvFileModel
```

### Security Audit
```bash
# Check for dependency vulnerabilities
composer audit

# Keep dependencies updated
composer update
```

## Best Practices for Deployment

### 1. Protect Your APP_KEY
```bash
# Generate a strong application key
php artisan key:generate

# Keep APP_KEY secret - never commit to version control
# Backup APP_KEY securely - encrypted data cannot be recovered without it
```

### 2. Use HTTPS in Production
```nginx
# Example Nginx configuration
server {
    listen 443 ssl http2;
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # Force HTTPS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
}
```

### 3. Environment Configuration
```env
# Production .env settings
APP_ENV=production
APP_DEBUG=false

# Use strong database credentials
DB_PASSWORD=your_strong_database_password

# Configure session security
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### 4. Database Backups
- Regular automated backups of the database
- Encrypted backup storage
- Test restoration procedures regularly
- Keep backups in a secure, separate location

### 5. Access Control
- Implement strong password policies
- Consider adding two-factor authentication (2FA)
- Use Laravel Sanctum for API authentication if needed
- Regular security audits of user accounts

### 6. Rate Limiting
```php
// config/fortify.php or middleware
'limiters' => [
    'login' => 5,  // 5 attempts per minute
],
```

### 7. Keep Dependencies Updated
```bash
# Regularly update dependencies
composer update
npm update

# Check for security vulnerabilities
composer audit
npm audit
```

## Reporting Security Vulnerabilities

If you discover a security vulnerability, please send an email to the repository maintainer. Do not create a public GitHub issue.

**Please include:**
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

## Security Checklist for Production

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated and backed up
- [ ] HTTPS enabled with valid SSL certificate
- [ ] Database credentials are strong and unique
- [ ] `SESSION_SECURE_COOKIE=true` enabled
- [ ] Regular automated database backups configured
- [ ] All dependencies up to date
- [ ] File permissions properly configured (storage, bootstrap/cache)
- [ ] Error logging configured (don't expose stack traces)
- [ ] Rate limiting enabled on authentication routes
- [ ] Consider implementing 2FA for users
- [ ] Regular security audits scheduled

## Encryption Key Management

⚠️ **CRITICAL**: If you lose your `APP_KEY`, all encrypted env file data will be permanently unrecoverable!

**Backup Strategy:**
1. Store `APP_KEY` in a secure password manager
2. Keep encrypted offline backup
3. Document key rotation procedures
4. Never commit `APP_KEY` to version control

## Additional Security Recommendations

### Content Security Policy
Consider implementing CSP headers to prevent XSS attacks:

```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    $response->headers->set('Content-Security-Policy', "default-src 'self'");
    return $response;
}
```

### Audit Logging
Consider implementing audit logs for sensitive operations:
- Track who created/edited/deleted env files
- Log authentication attempts
- Monitor for suspicious activity

### Regular Security Updates
- Subscribe to Laravel security announcements
- Monitor CVE databases for PHP/Laravel vulnerabilities
- Keep server software updated (PHP, web server, database)
