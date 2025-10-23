# 🔧 Troubleshooting Guide - Internal Server Error

## Quick Fixes to Try (In Order)

### 1. **Test Basic PHP Functionality**
Navigate to: `http://localhost/restaurant-landing-page/test.php`
- If this works: PHP is fine, issue is elsewhere
- If this fails: PHP configuration problem

### 2. **Check XAMPP Services**
Ensure these are running in XAMPP Control Panel:
- ✅ **Apache** (green light)
- ✅ **MySQL** (green light)

### 3. **Database Setup**
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database: `restaurant_db`
3. Import SQL file: `setup/database.sql`

### 4. **Test Simple API**
Navigate to: `http://localhost/restaurant-landing-page/api/test-simple.php`
- Should return JSON response
- If 500 error: PHP syntax issue

### 5. **Check Error Logs**
Look for error details in:
- `C:\xampp\apache\logs\error.log`
- `C:\xampp\php\logs\php_error_log`

---

## Common Issues & Solutions

### Issue 1: "Class 'PDO' not found"
**Solution:** Enable PDO MySQL extension
1. Open `C:\xampp\php\php.ini`
2. Uncomment: `extension=pdo_mysql`
3. Restart Apache

### Issue 2: ".htaccess causing 500 error"
**Solution:** Temporarily disable .htaccess
1. Rename `.htaccess` to `.htaccess.disabled`
2. Test website
3. If works, gradually add back .htaccess rules

### Issue 3: "Database connection failed"
**Solution:** Check MySQL credentials
1. Verify MySQL is running
2. Check username/password in `api/submit-reservation.php`
3. Default XAMPP: username=`root`, password=`empty`

### Issue 4: "Syntax error in PHP"
**Solution:** Check PHP files for errors
1. Enable error reporting in PHP
2. Check `api/submit-reservation.php` for syntax issues
3. Use `test.php` to verify PHP works

---

## Step-by-Step Diagnosis

### Step 1: Basic Tests
```
✅ Test: http://localhost/restaurant-landing-page/test.php
✅ Test: http://localhost/restaurant-landing-page/api/test-simple.php
✅ Test: http://localhost/restaurant-landing-page/index.html (static)
```

### Step 2: Check Services
1. Open XAMPP Control Panel
2. Ensure Apache and MySQL are running
3. Click "Admin" next to MySQL to open phpMyAdmin

### Step 3: Database Setup
```sql
-- Run this in phpMyAdmin
CREATE DATABASE IF NOT EXISTS restaurant_db;
USE restaurant_db;
-- Then import setup/database.sql
```

### Step 4: PHP Configuration
Check if these extensions are enabled in `php.ini`:
```ini
extension=pdo_mysql
extension=mysqli
```

---

## Emergency Fixes

### Fix 1: Disable .htaccess Temporarily
```bash
# Rename the file to disable it
mv .htaccess .htaccess.disabled
```

### Fix 2: Enable PHP Error Display
Add to top of `api/submit-reservation.php`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Fix 3: Simple Index Test
Create `simple-test.html`:
```html
<!DOCTYPE html>
<html>
<head><title>Test</title></head>
<body><h1>Static HTML Works!</h1></body>
</html>
```

---

## What Each Test File Does

### `test.php`
- Tests PHP functionality
- Tests database connection
- Shows PHP configuration
- Displays phpinfo()

### `api/test-simple.php`
- Tests API endpoint
- Returns JSON response
- Minimal code to isolate issues

### `index.html`
- Main website (should work as static HTML)
- Tests CSS and JavaScript loading
- Tests external resources

---

## Expected Results

### ✅ Working Scenario:
- `test.php` → Shows PHP info and database status
- `api/test-simple.php` → Returns JSON success message
- `index.html` → Loads restaurant website

### ❌ Error Scenarios:

**500 Internal Server Error:**
- Check Apache error logs
- Usually .htaccess or PHP syntax issue

**Database Connection Failed:**
- MySQL not running
- Wrong credentials
- Database doesn't exist

**Blank Page:**
- PHP fatal error
- Check PHP error logs

---

## Contact Information

If you're still having issues:

1. **Check the error logs** (most important!)
2. **Try the test files** to isolate the problem
3. **Disable .htaccess** temporarily
4. **Verify XAMPP services** are running

The website should work once these basic issues are resolved!
