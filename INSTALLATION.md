# Queue Management System - Installation Guide

## Quick Start Guide

### Step 1: Prerequisites Check
Before installing, ensure you have:
- PHP 7.4 or higher
- MySQL 5.7 or higher
- A web server (Apache with mod_rewrite or Nginx)
- Composer (optional, for PDF support)

### Step 2: Clone or Download
```bash
cd /var/www/html
git clone https://github.com/yourusername/Queue-Management-System.git
# OR download and extract the ZIP file
```

### Step 3: Create Database

#### Option A: Using MySQL Command Line
```bash
mysql -u root -p
CREATE DATABASE queue_management_system;
USE queue_management_system;
SOURCE /path/to/database/schema.sql;
EXIT;
```

#### Option B: Using phpMyAdmin
1. Open phpMyAdmin
2. Click "New"
3. Enter database name: `queue_management_system`
4. Select charset: `utf8mb4_unicode_ci`
5. Click "Create"
6. Go to "Import" tab
7. Select `database/schema.sql` file
8. Click "Import"

### Step 4: Configure Database

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');      // Your host
define('DB_USER', 'root');           // Your username
define('DB_PASSWORD', 'password');   // Your password
define('DB_NAME', 'queue_management_system');
```

### Step 5: Set File Permissions (Linux/Mac)

```bash
cd /path/to/Queue-Management-System

# Set ownership
sudo chown -R www-data:www-data .
# OR
sudo chown -R _www:_www .           # For macOS

# Set permissions
chmod -R 755 .
chmod -R 775 assets/
chmod -R 775 uploads/
```

### Step 6: Configure Web Server

#### Apache (with .htaccess)
Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

The `.htaccess` file is already included in the project root.

#### Nginx
Add to your server block:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/Queue-Management-System;

    index index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\. {
        deny all;
    }
}
```

### Step 7: Install Optional Dependencies

#### For PDF Report Generation
```bash
cd /path/to/Queue-Management-System
composer require tecnickcom/tcpdf
```

Or manually:
1. Download TCPDF from https://tcpdf.org/
2. Extract to `vendor/` directory

### Step 8: Access Your Installation

Open your browser and go to:
```
http://localhost/Queue-Management-System/index.php
```

### Step 9: Login with Default Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin123`

**Staff Accounts:**
- Username: `staff1`, `staff2`, or `staff3`
- Password: `staff123`

## Detailed Installation on Different Servers

### Installation on Apache (Ubuntu/Debian)

```bash
# 1. Install required packages
sudo apt-get update
sudo apt-get install php php-mysql php-mbstring apache2

# 2. Clone repository
cd /var/www/html
sudo git clone https://github.com/yourusername/Queue-Management-System.git
cd Queue-Management-System

# 3. Create database
mysql -u root -p < database/schema.sql

# 4. Configure database
nano config/database.php
# Update DB credentials

# 5. Set permissions
sudo chown -R www-data:www-data .
chmod -R 755 .

# 6. Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# 7. Access
# http://localhost/Queue-Management-System/index.php
```

### Installation on Nginx (Ubuntu/Debian)

```bash
# 1. Install required packages
sudo apt-get install php-fpm php-mysql php-mbstring nginx

# 2. Clone repository
cd /var/www
sudo git clone https://github.com/yourusername/Queue-Management-System.git
cd Queue-Management-System

# 3. Create database
mysql -u root -p < database/schema.sql

# 4. Configure nginx
sudo nano /etc/nginx/sites-available/queue-system
# Add configuration block (see above)

sudo ln -s /etc/nginx/sites-available/queue-system /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

# 5. Set permissions
sudo chown -R www-data:www-data .
chmod 755 .
chmod 775 assets/

# 6. Start PHP-FPM
sudo systemctl start php7.4-fpm

# 7. Access
# http://yourdomain.com/index.php
```

### Installation on Windows (XAMPP)

```
1. Download and install XAMPP from https://www.apachefriends.org/
2. Extract Queue-Management-System to C:\xampp\htdocs\queue-system
3. Start Apache and MySQL from XAMPP Control Panel
4. Open phpMyAdmin: http://localhost/phpmyadmin
5. Create database: queue_management_system
6. Import database/schema.sql
7. Edit config/database.php with correct credentials
8. Access: http://localhost/queue-system/index.php
```

### Installation on Docker

Create `docker-compose.yml`:

```yaml
version: '3.8'
services:
  web:
    image: php:7.4-apache
    ports:
      - "80:80"
    volumes:
      - .:/var/www/html
    environment:
      - MYSQL_HOST=db
      - MYSQL_DATABASE=queue_management_system
      - MYSQL_USER=root
      - MYSQL_PASSWORD=root

  db:
    image: mysql:5.7
    environment:
      - MYSQL_ROOT_PASSWORD=root
      - MYSQL_DATABASE=queue_management_system
    volumes:
      - db_data:/var/lib/mysql
      - ./database/schema.sql:/docker-entrypoint-initdb.d/schema.sql

volumes:
  db_data:
```

Run:
```bash
docker-compose up -d
# Access at http://localhost
```

## Troubleshooting

### Problem: "Connection failed: Connection refused"
**Solution:**
- Check MySQL is running
- Verify credentials in `config/database.php`
- Check database exists

### Problem: "Access Denied" on file upload
**Solution:**
```bash
chmod -R 777 assets/
chmod -R 777 uploads/
```

### Problem: "No input file specified" (Nginx)
**Solution:**
- Check `fastcgi_param SCRIPT_FILENAME` in nginx config
- Restart PHP-FPM and Nginx

### Problem: Sound files not playing
**Solution:**
- Check files exist in `assets/sounds/`
- Verify browser permissions for audio
- Check browser console for errors

### Problem: PDF reports not generating
**Solution:**
- Install TCPDF: `composer require tecnickcom/tcpdf`
- Check write permissions on `/assets/reports/`
- Verify PHP memory_limit is sufficient

### Problem: Session timeout issues
**Solution:**
- Check `php.ini` settings
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 0
```

## After Installation

### Recommended Post-Installation Steps

1. **Change Default Passwords**
   - Login as admin
   - Go to Settings → Change Password

2. **Customize System Settings**
   - Set system name
   - Configure refresh intervals
   - Enable/disable sound

3. **Create Services**
   - Add your service categories
   - Assign staff to services

4. **Configure Counters**
   - Create physical counters
   - Assign staff members

5. **Set Up SSL/HTTPS** (Important for production)
   ```bash
   # Using Let's Encrypt
   sudo certbot --apache -d yourdomain.com
   # OR for Nginx
   sudo certbot --nginx -d yourdomain.com
   ```

6. **Enable Backups**
   - Set up automated database backups
   ```bash
   # Create backup script
   sudo nano /usr/local/bin/backup-queue.sh
   # Add to cron
   sudo crontab -e
   # Add line: 0 2 * * * /usr/local/bin/backup-queue.sh
   ```

## Security Recommendations

### 1. Change Default Credentials
Change all default passwords immediately.

### 2. Create Admin User
```php
// In database
INSERT INTO users VALUES (NULL, 'newadmin', PASSWORD_HASH, 'email@example.com', 'admin', 'Your Name', 'active', NOW(), NOW());
```

### 3. SSL Certificate
Install and configure HTTPS

### 4. Backup Database Regularly
```bash
mysqldump -u root -p queue_management_system > backup_$(date +%Y%m%d).sql
```

### 5. Update Dependencies
```bash
composer update
```

### 6. Monitor Logs
```bash
tail -f /var/log/apache2/error.log
tail -f /var/log/php-fpm.log
```

## Performance Optimization

### 1. Database Optimization
```sql
OPTIMIZE TABLE queue;
OPTIMIZE TABLE users;
OPTIMIZE TABLE staff_activity;
```

### 2. Enable Caching
Add to `.htaccess`:
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

### 3. PHP Configuration
Edit `php.ini`:
```
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 100M
post_max_size = 100M
```

## Support & Help

For installation issues:
1. Check this guide
2. Review error logs
3. Check PHP error_log
4. Enable debug mode

## Next Steps

After successful installation:
1. Review [README.md](README.md) for feature documentation
2. Explore Admin Dashboard
3. Create staff members
4. Configure services and counters
5. Test queue operations
4. Set up display screens
5. Train staff on the system

---

**Installation Complete!** Your Queue Management System is ready to use.
