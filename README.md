# La TRUNG Printing & Packaging Website

A production-ready, secure, and professional corporate website for La TRUNG Printing & Packaging company.

## Overview

This website showcases La TRUNG's premium offset printing and packaging services, emphasizing reliability, international quality standards, and partnerships with global corporations.

## Features

### Pages
- **Home (index.php)** - Hero section, services overview, client showcase, and company statistics
- **About (about.php)** - Company history, values, certifications, and quality commitment
- **Services (services.php)** - Detailed presentation of all services and production capabilities
- **Contact (contact.php)** - Fully functional contact form with database integration
- **Error Pages** - Custom 404 and 500 error pages

### Design Highlights
- Modern, minimalistic design with clean typography
- Turquoise (#2DBAA7) and dark gray color scheme matching brand identity
- Fully responsive layout for desktop, tablet, and mobile devices
- Professional animations and hover effects
- Bilingual support (English & Vietnamese)
- Optimized for corporate audiences

### Security Features ✓
- **CSRF Protection** - Token-based form protection
- **XSS Prevention** - All outputs sanitized with htmlspecialchars
- **SQL Injection Protection** - PDO prepared statements
- **Secure Sessions** - HttpOnly, Secure, and SameSite flags
- **Rate Limiting** - Prevents spam and abuse
- **Honeypot Fields** - Additional spam protection
- **Session Hijacking Prevention** - User agent and IP validation
- **HTTPS Enforcement** - Automatic redirect to HTTPS
- **Security Headers** - X-Frame-Options, X-Content-Type-Options, X-XSS-Protection
- **Email Header Injection Prevention** - Input sanitization

### Production Features ✓
- **Database Integration** - MySQL/MariaDB with PDO
- **Email Notifications** - Admin alerts and auto-reply to customers
- **Comprehensive Logging** - Application, security, email, and database logs
- **Error Handling** - Graceful error pages and logging
- **Environment Configuration** - .env file for deployment settings
- **Form Validation** - Client and server-side validation
- **SEO Optimized** - robots.txt, sitemap.xml, meta tags
- **Performance** - Gzip compression, browser caching, optimized assets

## Installation & Setup

### Requirements
- PHP 7.4 or higher (8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.2+
- Apache with mod_rewrite OR Nginx
- SSL certificate (for production)

### Local Development

1. **Navigate to the project directory:**
   ```bash
   cd "/Users/anla/Downloads/latrung website"
   ```

2. **Create environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Configure database** (for testing contact form):
   - Create a MySQL database
   - Import `database.sql`
   - Update `.env` with database credentials

4. **Start PHP development server:**
   ```bash
   php -S localhost:8000
   ```

5. **Open in browser:**
   ```
   http://localhost:8000
   ```

### Production Deployment

**See [DEPLOYMENT.md](DEPLOYMENT.md) for complete deployment instructions.**

Quick steps:
1. Upload all files to your web hosting server
2. Create `.env` file from `.env.example`
3. Configure database credentials in `.env`
4. Import `database.sql` to your MySQL database
5. Set file permissions (644 for files, 755 for directories)
6. Ensure `.env` file is protected (chmod 600)
7. Install SSL certificate
8. Test all functionality

## File Structure

```
latrung website/
├── index.php                 # Homepage
├── about.php                 # About page
├── services.php              # Services page
├── contact.php               # Contact page with form
├── 404.php                   # Custom 404 error page
├── 500.php                   # Custom 500 error page
├── config.php                # Application configuration
├── .env.example              # Environment configuration template
├── .env                      # Environment variables (DO NOT COMMIT)
├── .htaccess                 # Apache configuration
├── database.sql              # Database schema
├── robots.txt                # SEO robots file
├── sitemap.xml               # SEO sitemap
├── DEPLOYMENT.md             # Deployment guide
├── includes/
│   ├── header.php            # Navigation header
│   ├── footer.php            # Footer
│   ├── language.php          # Bilingual support
│   ├── session.php           # Secure session handling
│   ├── csrf.php              # CSRF protection
│   ├── database.php          # Database connection
│   ├── mailer.php            # Email functionality
│   ├── ratelimit.php         # Rate limiting
│   └── logger.php            # Logging system
├── lang/
│   ├── en.php                # English translations
│   └── vi.php                # Vietnamese translations
├── css/
│   └── styles.css            # Main stylesheet
├── assets/
│   └── [images]              # Company images and icons
└── logs/                     # Application logs (auto-created)
```

## Security

### Protected Files
The following files are protected and cannot be accessed via browser:
- `.env` - Environment configuration
- `.htaccess` - Server configuration
- `config.php` - Application configuration
- `database.sql` - Database schema
- `/includes/*` - All include files
- `/logs/*` - All log files

### Important Security Notes
1. **NEVER commit .env to version control**
2. Always use HTTPS in production
3. Keep PHP and MySQL updated
4. Use strong database passwords
5. Regular security audits
6. Monitor logs regularly
7. Keep backups

## Database

The website uses MySQL/MariaDB with the following tables:

- **contact_submissions** - Stores contact form submissions
- **site_settings** - Application settings
- **admin_users** - Admin user accounts (for future admin panel)

## Email Configuration

The contact form supports two email methods:

1. **PHP mail()** - Default, works with most hosting
2. **SMTP** - Configure in `.env` for better deliverability

Configure in `.env`:
```
MAIL_FROM_EMAIL=info@latrungprint.vn
MAIL_TO_EMAIL=info@latrungprint.vn
```

For SMTP:
```
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=your_username
SMTP_PASS=your_password
SMTP_ENCRYPTION=tls
```

## Logging

Logs are stored in `/logs/` directory:

- `application.log` - General application events
- `security.log` - Security events (failed CSRF, rate limits)
- `database.log` - Database queries and errors
- `email.log` - Email sending activity
- `submissions.log` - Form submissions
- `php-errors.log` - PHP errors

View logs:
```bash
tail -f logs/submissions.log
tail -f logs/security.log
```

## Rate Limiting

Contact form submissions are rate-limited to prevent spam:
- **Default**: 3 submissions per hour per IP address
- Configure in `.env`:
  ```
  RATE_LIMIT_MAX_ATTEMPTS=3
  RATE_LIMIT_WINDOW=3600
  ```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

The website includes:
- Gzip compression (via .htaccess)
- Browser caching (via .htaccess)
- Optimized CSS
- Lazy loading ready
- Minimal dependencies
- Mobile-first responsive design

## SEO Features

- Semantic HTML5 markup
- Meta descriptions on all pages
- Proper heading hierarchy
- Alt text for images
- Clean URL structure
- robots.txt
- sitemap.xml
- Bilingual support with hreflang
- OpenGraph ready

## Maintenance

### Daily
- Monitor error logs
- Check form submissions

### Weekly
- Review security logs
- Verify backups

### Monthly
- Clean old logs
- Optimize database
- Update dependencies

## Troubleshooting

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed troubleshooting guide.

Common issues:
- **White page**: Check PHP error logs
- **Database error**: Verify credentials in `.env`
- **Email not sending**: Check logs/email.log
- **CSRF error**: Clear cookies and try again
- **Rate limit**: Wait for the window to expire

## Version History

### Version 1.0.0 (2024-12-24)
- Initial production-ready release
- Full security implementation
- Database integration
- Email functionality
- Comprehensive logging
- Rate limiting
- Error handling
- SEO optimization

## Support

For deployment assistance, see [DEPLOYMENT.md](DEPLOYMENT.md)

## License

Proprietary - La TRUNG Printing & Packaging

---

**Production Ready**: ✓ Yes
**Last Updated**: 2024-12-24
**Version**: 1.0.0
