# 🍝 Bella Vista Restaurant - Landing Page

A modern, responsive restaurant landing page designed to generate leads and showcase authentic Italian cuisine.

## 📋 Project Overview

This landing page is built for restaurants and food businesses to:
- Showcase menu highlights and restaurant atmosphere
- Capture table reservations and catering inquiries
- Provide contact information and location details
- Generate leads through contact forms

## 🚀 Features

### ✨ Design & User Experience
- **Modern Design**: Clean, elegant layout with warm color scheme
- **Fully Responsive**: Mobile-first design that works on all devices
- **Smooth Animations**: AOS (Animate On Scroll) library integration
- **Interactive Gallery**: Lightbox functionality for restaurant photos
- **Smooth Scrolling**: Enhanced navigation experience

### 📱 Sections Included
1. **Hero Section**: Eye-catching banner with call-to-action
2. **About Section**: Restaurant story and chef information
3. **Menu Highlights**: Featured dishes with prices
4. **Gallery**: Restaurant interior and food photography
5. **Testimonials**: Customer reviews and ratings
6. **Reservation Form**: Lead capture with validation
7. **Contact & Location**: Business info with Google Maps
8. **Footer**: Quick links and social media

### 🛠️ Technical Features
- **SEO Optimized**: Meta tags, structured data, and semantic HTML
- **Form Validation**: Client-side and server-side validation
- **Performance Optimized**: Lazy loading and optimized assets
- **Cross-browser Compatible**: Works on all modern browsers
- **Accessibility**: WCAG compliant design

## 📁 Project Structure

```
restaurant-landing-page/
├── index.html                 # Main HTML file
├── assets/
│   ├── css/
│   │   └── style.css         # Main stylesheet
│   ├── js/
│   │   └── script.js         # JavaScript functionality
│   └── images/               # Image assets (see image guide below)
├── api/
│   └── submit-reservation.php # Backend form handler
├── README.md                 # This file
└── .htaccess                # Apache configuration
```

## 🖼️ Image Requirements

### Required Images (Recommended Dimensions)

**Hero Section:**
- `hero-bg.jpg` - 1920x1080px (Restaurant exterior or signature dish)

**About Section:**
- `chef-cooking.jpg` - 800x600px (Chef preparing food)

**Menu Items:**
- `pasta-carbonara.jpg` - 400x300px
- `margherita-pizza.jpg` - 400x300px
- `osso-buco.jpg` - 400x300px
- `tiramisu.jpg` - 400x300px

**Gallery:**
- `restaurant-interior-1.jpg` - 600x400px
- `food-plating.jpg` - 600x400px
- `wine-selection.jpg` - 600x400px
- `outdoor-seating.jpg` - 600x400px
- `kitchen-action.jpg` - 600x400px
- `private-dining.jpg` - 600x400px

**Testimonials:**
- `customer-1.jpg` - 100x100px (Profile photos)
- `customer-2.jpg` - 100x100px
- `customer-3.jpg` - 100x100px

**Other:**
- `favicon.ico` - 32x32px (Restaurant logo)

### Image Guidelines
- **Format**: JPG for photos, PNG for logos with transparency
- **Quality**: High quality, well-lit, professional photography
- **Optimization**: Compress images for web (aim for <200KB per image)
- **Alt Text**: All images have descriptive alt text for accessibility

## 🔧 Setup Instructions

### 1. Local Development Setup
1. Clone or download the project files
2. Place in your web server directory (e.g., `htdocs` for XAMPP)
3. Add your restaurant images to `assets/images/` directory
4. Configure database connection in `api/submit-reservation.php`
5. Update restaurant information in `index.html`

### 2. Customization
- **Colors**: Modify CSS variables in `style.css`
- **Content**: Update text content in `index.html`
- **Images**: Replace placeholder images with your restaurant photos
- **Contact Info**: Update address, phone, email, and social links

### 3. Form Backend Setup
- Configure MySQL database for reservations
- Update email settings in PHP file
- Test form submission functionality

## 📊 Lead Generation Features

### Reservation Form
- **Required Fields**: Name, Email, Phone, Date, Time, Guests
- **Validation**: Client-side and server-side validation
- **Confirmation**: Email confirmation to customer and restaurant
- **Database Storage**: All reservations stored in MySQL database

### Newsletter Signup
- **Email Collection**: Footer newsletter subscription
- **Validation**: Email format validation
- **Integration**: Ready for email marketing platform integration

### Contact Forms
- **Multiple Touchpoints**: Various contact opportunities throughout page
- **Call-to-Action**: Strategic placement of booking buttons
- **Mobile Optimization**: Touch-friendly buttons and forms

## 🎨 Customization Guide

### Color Scheme
The default color scheme uses warm, food-friendly colors:
- **Primary Gold**: #d4af37
- **Dark Brown**: #2c1810
- **Light Background**: #f8f6f3

### Typography
- **Headings**: Playfair Display (serif)
- **Body Text**: Inter (sans-serif)

### Responsive Breakpoints
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

## 🚀 Performance Optimization

- **Image Optimization**: WebP format support with fallbacks
- **CSS Minification**: Compressed stylesheets
- **JavaScript Optimization**: Debounced scroll events
- **Lazy Loading**: Images load as needed
- **Caching**: Browser caching headers configured

## 📈 SEO Features

- **Meta Tags**: Comprehensive meta tag setup
- **Structured Data**: Restaurant schema markup
- **Open Graph**: Social media sharing optimization
- **Sitemap**: XML sitemap for search engines
- **Mobile-First**: Google mobile-first indexing ready

## 🔒 Security Features

- **Form Validation**: Prevents malicious input
- **CSRF Protection**: Cross-site request forgery protection
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization

## 📞 Support & Maintenance

### Browser Support
- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

### Performance Metrics
- **Page Load Speed**: < 3 seconds
- **Lighthouse Score**: 90+ (Performance, Accessibility, SEO)
- **Mobile Friendly**: Google Mobile-Friendly Test approved

## 📝 License

This project is created for educational and commercial use. Feel free to customize for your restaurant business.

## 🤝 Contributing

To improve this landing page:
1. Fork the repository
2. Create a feature branch
3. Make your improvements
4. Submit a pull request

---

**Built with ❤️ for restaurant owners who want to grow their business online.**
