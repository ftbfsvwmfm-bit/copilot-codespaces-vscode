# SIMS Installation & Configuration Guide

## Complete System Installation

This guide provides detailed instructions for installing and configuring the Student Information Management System (SIMS) from scratch.

## Part 1: System Requirements

### Minimum Requirements
- **OS**: Linux (Ubuntu 18.04+), Windows 7+, macOS 10.12+
- **Web Server**: Apache 2.4+ or Nginx 1.16+
- **PHP**: 7.4+ (php-cli, php-mysql, php-gd extensions)
- **Database**: MySQL 5.7+ or MariaDB 10.1+
- **RAM**: 512 MB minimum (1 GB recommended)
- **Storage**: 500 MB minimum
- **Connection**: Internet for updates

### Recommended Setup
- Ubuntu 20.04 LTS Server
- Apache 2.4 with PHP 8.0
- MySQL 8.0
- 2GB RAM
- 10GB Storage

## Part 2: Installation Steps

### 2.1 Linux/Ubuntu Installation

#### Step 1: Update System
```bash
sudo apt update
sudo apt upgrade -y
```

#### Step 2: Install Apache, PHP, MySQL
```bash
# Install Apache
sudo apt install -y apache2 apache2-utils

# Install PHP and extensions
sudo apt install -y php php-cli php-mysql php-gd php-xml php-json php-curl

# Install MySQL
sudo apt install -y mysql-server mysql-client

# Enable Apache modules
sudo a2enmod rewrite
sudo a2enmod headers
```

#### Step 3: Configure Apache Virtual Host
```bash
# Create configuration file
sudo nano /etc/apache2/sites-available/sims.conf
```

Add the following content:
```apache
<VirtualHost *:80>
    ServerName sims.local
    ServerAlias www.sims.local
    DocumentRoot /var/www/sims

    <Directory /var/www/sims>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/sims_error.log
    CustomLog ${APACHE_LOG_DIR}/sims_access.log combined
</VirtualHost>
```

Then enable and restart:
```bash
sudo a2ensite sims.conf
sudo systemctl restart apache2
```

#### Step 4: Configure PHP
```bash
# Edit PHP configuration
sudo nano /etc/php/8.0/apache2/php.ini

# Important settings:
# upload_max_filesize = 50M
# post_max_size = 50M
# max_execution_time = 300
```

#### Step 5: Set File Permissions
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/sims

# Set permissions
sudo chmod -R 755 /var/www/sims
sudo chmod -R 775 /var/www/sims/uploads
```

### 2.2 Windows Installation (XAMPP)

#### Step 1: Download XAMPP
- Visit https://www.apachefriends.org/
- Download XAMPP for Windows
- Run installer and follow prompts
- Default installation path: C:\xampp

#### Step 2: Extract SIMS
```
Extract sims folder to: C:\xampp\htdocs\sims
```

#### Step 3: Start XAMPP Services
- Open XAMPP Control Panel
- Start Apache
- Start MySQL

#### Step 4: Access via Browser
```
http://localhost/sims/
```

### 2.3 macOS Installation

#### Using Homebrew:
```bash
# Install Homebrew first if not installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install required packages
brew install apache2
brew install php
brew install mysql

# Start services
brew services start apache2
brew services start mysql
```

## Part 3: Database Setup

### Step 1: Access MySQL
```bash
# Linux/macOS
mysql -u root -p

# Windows (XAMPP)
Use phpMyAdmin: http://localhost/phpmyadmin
```

### Step 2: Create Database and User
```sql
-- Create database
CREATE DATABASE sims;

-- Create user and grant privileges
CREATE USER 'sims_user'@'localhost' IDENTIFIED BY 'strongpassword123';
GRANT ALL PRIVILEGES ON sims.* TO 'sims_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 3: Import Schema
```bash
# From command line
mysql -u sims_user -p sims < /path/to/sims/db/sims.sql

# Or using phpMyAdmin
# 1. Select 'sims' database
# 2. Click Import tab
# 3. Select db/sims.sql
# 4. Click Go
```

### Step 4: Verify Installation
```sql
USE sims;
SHOW TABLES;
```

Should display 18+ tables including: users, students, faculty, courses, subjects, etc.

## Part 4: Configuration

### Step 1: Update Database Credentials
Edit `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'sims_user');
define('DB_PASS', 'strongpassword123');
define('DB_NAME', 'sims');
define('SITE_URL', 'http://localhost/sims/');
```

### Step 2: Set Correct File Permissions
```bash
# Linux
chmod 755 /var/www/html/sims
chmod 644 /var/www/html/sims/*.php
chmod -R 755 /var/www/html/sims/assets
chmod -R 755 /var/www/html/sims/admin
chmod -R 755 /var/www/html/sims/pages
chmod -R 755 /var/www/html/sims/includes
chmod -R 755 /var/www/html/sims/db
```

### Step 3: Configure Hosts File
Edit hosts file to enable local domain:

**Linux/macOS**: `/etc/hosts`
```
127.0.0.1  sims.local
127.0.0.1  www.sims.local
```

**Windows**: `C:\Windows\System32\drivers\etc\hosts`
```
127.0.0.1  sims.local
127.0.0.1  www.sims.local
```

### Step 4: Create .htaccess (for Apache)
File: `/var/www/html/sims/.htaccess`

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /sims/
    
    # Redirect HTTP to HTTPS (if SSL enabled)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# Hide sensitive files
<FilesMatch "^(config|.env|database)">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
</IfModule>
```

## Part 5: SSL Configuration (HTTPS)

### Using Let's Encrypt (Linux)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache

# Generate certificate
sudo certbot certonly --apache -d sims.local -d www.sims.local

# Auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

### Self-Signed Certificate (Testing)

```bash
# Generate self-signed certificate
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/sims.key \
    -out /etc/ssl/certs/sims.crt

# Update Apache configuration for SSL
sudo nano /etc/apache2/sites-available/sims-ssl.conf
```

## Part 6: Initial Setup & Testing

### Step 1: Verify Installation
```bash
# Check PHP version and extensions
php -v
php -m | grep mysql

# Check Apache status
sudo systemctl status apache2
sudo systemctl status mysql
```

### Step 2: Access Application
1. Open browser: http://localhost/sims/
2. Should see home page
3. Click Login

### Step 3: Default Credentials
```
Username: admin
Password: admin123
```

### Step 4: Post-Installation Tasks
1. ✓ Change admin password
2. ✓ Add college details
3. ✓ Create sample data
4. ✓ Test all user roles
5. ✓ Schedule database backups

## Part 7: Backup & Recovery

### Automated Backup Script

Create `backup.sh`:
```bash
#!/bin/bash

BACKUP_DIR="/home/backup/sims"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/sims_backup_$DATE.sql"

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u sims_user -p'strongpassword123' sims > $BACKUP_FILE

# Compress backup
gzip $BACKUP_FILE

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $BACKUP_FILE.gz"
```

### Schedule Backup (Cron)
```bash
# Edit crontab
crontab -e

# Add this line for daily 2 AM backup
0 2 * * * /home/backup/backup.sh
```

## Part 8: Performance Optimization

### MySQL Optimization
```sql
-- Run weekly
OPTIMIZE TABLE users;
OPTIMIZE TABLE students;
OPTIMIZE TABLE enrollments;
ANALYZE TABLE internal_marks;
```

### PHP Configuration
```php
// Edit includes/config.php
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
```

### Web Server Optimization
```apache
# In httpd.conf or site config
<IfModule mpm_prefork_module>
    StartServers 8
    MinSpareServers 5
    MaxSpareServers 20
    MaxRequestWorkers 256
    MaxConnectionsPerChild 0
</IfModule>
```

## Part 9: Security Hardening

### File and Directory Permissions
```bash
# Find and remove world-readable passwords
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 includes/config.php
```

### Create Admin Panel Protection
```apache
# Add to .htaccess
<Directory /var/www/html/sims/admin>
    AuthType Basic
    AuthName "Admin Area"
    AuthUserFile /var/www/.admin_users
    Require user @admin
</Directory>
```

### Database Password Protection
```bash
# Create .my.cnf for secure MySQL access
nano ~/.my.cnf

[client]
user=sims_user
password=strongpassword123

chmod 600 ~/.my.cnf
```

## Part 10: Troubleshooting

### Connection Errors
```bash
# Check MySQL connection
mysql -u sims_user -pstrongpassword123 -h localhost

# Check Apache modules
apache2ctl -M | grep rewrite
apache2ctl -M | grep php
```

### Permission Issues
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/html/sims

# Fix permissions
sudo find /var/www/html/sims -type f -exec chmod 644 {} \;
sudo find /var/www/html/sims -type d -exec chmod 755 {} \;
```

### Database Issues
```sql
-- Check table status
CHECK TABLE users;
REPAIR TABLE users;
OPTIMIZE TABLE users;

-- Clear cache if needed
FLUSH QUERY CACHE;
FLUSH PRIVILEGES;
```

## Part 11: Maintenance Schedule

### Daily
- Check error logs
- Monitor application performance
- Review critical logs

### Weekly
- Database optimization
- Clean temporary files
- Verify backup completion

### Monthly
- Update PHP/MySQL packages
- Security patches
- Full system backup
- User activity audit

### Quarterly
- Security penetration test
- Performance review
- Database maintenance
- Disaster recovery test

### Annually
- Full system upgrade
- Capacity planning
- Documentation update
- Staff training

## Support & Resources

- **Documentation**: See README.md
- **Error Logs**: `/var/log/apache2/error.log`
- **System Logs**: `journalctl -u apache2`
- **Database Logs**: `/var/log/mysql/error.log`
- **PHP Errors**: Configured in php.ini

## Version History

- **v1.0.0** - Initial Release (May 2024)
  - Basic CRUD operations
  - User authentication
  - Role-based access control
  - Responsive design

---

**Last Updated**: May 2024
**For Support**: Refer to README.md or project documentation
