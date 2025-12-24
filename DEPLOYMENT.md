# La TRUNG Website - Deployment Guide

## Table of Contents
1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Requirements](#server-requirements)
3. [Database Setup](#database-setup)
4. [Environment Configuration](#environment-configuration)
5. [File Upload](#file-upload)
6. [SSL Certificate Setup](#ssl-certificate-setup)
7. [Post-Deployment Steps](#post-deployment-steps)
8. [Testing](#testing)
9. [Maintenance](#maintenance)
10. [Troubleshooting](#troubleshooting)

---

## Pre-Deployment Checklist

Before deploying to production, ensure you have:

- [ ] Domain name registered and DNS configured
- [ ] Web hosting account with PHP and MySQL support
- [ ] SSL certificate (Let's Encrypt or purchased)
- [ ] Database credentials from your hosting provider
- [ ] Email account configured (for contact form)
- [ ] All files backed up locally

---

## Server Requirements

### Minimum Requirements
- **PHP**: 7.4 or higher (8.0+ recommended)
- **MySQL**: 5.7 or higher (8.0+ recommended) OR MariaDB 10.2+
- **Web Server**: Apache 2.4+ with mod_rewrite enabled OR Nginx
- **Disk Space**: 100MB minimum
- **Memory**: 128MB minimum PHP memory_limit

### Required PHP Extensions
```
- pdo_mysql
- mbstring
- session
- json
- openssl
```

### Apache Modules (Required)
```
- mod_rewrite
- mod_deflate (optional, for compression)
- mod_expires (optional, for caching)
- mod_headers
```

---

## Database Setup

### Step 1: Create Database

Login to your hosting control panel (cPanel/Plesk) or use phpMyAdmin:

```sql
CREATE DATABASE latrung_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Create Database User

```sql
CREATE USER 'latrung_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON latrung_website.* TO 'latrung_user'@'localhost';
FLUSH PRIVILEGES;
```

**Security Note**: Replace `YOUR_STRONG_PASSWORD` with a strong password (at least 16 characters, mixed case, numbers, symbols).

### Step 3: Import Database Schema

Upload and run the `database.sql` file:

```bash
# Via command line
mysql -u latrung_user -p latrung_website < database.sql

# OR use phpMyAdmin:
# 1. Select your database
# 2. Click "Import" tab
# 3. Choose database.sql file
# 4. Click "Go"
```

---

## Environment Configuration

### Step 1: Create .env File

Copy the example environment file:

```bash
cp .env.example .env
```

### Step 2: Configure .env File

Edit `.env` with your actual production values:

```bash
# Environment
APP_ENV=production
APP_DEBUG=false

# Database
DB_HOST=localhost
DB_NAME=latrung_website
DB_USER=latrung_user
DB_PASS=YOUR_DATABASE_PASSWORD

# Email
MAIL_FROM_EMAIL=info@latrungprint.vn
MAIL_FROM_NAME="La TRUNG Printing & Packaging"
MAIL_TO_EMAIL=info@latrungprint.vn
MAIL_ADMIN_EMAIL=admin@latrungprint.vn

# SMTP (Optional - leave empty to use PHP mail())
SMTP_HOST=
SMTP_PORT=587
SMTP_USER=
SMTP_PASS=
SMTP_ENCRYPTION=tls

# Security
SESSION_SECURE=true
SESSION_HTTPONLY=true
SESSION_SAMESITE=Strict

# Site Configuration
SITE_URL=https://www.latrungprint.vn
SITE_NAME="La TRUNG Printing & Packaging"

# Logging
LOG_ENABLED=true
LOG_PATH=logs/
LOG_LEVEL=error
```

### Step 3: Secure the .env File

**CRITICAL**: Ensure `.env` is NOT accessible via web browser:

```bash
chmod 600 .env
```

The `.htaccess` file already includes protection, but verify:

```apache
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## File Upload

### Option 1: FTP/SFTP Upload

1. Connect to your server via FTP/SFTP client (FileZilla, Cyberduck, etc.)
2. Upload all files to your web root directory (usually `public_html`, `www`, or `htdocs`)
3. Preserve file structure exactly as is

### Option 2: Git Deployment (Recommended)

```bash
# On your server
cd /path/to/webroot
git clone <your-repository-url> .

# Install/update (if using)
composer install --no-dev --optimize-autoloader
```

### File Permissions

Set correct permissions:

```bash
# Make sure web server can read files
chmod 644 *.php
chmod 644 config.php
chmod 755 includes/
chmod 755 assets/
chmod 755 css/

# Create and secure logs directory
mkdir -p logs
chmod 755 logs
chown www-data:www-data logs  # Replace www-data with your web server user

# Protect sensitive files
chmod 600 .env
chmod 644 .htaccess
```

---

## SSL Certificate Setup

### Option 1: Let's Encrypt (Free)

Most hosting providers have one-click Let's Encrypt installation. If not:

```bash
# Using Certbot
sudo certbot --apache -d www.latrungprint.vn -d latrungprint.vn
```

### Option 2: Purchased SSL Certificate

1. Upload your certificate files to the server
2. Configure in your hosting control panel OR
3. Configure in Apache/Nginx config

### Verify HTTPS

After SSL is installed:

1. Visit `https://www.latrungprint.vn`
2. Check for the padlock icon in browser
3. Verify HTTPS redirect is working (HTTP should redirect to HTTPS)

---

## Post-Deployment Steps

### 1. Test Database Connection

Create a test file (then delete it):

```php
<?php
require_once 'config.php';
require_once 'includes/database.php';

try {
    $db = db();
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
```

### 2. Update sitemap.xml

Edit `sitemap.xml` and replace `www.latrungprint.vn` with your actual domain if different.

### 3. Submit to Search Engines

```bash
# Google Search Console
https://search.google.com/search-console

# Bing Webmaster Tools
https://www.bing.com/webmasters

# Submit sitemap:
https://www.latrungprint.vn/sitemap.xml
```

### 4. Set Up Monitoring

Configure monitoring for:
- Website uptime
- SSL certificate expiration
- Error logs
- Form submissions

### 5. Configure Email

Test the contact form to ensure emails are being sent/received properly.

If emails aren't working, consider using SMTP with services like:
- SendGrid
- Mailgun
- Amazon SES
- Your hosting provider's SMTP

Update `.env` with SMTP credentials:

```bash
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_USER=apikey
SMTP_PASS=your_sendgrid_api_key
SMTP_ENCRYPTION=tls
```

### 6. Set Up Backup

Configure automatic backups:

**Database Backup** (cron job):
```bash
# Add to crontab (crontab -e)
0 2 * * * mysqldump -u latrung_user -p'PASSWORD' latrung_website > /path/to/backups/db_$(date +\%Y\%m\%d).sql
```

**File Backup**:
```bash
# Weekly file backup
0 3 * * 0 tar -czf /path/to/backups/files_$(date +\%Y\%m\%d).tar.gz /path/to/webroot
```

### 7. Configure Log Rotation

Create `/etc/logrotate.d/latrung-website`:

```
/path/to/webroot/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
}
```

---

## Testing

### Critical Tests

After deployment, test all these:

- [ ] Homepage loads correctly (index.php)
- [ ] About page loads (about.php)
- [ ] Services page loads (services.php)
- [ ] Contact page loads (contact.php)
- [ ] Contact form submission works
- [ ] Email notifications received
- [ ] Database saving form submissions
- [ ] Language switcher (EN/VI) works
- [ ] HTTPS redirect works (HTTP → HTTPS)
- [ ] Mobile responsive design
- [ ] 404 error page works
- [ ] robots.txt accessible
- [ ] sitemap.xml accessible

### Security Tests

- [ ] `.env` file is NOT accessible via browser
- [ ] `config.php` cannot be accessed directly
- [ ] `/includes/` directory not browseable
- [ ] `/logs/` directory not browseable
- [ ] CSRF protection working on forms
- [ ] Rate limiting working (try submitting form 4+ times)
- [ ] Session security enabled
- [ ] SQL injection protection (PDO prepared statements)
- [ ] XSS protection (htmlspecialchars)

### Performance Tests

Run these tools:
- Google PageSpeed Insights
- GTmetrix
- WebPageTest

---

## Maintenance

### Regular Tasks

**Daily:**
- Monitor error logs
- Check form submissions in database

**Weekly:**
- Review security logs
- Check backup integrity
- Monitor disk space

**Monthly:**
- Update PHP/MySQL if needed
- Review and archive old submissions
- Check SSL certificate expiration
- Review and optimize database

**Quarterly:**
- Review and update content
- Audit security settings
- Test disaster recovery procedure

### Monitoring Log Files

```bash
# View latest errors
tail -f logs/php-errors.log

# Check form submissions
tail -f logs/submissions.log

# Security events
tail -f logs/security.log

# Email logs
tail -f logs/email.log
```

### Managing Form Submissions

Access database to view submissions:

```sql
-- View recent submissions
SELECT * FROM contact_submissions ORDER BY submitted_at DESC LIMIT 10;

-- Count submissions by status
SELECT status, COUNT(*) as count FROM contact_submissions GROUP BY status;

-- Update submission status
UPDATE contact_submissions SET status = 'completed' WHERE id = 123;
```

---

## Troubleshooting

### Common Issues

#### 1. White Page / 500 Error

**Solution:**
```bash
# Check PHP error log
tail -f /var/log/apache2/error.log

# Enable display_errors temporarily (NEVER in production!)
# Edit php.ini or .htaccess
php_value display_errors 1
```

#### 2. Database Connection Failed

**Solution:**
- Verify database credentials in `.env`
- Check if database user has permissions
- Verify MySQL service is running
- Check firewall rules

```bash
# Test database connection
mysql -u latrung_user -p -h localhost latrung_website
```

#### 3. Contact Form Not Sending Emails

**Solution:**
- Check logs/email.log
- Verify MAIL_TO_EMAIL in .env
- Test PHP mail() function
- Consider using SMTP instead

```php
// Test mail function
<?php
mail('your@email.com', 'Test', 'This is a test email');
?>
```

#### 4. HTTPS Redirect Loop

**Solution:**
Check if your hosting uses a proxy/load balancer:

```apache
# Add to .htaccess before HTTPS redirect
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteCond %{HTTPS} off
```

#### 5. Rate Limiting Too Strict

**Solution:**
Adjust in `.env`:

```bash
RATE_LIMIT_MAX_ATTEMPTS=5
RATE_LIMIT_WINDOW=3600
```

#### 6. Session Issues

**Solution:**
```bash
# Check session directory permissions
ls -ld /var/lib/php/sessions

# Verify session.save_path in php.ini
php -i | grep session.save_path
```

### Getting Help

If you encounter issues:

1. Check `logs/` directory for error details
2. Enable debug mode temporarily (set `APP_DEBUG=true` in .env)
3. Review this deployment guide
4. Check PHP error logs
5. Contact your hosting provider's support

---

## Security Best Practices

1. **Never commit .env to version control**
2. **Keep software updated** (PHP, MySQL, libraries)
3. **Use strong passwords** (database, admin accounts)
4. **Regular backups** (database + files)
5. **Monitor logs** regularly
6. **Limit file permissions** (644 for files, 755 for directories)
7. **Keep admin tools separate** (consider separate admin subdomain)
8. **Use HTTPS only**
9. **Regular security audits**
10. **Keep database credentials secure**

---

## Performance Optimization

### Enable OPcache

Add to php.ini:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### Enable Compression

Already enabled in `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

### Enable Browser Caching

Already configured in `.htaccess`:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
</IfModule>
```

### Database Optimization

```sql
-- Optimize tables monthly
OPTIMIZE TABLE contact_submissions;

-- Add indexes if needed
CREATE INDEX idx_email ON contact_submissions(email);
CREATE INDEX idx_status ON contact_submissions(status);
CREATE INDEX idx_submitted ON contact_submissions(submitted_at);
```

---

## Changelog & Updates

Keep track of updates:

```markdown
## 2024-12-24
- Initial production deployment
- Implemented security features
- Configured email functionality
```

---

## Support

For technical support or questions about this deployment:
- Review documentation in README.md
- Check logs in `logs/` directory
- Consult your hosting provider

---

**Last Updated:** 2024-12-24
**Version:** 1.0.0
