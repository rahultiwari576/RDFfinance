# Hostinger Deployment Guide for Laravel Finance Application

## Prerequisites
- Hostinger hosting account with PHP 8.1+ support
- FTP/SFTP access or File Manager access
- SSH access (if available)
- Domain name configured

---

## Step 1: Prepare Your Application for Production

### 1.1 Update .env for Production
```bash
# In your local project, update .env file:
APP_NAME="Finance Management"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database Configuration (Hostinger MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Mail Configuration (Hostinger SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your_email@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 1.2 Optimize Application
```bash
# Run these commands locally before uploading:
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 2: Database Setup on Hostinger

### 2.1 Create MySQL Database
1. Log in to Hostinger **hPanel**
2. Go to **Databases** → **MySQL Databases**
3. Click **Create New Database**
4. Enter database name (e.g., `finance_db`)
5. Create a database user with a strong password
6. Grant all privileges to the user
7. Note down:
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

### 2.2 Import Database Schema (Optional)
If you want to import existing data:
1. Export your local database:
   ```bash
   # For SQLite (local)
   sqlite3 database/database.sqlite .dump > database_backup.sql
   
   # Or use phpMyAdmin to export
   ```
2. In Hostinger hPanel:
   - Go to **Databases** → **phpMyAdmin**
   - Select your database
   - Click **Import**
   - Choose your SQL file and import

---

## Step 3: Upload Files to Hostinger

### 3.1 Using File Manager (Recommended for beginners)
1. Log in to Hostinger **hPanel**
2. Go to **Files** → **File Manager**
3. Navigate to `public_html` folder (or your domain's root folder)
4. Delete default files (index.html, etc.) if any
5. Upload your Laravel project files:
   - **Important**: Upload all files EXCEPT:
     - `.env` (create new one on server)
     - `node_modules/` folder
     - `.git/` folder
     - `storage/logs/*.log` files
     - `database/database.sqlite` (if using MySQL)

### 3.2 Using FTP/SFTP (Faster for large files)
1. Get FTP credentials from Hostinger hPanel:
   - Go to **FTP Accounts**
   - Note: Host, Username, Password, Port
2. Use FTP client (FileZilla, WinSCP, etc.):
   ```
   Host: ftp.yourdomain.com
   Username: your_ftp_username
   Password: your_ftp_password
   Port: 21 (or 22 for SFTP)
   ```
3. Connect and upload files to `public_html` folder

### 3.3 File Structure on Server
After uploading, your structure should be:
```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── ...
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── ...
```

---

## Step 4: Configure Web Server

### 4.1 Move Public Folder Contents (IMPORTANT)
Hostinger typically serves from `public_html`, so you need to:

**Option A: Point domain to public folder (Recommended)**
1. In Hostinger hPanel, go to **Domains** → **Manage**
2. Set document root to: `public_html/public`
3. Or create `.htaccess` in `public_html`:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```

**Option B: Move public folder contents**
1. Move all files from `public/` to `public_html/`
2. Update `public/index.php` paths:
   ```php
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```

### 4.2 Set File Permissions
Using File Manager or SSH:
```bash
# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public
```

Or via File Manager:
- Right-click `storage` folder → **Change Permissions** → `755`
- Right-click `bootstrap/cache` folder → **Change Permissions** → `755`

---

## Step 5: Configure Environment Variables

### 5.1 Create .env File on Server
1. In File Manager, go to `public_html` folder
2. Create new file named `.env`
3. Copy content from your local `.env` and update:
   ```env
   APP_NAME="Finance Management"
   APP_ENV=production
   APP_KEY=
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=465
   MAIL_USERNAME=your_email@yourdomain.com
   MAIL_PASSWORD=your_email_password
   MAIL_ENCRYPTION=ssl
   MAIL_FROM_ADDRESS=your_email@yourdomain.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

### 5.2 Generate Application Key
If you have SSH access:
```bash
cd public_html
php artisan key:generate
```

If no SSH access:
1. Generate key locally: `php artisan key:generate`
2. Copy the `APP_KEY` value to server's `.env`

---

## Step 6: Run Migrations and Seeders

### 6.1 Using SSH (If Available)
```bash
cd public_html
php artisan migrate --force
php artisan db:seed --force
```

### 6.2 Using Hostinger Terminal (hPanel)
1. Go to **Advanced** → **Terminal** in hPanel
2. Navigate to your project:
   ```bash
   cd public_html
   php artisan migrate --force
   php artisan db:seed --force
   ```

### 6.3 Manual Database Setup
If you can't run artisan commands:
1. Use phpMyAdmin to run migrations manually
2. Or create tables using the migration SQL files

---

## Step 7: Configure Storage Link

### 7.1 Create Storage Symbolic Link
If you have SSH/Terminal access:
```bash
php artisan storage:link
```

### 7.2 Manual Link (If no SSH)
1. In File Manager, create symbolic link:
   - Source: `public_html/storage/app/public`
   - Destination: `public_html/public/storage`
2. Or copy storage files to public folder

---

## Step 8: Set Up Cron Jobs (For Scheduled Tasks)

### 8.1 Configure Cron Job in Hostinger
1. Go to **Advanced** → **Cron Jobs** in hPanel
2. Create new cron job:
   - **Command**: `php /home/username/public_html/artisan schedule:run >> /dev/null 2>&1`
   - **Frequency**: Every minute (`* * * * *`)
   - Replace `username` with your Hostinger username

### 8.2 Alternative: Use Hostinger's Cron Job Manager
- Set command to: `cd /home/username/public_html && php artisan schedule:run`

---

## Step 9: Test Your Application

### 9.1 Test Basic Functionality
1. Visit: `https://yourdomain.com`
2. Check if homepage loads
3. Test login functionality
4. Test loan application (if admin)

### 9.2 Test Email Functionality
1. Submit a loan application
2. Check if email is sent
3. Verify SMTP settings if emails fail

### 9.3 Check Error Logs
- Check `storage/logs/laravel.log` for errors
- Check Hostinger error logs in hPanel

---

## Step 10: Security Checklist

### 10.1 Final Security Steps
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Ensure `.env` file is not publicly accessible
- [ ] Remove `phpinfo()` files if any
- [ ] Set proper file permissions (755 for folders, 644 for files)
- [ ] Enable HTTPS/SSL certificate
- [ ] Update admin password
- [ ] Remove test/dummy data if needed

---

## Troubleshooting Common Issues

### Issue 1: 500 Internal Server Error
**Solution:**
- Check `storage/logs/laravel.log`
- Verify file permissions
- Check `.env` configuration
- Ensure `APP_KEY` is set

### Issue 2: Database Connection Error
**Solution:**
- Verify database credentials in `.env`
- Check if database exists in phpMyAdmin
- Ensure database user has proper permissions

### Issue 3: CSS/JS Not Loading
**Solution:**
- Run `php artisan storage:link`
- Check `public` folder permissions
- Clear cache: `php artisan cache:clear`

### Issue 4: Email Not Sending
**Solution:**
- Verify SMTP credentials
- Check Hostinger email settings
- Test with a simple email first
- Check spam folder

### Issue 5: File Upload Not Working
**Solution:**
- Check `storage` folder permissions (755)
- Verify `upload_max_filesize` in PHP settings
- Check `post_max_size` in PHP settings

---

## Additional Hostinger-Specific Settings

### PHP Version
1. Go to **Advanced** → **PHP Configuration**
2. Select **PHP 8.1** or higher
3. Enable required extensions:
   - `fileinfo`
   - `pdo_mysql`
   - `mbstring`
   - `openssl`
   - `tokenizer`
   - `xml`
   - `ctype`
   - `json`

### Increase PHP Limits (If Needed)
In **PHP Configuration**, set:
- `upload_max_filesize = 10M`
- `post_max_size = 10M`
- `memory_limit = 256M`
- `max_execution_time = 300`

---

## Quick Deployment Checklist

- [ ] Database created in Hostinger
- [ ] Files uploaded to `public_html`
- [ ] `.env` file created and configured
- [ ] `APP_KEY` generated
- [ ] File permissions set (755 for folders)
- [ ] Migrations run
- [ ] Storage link created
- [ ] Cron job configured (if needed)
- [ ] SSL certificate enabled
- [ ] Application tested
- [ ] Email functionality tested

---

## Support Resources

- **Hostinger Support**: https://www.hostinger.com/contact
- **Laravel Documentation**: https://laravel.com/docs
- **Hostinger Knowledge Base**: https://support.hostinger.com/

---

## Notes

1. **Backup First**: Always backup your local application before deploying
2. **Test Locally**: Test with production-like settings locally first
3. **Gradual Rollout**: Test thoroughly before making it live
4. **Monitor Logs**: Regularly check error logs after deployment
5. **Update Regularly**: Keep Laravel and dependencies updated

---

**Good luck with your deployment! 🚀**

