# 🚀 Restaurant Landing Page - Setup Guide

## Quick Start (5 Minutes)

### 1. Prerequisites
- **XAMPP** installed and running (Apache + MySQL)
- Web browser (Chrome, Firefox, Safari, Edge)

### 2. Installation Steps

#### Step 1: Download & Extract
1. Place the `restaurant-landing-page` folder in your XAMPP `htdocs` directory
2. The path should be: `C:\xampp\htdocs\restaurant-landing-page\`

#### Step 2: Database Setup
1. Open **phpMyAdmin** in your browser: `http://localhost/phpmyadmin`
2. Create a new database named `restaurant_db`
3. Import the SQL file: `setup/database.sql`
   - Or copy and paste the SQL content and execute it

#### Step 3: Configure Database Connection
1. Open `api/submit-reservation.php`
2. Update the database configuration (lines 20-25):
```php
$db_config = [
    'host' => 'localhost',
    'username' => 'root',        // Your MySQL username
    'password' => '',            // Your MySQL password
    'database' => 'restaurant_db'
];
```

#### Step 4: Test the Website
1. Open your browser and go to: `http://localhost/restaurant-landing-page`
2. The website should load with all sections visible

#### Step 5: Test Form Submission
1. Scroll to the "Reserve Your Table" section
2. Fill out the reservation form
3. Click "Reserve Table"
4. You should see a success message

#### Step 6: Access Admin Panel
1. Go to: `http://localhost/restaurant-landing-page/admin`
2. Login with:
   - **Username:** `admin`
   - **Password:** `admin123`
3. View and manage reservations

## 📧 Email Configuration (Optional)

To enable email notifications:

1. Open `api/submit-reservation.php`
2. Update email configuration (lines 27-35):
```php
$email_config = [
    'restaurant_email' => 'your-restaurant@email.com',
    'restaurant_name' => 'Your Restaurant Name',
    'from_email' => 'noreply@your-domain.com'
];
```

For production, configure SMTP settings for reliable email delivery.

## 🖼️ Adding Your Images

Replace placeholder images in `assets/images/` with your restaurant photos:

### Required Images:
- `hero-bg.jpg` (1920x1080px) - Main hero background
- `chef-cooking.jpg` (800x600px) - Chef or kitchen photo
- `pasta-carbonara.jpg` (400x300px) - Menu item
- `margherita-pizza.jpg` (400x300px) - Menu item
- `osso-buco.jpg` (400x300px) - Menu item
- `tiramisu.jpg` (400x300px) - Menu item
- `restaurant-interior-1.jpg` (600x400px) - Gallery
- `food-plating.jpg` (600x400px) - Gallery
- `wine-selection.jpg` (600x400px) - Gallery
- `outdoor-seating.jpg` (600x400px) - Gallery
- `kitchen-action.jpg` (600x400px) - Gallery
- `private-dining.jpg` (600x400px) - Gallery
- `customer-1.jpg` (100x100px) - Testimonial
- `customer-2.jpg` (100x100px) - Testimonial
- `customer-3.jpg` (100x100px) - Testimonial

## ✏️ Customizing Content

### 1. Restaurant Information
Edit `index.html` and update:
- Restaurant name (search for "Bella Vista")
- Address and contact details
- Menu items and prices
- About section content
- Testimonials

### 2. Colors and Styling
Edit `assets/css/style.css`:
- Primary color: `#d4af37` (gold)
- Dark color: `#2c1810` (brown)
- Background: `#f8f6f3` (cream)

### 3. Google Maps
Update the Google Maps embed in the contact section with your restaurant's location.

## 🔧 Troubleshooting

### Common Issues:

#### 1. "Database connection failed"
- Check if MySQL is running in XAMPP
- Verify database credentials in `api/submit-reservation.php`
- Ensure `restaurant_db` database exists

#### 2. Form not submitting
- Check browser console for JavaScript errors
- Verify the API endpoint path
- Check Apache error logs

#### 3. Images not loading
- Ensure image files exist in `assets/images/`
- Check file names match exactly (case-sensitive)
- Verify image file permissions

#### 4. Admin panel not working
- Check database connection
- Verify `reservations` table exists
- Clear browser cache

### Debug Mode:
Enable error reporting by uncommenting lines 7-8 in `api/submit-reservation.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 🚀 Going Live (Production)

### 1. Security Checklist:
- [ ] Change admin password in `admin/index.php`
- [ ] Update database credentials
- [ ] Disable error reporting in PHP files
- [ ] Configure SSL certificate
- [ ] Set up proper email SMTP
- [ ] Update contact information
- [ ] Test all forms and functionality

### 2. Performance Optimization:
- [ ] Compress and optimize images
- [ ] Enable gzip compression
- [ ] Set up CDN for static assets
- [ ] Configure browser caching
- [ ] Minify CSS and JavaScript

### 3. SEO Optimization:
- [ ] Update meta tags with your restaurant info
- [ ] Submit sitemap to Google Search Console
- [ ] Set up Google Analytics
- [ ] Configure Google My Business
- [ ] Add structured data markup

## 📱 Mobile Testing

Test the website on various devices:
- [ ] iPhone (Safari)
- [ ] Android (Chrome)
- [ ] iPad (Safari)
- [ ] Desktop browsers

## 📊 Analytics Setup

1. **Google Analytics:**
   - Create GA4 property
   - Add tracking code to `index.html`

2. **Facebook Pixel:**
   - Add pixel code for social media advertising

## 🆘 Support

### Resources:
- **Documentation:** This README file
- **Database Schema:** `setup/database.sql`
- **Admin Panel:** `http://localhost/restaurant-landing-page/admin`

### Need Help?
1. Check the troubleshooting section above
2. Review browser console for errors
3. Check Apache/PHP error logs
4. Verify database connection and tables

---

## 🎉 You're Ready!

Your restaurant landing page is now set up and ready to capture reservations and grow your business online!

**Default Admin Login:**
- URL: `http://localhost/restaurant-landing-page/admin`
- Username: `admin`
- Password: `admin123` (⚠️ Change this immediately!)

**Next Steps:**
1. Add your restaurant's images
2. Customize the content and colors
3. Test the reservation form
4. Set up email notifications
5. Go live with your domain!
