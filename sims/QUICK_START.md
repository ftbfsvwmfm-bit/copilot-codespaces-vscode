# SIMS - Quick Start Guide

## Overview
This is a comprehensive Student Information Management System (SIMS) built with PHP, MySQL, HTML5, CSS3, and JavaScript.

## Quick Setup (5 Minutes)

### Prerequisites Check
- ✓ PHP 7.4+ installed
- ✓ MySQL 5.7+ installed
- ✓ Apache/Nginx web server running
- ✓ 500MB free disk space

### Step 1: Prepare Database
```bash
# Connect to MySQL
mysql -u root -p

# Create database (paste in MySQL shell)
SOURCE db/sims.sql;

# Exit
EXIT;
```

### Step 2: Configure Database
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'sims');
```

### Step 3: Set Permissions (Linux only)
```bash
chmod 755 /var/www/html/sims
chmod -R 755 /var/www/html/sims/assets
chmod -R 755 /var/www/html/sims/admin
chmod -R 755 /var/www/html/sims/pages
```

### Step 4: Start Using
1. Open browser: http://localhost/sims/
2. Click "Login"
3. Use test credentials (see README.md)

## File Structure Purpose

```
sims/
├── login.php              → User login page
├── register.php           → New user registration
├── index.php              → Home page
├── db/
│   └── sims.sql          → Database schema (import this!)
├── includes/
│   ├── config.php        → Database & settings configuration
│   ├── auth.php          → Authentication logic
│   ├── header.php        → Page header template
│   └── footer.php        → Page footer template
├── assets/
│   ├── css/
│   │   └── style.css     → All styling
│   └── js/
│       └── validation.js → Form validation
├── admin/
│   └── *.php             → Admin pages
├── pages/
│   └── *.php             → Student/Faculty/Exam pages
└── README.md             → Full documentation
```

## Default Test Users

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Student | student001 | student123 |
| Faculty | faculty001 | faculty123 |

**⚠️ Change passwords after first login!**

## Common Issues & Solutions

### Error: "Connection refused"
**Problem:** Can't connect to MySQL
**Solution:**
```bash
# Check if MySQL is running
sudo service mysql status

# Start MySQL if not running
sudo service mysql start
```

### Error: "Access denied for user"
**Problem:** Wrong database credentials
**Solution:**
1. Check username in config.php
2. Verify MySQL user exists: `SELECT user FROM mysql.user;`
3. Create user if needed:
   ```sql
   CREATE USER 'sims_user'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON sims.* TO 'sims_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Pages showing as text
**Problem:** PHP not executing
**Solution:**
1. Check file extensions are .php
2. Enable PHP in Apache: `sudo a2enmod php7.4`
3. Restart Apache: `sudo service apache2 restart`

### Can't create new users
**Problem:** Database issues
**Solution:**
1. Verify database imported correctly
2. Check all tables exist: In MySQL: `USE sims; SHOW TABLES;`
3. Re-import database if needed

## First Steps After Installation

### 1. Change Admin Password
- Log in as admin
- Go to Profile
- Change password immediately

### 2. Add Your Institution Details
- Admin Dashboard → System Settings
- Enter college name, address, etc.

### 3. Create Test Data
- Add sample students, faculty, courses
- Set up academic calendar

### 4. Backup Database
```bash
mysqldump -u root -p sims > sims_backup.sql
```

## Important Configuration Files

| File | Purpose | Edit If |
|------|---------|---------|
| config.php | Database connection | Changing DB credentials |
| style.css | All colors/styling | Branding needs |
| validation.js | Form validation rules | Rules need changes |

## Performance Tips

### Database Optimization
```sql
-- Run weekly
OPTIMIZE TABLE users;
OPTIMIZE TABLE students;
OPTIMIZE TABLE internal_marks;
ANALYZE TABLE enrollments;
```

### Enable Caching (Apache)
Add to .htaccess:
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
</IfModule>
```

## Backup & Restore

### Backup
```bash
# Full backup
mysqldump -u root -p sims > backup_$(date +%Y%m%d).sql

# Backup with structure only
mysqldump -u root -p --no-data sims > structure_backup.sql
```

### Restore
```bash
# Restore from backup
mysql -u root -p sims < backup_20240509.sql

# Restore on different server
mysql -u user -h server.com -p sims < backup.sql
```

## Security Checklist

- [ ] Changed all default passwords
- [ ] Updated database credentials in config.php
- [ ] Set proper file permissions (755)
- [ ] Enabled HTTPS if possible
- [ ] Regular database backups scheduled
- [ ] Disabled direct file access (.htaccess)
- [ ] Updated PHP to latest version
- [ ] Configured firewall rules

## Monitoring & Maintenance

### Weekly Tasks
- Check error logs: `tail /var/log/apache2/error.log`
- Monitor disk space: `df -h`
- Database optimization: Run OPTIMIZE on large tables

### Monthly Tasks
- Review user activity logs
- Check for unused accounts
- Update system packages
- Full database backup

### Quarterly Tasks
- Update PHP and MySQL
- Security audit
- Performance review
- Data cleanup

## Support Resources

- **Admin Panel Help**: Click "?" in top right
- **Documentation**: See README.md for detailed docs
- **Error Logs**: Check /var/log/apache2/error.log
- **Database**: Use phpMyAdmin or MySQL CLI

## Getting Help

### Debug Mode
To see detailed error messages:
1. Edit includes/config.php
2. Add: `define('DEBUG_MODE', true);`
3. Errors will show in browser

### Known Limitations
- Works best with 500-10,000 users per instance
- Max upload file size: 50MB (configurable)
- Session timeout: 1 hour
- Database queries optimized for 2-5 second response time

## Next Steps

1. ✓ Familiarize with interface (15 min)
2. ✓ Add sample data (30 min)
3. ✓ Test all roles (15 min)
4. ✓ Customize branding (30 min)
5. ✓ Schedule backups (10 min)
6. ✓ Train users (varies)

## Version & Updates

**Current Version:** 1.0.0
**PHP Support:** 7.4 - 8.2
**MySQL Support:** 5.7 - 8.0
**Last Updated:** May 2024

---

**Questions?** Refer to README.md for comprehensive documentation.
