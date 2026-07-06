# SecureBank - Online Banking Platform

A comprehensive, enterprise-grade online banking platform built with PHP, MySQL, HTML, CSS, and JavaScript. Designed for shared hosting deployment with modern security features and user-friendly interface.

## 🎯 Features

### Core Banking Features
- **Account Management**: Multiple account types (Checking, Savings, Business, Investment, Retirement)
- **Transactions**: Real-time transaction processing with complete audit trail
- **Transfers**: Internal, domestic, and international money transfers
- **Bill Payments**: Schedule and manage recurring bill payments
- **Beneficiary Management**: Save and manage frequent transfer recipients

### Card Services
- **Virtual Cards**: Create single-use or reusable virtual cards
- **Card Management**: Freeze/unfreeze cards, set spending limits
- **CVV Refresh**: Automatic CVV refresh for enhanced security
- **Transaction Tracking**: Real-time card transaction monitoring

### Loan Management
- **Loan Applications**: Personal, auto, mortgage, business, and education loans
- **Payment Scheduling**: Automated payment schedule generation
- **Early Repayment**: Option for early loan repayment
- **Loan Dashboard**: Complete overview of all active loans

### Security Features
- **2FA Authentication**: Email, SMS, and app-based two-factor authentication
- **AES-256 Encryption**: Bank-grade encryption for sensitive data
- **Fraud Detection**: AI-based suspicious activity monitoring
- **Session Management**: Secure session handling with automatic timeouts
- **Activity Logging**: Complete audit trail of all user actions

### User Management
- **KYC Verification**: Document upload and verification system
- **Profile Management**: Complete profile customization
- **Notification Preferences**: Customizable email and SMS notifications
- **Security Questions**: Multi-factor account recovery

### Admin Panel
- **User Management**: View, approve, suspend user accounts
- **Transaction Monitoring**: Real-time transaction oversight
- **Loan Approval System**: Review and approve/reject loan applications
- **System Settings**: Configure fees, rates, and system parameters
- **Activity Reports**: Comprehensive activity and audit logs

### Additional Features
- **Responsive Design**: Mobile-first design with breakpoints for all devices
- **Multi-currency Support**: Handle multiple currencies with real-time exchange rates
- **Spending Insights**: Visual analytics and spending categorization
- **Email Notifications**: Automated email notifications for all activities
- **SMS Integration**: Twilio integration for SMS notifications
- **Export Reports**: PDF and CSV export functionality

## 🛠 Technology Stack

- **Backend**: PHP 7.4+ (pure PHP, no frameworks)
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Architecture**: MVC-like structure with PDO for database access
- **Security**: Password hashing (bcrypt), CSRF protection, XSS prevention
- **Session Management**: Secure PHP sessions with regeneration

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache Web Server with mod_rewrite enabled
- SSL Certificate (recommended for production)
- PHP Extensions:
  - PDO
  - PDO_MySQL
  - OpenSSL
  - mbstring
  - cURL
  - GD or ImageMagick (for image processing)

## 🚀 Installation

### Step 1: Download and Extract

1. Download or clone this repository
2. Extract the files to your server's web directory (e.g., `public_html` or `www`)

### Step 2: Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE online_banking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u your_username -p online_banking < database/schema.sql
```

Or use phpMyAdmin:
- Login to phpMyAdmin
- Select your database
- Go to Import tab
- Choose `database/schema.sql`
- Click "Go"

### Step 3: Configuration

1. Open `config/config.php` and update the following:

```php
// Site Configuration
define('SITE_URL', 'https://yourdomain.com'); // Your domain
define('SITE_NAME', 'Your Bank Name');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'online_banking');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');

// Security - Generate a random 32-character key
define('ENCRYPTION_KEY', 'your-32-character-encryption-key-here');

// Email Configuration (for Gmail SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password'); // Use App Password for Gmail
define('SMTP_FROM', 'noreply@yourdomain.com');

// SMS Configuration (Twilio)
define('TWILIO_SID', 'your-twilio-account-sid');
define('TWILIO_TOKEN', 'your-twilio-auth-token');
define('TWILIO_FROM', '+1234567890'); // Your Twilio phone number

// Currency API (optional - for exchange rates)
define('EXCHANGE_RATE_API_KEY', 'your-api-key');
```

2. Generate a secure encryption key:
```php
// Run this once to generate a key
echo bin2hex(random_bytes(16));
```

### Step 4: File Permissions

Create necessary directories and set permissions:

```bash
mkdir -p uploads/documents uploads/profiles uploads/loans
chmod 755 uploads
chmod 755 uploads/*
```

For shared hosting, use File Manager:
- Create folders: `uploads/documents`, `uploads/profiles`, `uploads/loans`
- Set permissions to 755

### Step 5: .htaccess Configuration

The `.htaccess` file is already included. Make sure:
1. `mod_rewrite` is enabled on your server
2. `AllowOverride All` is set in Apache configuration

If clean URLs don't work, contact your hosting provider to enable `mod_rewrite`.

### Step 6: SSL Certificate (Production)

For production:
1. Install an SSL certificate (Let's Encrypt recommended)
2. Update `.htaccess` to force HTTPS (uncomment lines 4-6)
3. Update `SITE_URL` in `config/config.php` to use `https://`

### Step 7: Test Installation

1. Visit your website: `https://yourdomain.com`
2. You should see the homepage
3. Test login with default admin account:
   - Email: `admin@securebank.com`
   - Password: `Admin@123`
   - **IMPORTANT**: Change this password immediately after first login!

## 📁 Project Structure

```
online-banking/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── app.js             # JavaScript functions
├── config/
│   ├── config.php             # Main configuration
│   └── database.php           # Database class
├── controllers/
│   ├── AuthController.php     # Authentication
│   ├── DashboardController.php
│   ├── AccountController.php
│   ├── TransferController.php
│   ├── CardController.php
│   ├── LoanController.php
│   ├── ProfileController.php
│   └── AdminController.php
├── models/
│   ├── User.php               # User model
│   ├── Account.php            # Account model
│   ├── Transaction.php        # Transaction model
│   ├── Card.php               # Card model
│   ├── Loan.php               # Loan model
│   ├── Notification.php       # Notification model
│   └── Transfer.php           # Transfer model
├── views/
│   ├── layouts/
│   │   ├── header.php         # Page header
│   │   └── footer.php         # Page footer
│   ├── auth/                  # Authentication views
│   ├── dashboard/             # Dashboard views
│   ├── account/               # Account views
│   ├── transfer/              # Transfer views
│   ├── card/                  # Card views
│   ├── loan/                  # Loan views
│   ├── profile/               # Profile views
│   └── admin/                 # Admin views
├── includes/
│   ├── functions.php          # Helper functions
│   └── security.php           # Security functions
├── database/
│   └── schema.sql             # Database schema
├── uploads/                   # Upload directory
├── index.php                  # Main entry point
├── .htaccess                  # Apache configuration
└── README.md                  # This file
```

## 🔐 Security Considerations

### Production Checklist

1. **Change Default Credentials**
   - Change admin password immediately
   - Update all default email addresses

2. **Environment Configuration**
   - Set `display_errors` to `0` in `config/config.php`
   - Set `error_reporting` to `E_ALL & ~E_NOTICE`
   - Use strong, unique encryption key

3. **Database Security**
   - Use strong database passwords
   - Limit database user permissions
   - Regular backups

4. **File Permissions**
   - Config files: 644
   - Directories: 755
   - Upload directory: 755 (with .htaccess protection)

5. **SSL/TLS**
   - Install SSL certificate
   - Force HTTPS redirect
   - Set secure cookie flags

6. **Regular Updates**
   - Keep PHP updated
   - Update dependencies
   - Monitor security advisories

7. **Backup Strategy**
   - Daily database backups
   - Weekly file backups
   - Store backups off-site

## 📧 Email Configuration

### Gmail Setup

1. Enable 2-Step Verification in your Google Account
2. Generate an App Password:
   - Go to Google Account → Security
   - Select "App passwords"
   - Choose "Mail" and your device
   - Copy the generated password

3. Update `config/config.php`:
```php
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-16-character-app-password');
```

### Other Email Providers

For other providers (Outlook, Yahoo, etc.), update:
- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_USER`
- `SMTP_PASS`

## 📱 SMS Configuration (Twilio)

1. Create a Twilio account at https://www.twilio.com
2. Get your Account SID and Auth Token
3. Purchase a phone number
4. Update `config/config.php`:
```php
define('TWILIO_SID', 'your-account-sid');
define('TWILIO_TOKEN', 'your-auth-token');
define('TWILIO_FROM', '+1234567890');
```

## 🎨 Customization

### Changing Colors

Edit `assets/css/style.css` and modify the CSS variables:

```css
:root {
    --primary-color: #032B44;      /* Your primary color */
    --secondary-color: #FFFFFF;     /* Background color */
    --accent-color: #ADD8E6;        /* Accent color */
}
```

### Adding Languages

1. Create language files in `includes/languages/`
2. Update `includes/functions.php` to load language files
3. Add language selector in profile settings

### Custom Features

Follow the MVC structure:
1. Create model in `models/`
2. Create controller in `controllers/`
3. Create views in `views/`
4. Update routing in `index.php` if needed

## 🐛 Troubleshooting

### Common Issues

**Problem**: "Page Not Found" errors
- **Solution**: Enable `mod_rewrite` in Apache
- Check that `.htaccess` file exists
- Verify `AllowOverride All` in Apache config

**Problem**: Database connection failed
- **Solution**: Check database credentials in `config/config.php`
- Verify database exists
- Check MySQL service is running

**Problem**: Email not sending
- **Solution**: Verify SMTP credentials
- Check if port 587 is open
- For Gmail, ensure App Password is used

**Problem**: Images not uploading
- **Solution**: Check `uploads/` directory exists
- Verify directory permissions (755)
- Check `upload_max_filesize` in php.ini

**Problem**: Session timeout too short
- **Solution**: Adjust `SESSION_LIFETIME` in `config/config.php`
- Increase `session.gc_maxlifetime` in php.ini

## 📊 Database Management

### Backup Database

```bash
mysqldump -u username -p online_banking > backup_$(date +%Y%m%d).sql
```

### Restore Database

```bash
mysql -u username -p online_banking < backup_20240101.sql
```

### Via phpMyAdmin

- Export: Database → Export → Go
- Import: Database → Import → Choose File → Go

## 🚀 Performance Optimization

1. **Enable Caching**
   - Use OPcache for PHP (edit php.ini)
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

## 📞 Support

For issues, questions, or feature requests:
- Check the documentation first
- Review common issues in Troubleshooting section
- Contact your hosting provider for server-related issues

## 📄 License

This project is provided as-is for educational and commercial use.

## 🙏 Credits

- Font Awesome for icons
- Google Fonts for typography
- Open source PHP community

---

**Important**: This is a banking application handling sensitive financial data. Ensure you have proper security measures, regular backups, and comply with all applicable regulations before deploying to production.
