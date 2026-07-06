# Complete System Documentation

This document consolidates all system documentation, guides, reviews, and fixes.

---

## Table of Contents

1. [Deployment Guide](#deployment-guide)
2. [Account Status System](#account-status-system)
3. [Joint Account System](#joint-account-system)
4. [Security Audit](#security-audit)
5. [Design Patterns](#design-patterns)
6. [Transaction System](#transaction-system)
7. [Investment Module](#investment-module)
8. [Admin Permissions](#admin-permissions)
9. [Branding System](#branding-system)

---

## Deployment Guide

### Environment Variables

For production deployment, set the following environment variables:

```bash
# Database Configuration
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_secure_database_password

# Security Settings
ENCRYPTION_KEY=your_secure_encryption_key_here

# Email Configuration
SMTP_HOST=your_smtp_host
SMTP_PORT=465
SMTP_USER=your_smtp_username
SMTP_PASS=your_smtp_password
SMTP_FROM=noreply@yourdomain.com
SMTP_FROM_NAME=Your Bank Name

# SMS Configuration (Twilio)
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=+1234567890

# Currency API
EXCHANGE_RATE_API_KEY=your_exchange_rate_api_key
```

### Production Security Checklist

1. **Remove Demo Data**: Delete demo user accounts, test transactions, and clear demo credentials
2. **Change Default Passwords**: Update admin passwords, change all default PINs, update encryption keys
3. **Database Security**: Use strong passwords, enable SSL connections, restrict user permissions
4. **File Permissions**: Set 644 for files, 755 for directories, protect sensitive files with .htaccess
5. **SSL/HTTPS**: Enable SSL certificates, force HTTPS redirects, update SITE_URL to use HTTPS

### Admin User Management Features

All admin user management sub-pages are fully functional:
- User Information Edit (`/admin/user-edit/{id}`)
- User Profile View (`/admin/user-profile/{id}`)
- User Transactions (`/admin/user-transactions/{id}`)

Quick Actions Available:
- Adjust user balance
- Reset passwords
- Toggle 2FA
- Set transaction processing modes
- Manage account status
- Delete user accounts

---

## Account Status System

### Available Account Statuses

- `pending` (Default for new registrations)
- `active` (Full access)
- `suspended` (Temporarily disabled)
- `blocked` (Permanently disabled)
- `hold` (Account on hold, limited functionality)
- `restricted` (Limited functionality)
- `closed` (Account closed)

### Status Behavior Summary

| Status | Can Login | Can Transfer | Session Check | Notes |
|--------|-----------|--------------|---------------|-------|
| `pending` | ✅ Yes | ✅ Yes* | ✅ Allowed | *May require KYC |
| `active` | ✅ Yes | ✅ Yes | ✅ Allowed | Full access |
| `suspended` | ❌ No | ❌ No | ❌ Blocked | Immediate logout |
| `blocked` | ❌ No | ❌ No | ❌ Blocked | Immediate logout |
| `hold` | ❌ No | ❌ No | ❌ Blocked | Limited functionality |
| `restricted` | ❓ Varies | ⚠️ Pending | ❓ Varies | May allow login |
| `closed` | ❌ No | ❌ No | ❌ Blocked | Account closed |

### Status Change Flow

**Registration → Pending:**
```
User Registers → Status: 'pending' (default)
  ↓
Email Verification Required
  ↓
Can Login (status: 'pending')
```

**Pending → Active:**
```
User Submits KYC
  ↓
Admin Reviews KYC
  ↓
Admin Approves KYC → Status: 'active' (automatic)
```

**Active → Suspended/Blocked:**
```
Admin Suspends User → Status: 'suspended'/'blocked'
  ↓
Session Terminated Immediately
  ↓
All Transactions Blocked
```

### Security Checks

Status is checked in:
1. **Login** (`controllers/AuthController.php`) - Allows: `['active', 'pending']`
2. **Session Validation** (`includes/security.php`) - Runs on every request
3. **Transfer Processing** (`api/process-transfer.php`) - Blocks suspended/blocked
4. **User Lookup** (`api/lookup-user.php`) - Allows active/pending for recipient lookup

---

## Joint Account System

### Overview

The joint account system allows multiple users to share access to a single account with the same balance, transactions, and account number.

### Account Types

- **Joint Account**: Create a new joint account (user becomes primary owner)
- **Join Existing Account**: Request to join an existing account

### Features

- Joint owners see the same account balance
- Joint owners see the same transactions
- Joint owners see the same account number
- No account duplication - shared account with multiple owners
- Email notifications for requests, approvals, and rejections
- 7-day expiration on requests
- Primary owner approval required

### Database Tables

- `account_owners`: Tracks all owners of joint accounts
- `joint_account_requests`: Manages pending join requests

### Implementation Details

**Files Modified:**
- `models/JointAccount.php` - Core joint account logic
- `models/Account.php` - Updated to use joint account queries
- `models/Transaction.php` - Includes joint account transactions
- `controllers/AccountController.php` - Joint request approval/rejection
- `views/auth/register.php` - Joint account registration options
- `views/account/joint-requests.php` - Approval interface
- `views/profile/index.php` - Shows account owners list

### Issues Fixed

1. **Transaction Access Control**: Updated to check joint account access
2. **Transaction Statistics**: Updated queries to include joint accounts
3. **Dashboard Queries**: All transaction queries now include joint accounts

### Testing Checklist

- [ ] Register with "Joint Account" type
- [ ] Register with "Join Existing Account" and search
- [ ] Primary owner receives email notification
- [ ] Approve/reject joint request
- [ ] Joint owner sees same balance and transactions
- [ ] Dashboard shows joint account transactions

---

## Security Audit

### Security Features Implemented

1. **Password Security**
   - Bcrypt hashing with cost factor 12
   - Minimum 8 characters with complexity requirements
   - Password reset tokens with expiration

2. **Session Management**
   - Secure session handling with regeneration
   - Automatic timeout (30 minutes default)
   - Session validation on every request

3. **CSRF Protection**
   - Token generation and validation
   - Required for all form submissions
   - Automatic token refresh

4. **XSS Prevention**
   - Input sanitization using `Security::sanitize()`
   - Output escaping with `htmlspecialchars()`
   - Content Security Policy headers

5. **SQL Injection Prevention**
   - PDO prepared statements throughout
   - Parameterized queries
   - No direct SQL concatenation

6. **Encryption**
   - AES-256 encryption for sensitive data
   - Secure encryption key management
   - Encrypted storage for sensitive fields

7. **Access Control**
   - Role-based access control (admin/user)
   - Account status validation
   - Joint account access verification

### Security Headers

- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Content-Security-Policy: Comprehensive CSP
- Strict-Transport-Security: HSTS enabled

### Recommendations

1. Regular security audits
2. Keep PHP and dependencies updated
3. Monitor error logs for suspicious activity
4. Regular database backups
5. SSL certificate maintenance
6. Review and update CSP policies as needed

---

## Design Patterns

### MVC-Like Architecture

- **Models**: Data access layer (`models/`)
- **Controllers**: Business logic (`controllers/`)
- **Views**: Presentation layer (`views/`)

### Database Pattern

- Singleton Database class
- PDO for all database operations
- Prepared statements for security
- Transaction support for complex operations

### Security Pattern

- Centralized security functions (`includes/security.php`)
- Input validation and sanitization
- Output escaping
- CSRF token management

### Session Management

- Secure session initialization
- Automatic session validation
- Status-based access control
- Session timeout handling

---

## Transaction System

### Transaction Types

- `credit` - Money coming in
- `debit` - Money going out

### Transaction Statuses

- `pending` - Awaiting processing
- `completed` - Successfully processed
- `failed` - Processing failed
- `cancelled` - User or admin cancelled

### Transaction Processing

1. Validation (amount, account, status)
2. Balance check
3. Transaction creation
4. Balance update (if completed)
5. Activity logging

### Joint Account Transactions

- All joint owners see all transactions
- Transactions filtered by account_id (not user_id)
- Access control verified for transaction viewing

### Issues Fixed

1. Joint account transactions now visible to all owners
2. Dashboard statistics include joint account transactions
3. Transaction queries updated to use account_id filtering

---

## Investment Module

### Investment Products

- Crypto currencies
- Forex pairs
- Stocks/ETFs
- Fixed deposits

### Investment Features

- Principal investment tracking
- ROI calculation and accrual
- Maturity date management
- Withdrawal requests
- Funding from bank accounts

### Investment Statuses

- `pending` - Awaiting funding
- `active` - Investment active
- `matured` - Investment matured
- `closed` - Investment closed
- `cancelled` - Investment cancelled

### ROI Calculation

- Daily percentage-based ROI
- Automatic accrual via cron job
- Current accrued tracking
- Total ROI paid tracking

---

## Admin Permissions

### Admin Capabilities

1. **User Management**
   - View all users
   - Edit user information
   - Change account status
   - Reset passwords
   - Adjust balances
   - Delete users

2. **Transaction Management**
   - View all transactions
   - Edit transaction details
   - Reverse transactions
   - Delete transactions
   - Set transaction modes

3. **Loan Management**
   - Approve/reject loans
   - View loan details
   - Manage loan payments

4. **Card Management**
   - Approve/reject cards
   - View card details
   - Delete cards

5. **KYC Management**
   - Review KYC documents
   - Approve/reject KYC
   - View verification status

6. **System Settings**
   - Configure system parameters
   - Manage fees and rates
   - Update branding
   - System alerts

### Admin Audit Logs

All admin actions are logged:
- User modifications
- Transaction changes
- Status updates
- Balance adjustments
- System configuration changes

---

## Branding System

### Branding Features

- Dynamic site name from database
- Customizable logo
- Email template branding
- System-wide branding consistency

### Implementation

- `SystemSettings` class manages branding
- `getSiteName()` function retrieves site name
- Email templates use dynamic branding
- Admin panel for branding updates

### Logo Management

See `LOGO_LICENSE_ATTRIBUTION.md` for:
- Logo licensing information
- Attribution requirements
- CDN vs self-hosted options
- Logo update procedures

---

## API Endpoints

### User APIs
- `/api/get-user-accounts.php` - Get user accounts
- `/api/update-user-info.php` - Update user information
- `/api/change-password.php` - Change password
- `/api/update-login-pin.php` - Update login PIN
- `/api/update-transfer-pin.php` - Update transfer PIN
- `/api/toggle-2fa.php` - Enable/disable 2FA

### Transaction APIs
- `/api/process-transfer.php` - Process money transfers
- `/api/get-account-data.php` - Get account data
- `/api/get-expense-data.php` - Get expense analytics
- `/api/generate-receipt.php` - Generate transaction receipt

### Admin APIs
- `/api/admin-adjust-balance.php` - Adjust user balance
- `/api/admin-edit-transaction.php` - Edit transaction
- `/api/admin-reverse-transaction.php` - Reverse transaction
- `/api/admin-delete-transaction.php` - Delete transaction
- `/api/admin-approve-loan.php` - Approve loan
- `/api/admin-reject-loan.php` - Reject loan
- `/api/admin-approve-kyc.php` - Approve KYC
- `/api/admin-reject-kyc.php` - Reject KYC

### Joint Account APIs
- `/api/search-account.php` - Search account by number

---

## Troubleshooting

### Common Issues

**Problem**: "Page Not Found" errors
- **Solution**: Enable `mod_rewrite` in Apache, check `.htaccess` exists

**Problem**: Database connection failed
- **Solution**: Check credentials in `config/config.php`, verify database exists

**Problem**: Email not sending
- **Solution**: Verify SMTP credentials, check port 587 is open, use App Password for Gmail

**Problem**: Images not uploading
- **Solution**: Check `uploads/` directory exists, verify permissions (755), check `upload_max_filesize` in php.ini

**Problem**: Session timeout too short
- **Solution**: Adjust `SESSION_LIFETIME` in `config/config.php`

### Debug Mode

Set in `config/config.php`:
```php
define('DEVELOPMENT_MODE', true);
```

This enables:
- Error display
- Detailed error logging
- Debug information

**Important**: Always set to `false` in production!

---

## Database Management

### Backup Database

```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

### Restore Database

```bash
mysql -u username -p database_name < backup_20240101.sql
```

### Via phpMyAdmin

- Export: Database → Export → Go
- Import: Database → Import → Choose File → Go

---

## Performance Optimization

1. **Enable Caching**
   - Use OPcache for PHP
   - Implement Redis/Memcached for sessions

2. **Optimize Database**
   - Add indexes to frequently queried columns
   - Regular `OPTIMIZE TABLE` maintenance

3. **CDN Integration**
   - Serve static assets from CDN
   - Use cloud storage for uploads

4. **Enable Gzip Compression**
   - Already enabled in `.htaccess`
   - Reduces page load time

---

*Last Updated: Consolidated from all system documentation files*

