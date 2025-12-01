# AquaFlow - Water Refilling System

A complete PHP/MySQL water refilling management system with 100% visual fidelity to modern design standards.

## Features

### Admin Features
- **Dashboard**: Real-time statistics and analytics
- **Customer Management**: Full CRUD operations for customer database
- **Order Forms**: Create and manage water refill orders
- **Delivery Tracking**: Monitor and manage delivery schedules
- **Point of Sale**: Process walk-in transactions
- **User Management**: Manage system users and permissions

### Cashier Features
- **Dashboard**: View key statistics
- **Customers**: View customer database
- **Order Forms**: Create new orders
- **Deliveries**: View delivery schedules

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3 (Custom Properties), Vanilla JavaScript
- **Icons**: Inline SVG (Lucide-inspired)
- **Design**: Custom design system with exact color palette

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server (WAMP, XAMPP, MAMP, or similar)
- mysqli PHP extension enabled

### Setup Instructions

1. **Clone/Download the project**
   ```bash
   # Place in your web server directory
   # For WAMP: C:\wamp64\www\Water-Refilling-System
   # For XAMPP: C:\xampp\htdocs\Water-Refilling-System
   ```

2. **Create Database**
   - Open phpMyAdmin or MySQL command line
   - Import the database schema:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
   Or manually execute the SQL in `database/schema.sql`

3. **Configure Database Connection**
   - Open `config/database.php`
   - Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'water_refilling_system');
   ```

4. **Configure Base URL**
   - Open `config/constants.php`
   - Update the BASE_URL to match your setup:
   ```php
   define('BASE_URL', '/Water-Refilling-System');
   ```

5. **Set Permissions** (Linux/Mac)
   ```bash
   chmod 755 -R .
   chmod 644 -R assets/
   ```

6. **Access the Application**
   - Navigate to: `http://localhost/Water-Refilling-System`
   - You'll be redirected to the login page

## Default Credentials

### Admin Account
- **Email**: admin@aquaflow.com
- **Password**: password123

### Cashier Account
- **Email**: cashier@aquaflow.com
- **Password**: password123

**⚠️ IMPORTANT**: Change these passwords immediately after first login in production!

## Project Structure

```
water-refilling-system/
├── admin/                  # Admin pages
│   ├── index.php          # Dashboard
│   ├── customers.php      # Customer management
│   ├── forms.php          # Order forms
│   ├── deliveries.php     # Delivery tracking
│   ├── pos.php            # Point of Sale
│   └── users.php          # User management
├── cashier/               # Cashier pages
│   ├── index.php          # Dashboard
│   ├── customers.php      # View customers
│   ├── forms.php          # Create orders
│   └── deliveries.php     # View deliveries
├── assets/
│   ├── css/               # Stylesheets
│   │   ├── reset.css
│   │   ├── variables.css
│   │   ├── components.css
│   │   └── main.css
│   └── js/                # JavaScript files
│       ├── modal.js
│       ├── form-validation.js
│       └── main.js
├── auth/                  # Authentication
│   ├── login.php
│   ├── logout.php
│   └── session.php
├── config/                # Configuration
│   ├── database.php
│   └── constants.php
├── includes/              # Common includes
│   ├── header.php
│   ├── footer.php
│   ├── sidebar.php
│   └── functions.php
├── database/              # Database schema
│   └── schema.sql
└── index.php              # Entry point
```

## Design System

### Color Palette
- **Primary Water Theme**: #7FA489 (water-300)
- **Background**: #F8FAF5 (water-50)
- **Surface**: #E6EFE7 (water-100)
- **Borders**: #BBD2C5 (water-200)

### Typography
- **Font Family**: Inter (Google Fonts)
- **Base Size**: 16px
- **Weights**: 300, 400, 500, 600, 700

### Components
- Buttons (Primary, Secondary, Ghost)
- Input fields with icons
- Cards and stat cards
- Badges (Success, Warning, Error, Info)
- Tables with hover states
- Modals with animations
- Form validation

## Security Features

- ✅ Password hashing (bcrypt)
- ✅ Prepared statements (SQL injection prevention)
- ✅ Input sanitization (XSS prevention)
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ CSRF token support (in functions.php)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Database Schema

### Tables
1. **users** - System users (admin, cashier)
2. **customers** - Customer database
3. **orders** - Water refill orders
4. **deliveries** - Delivery schedules
5. **pos_transactions** - Point of sale transactions

## Customization

### Changing Colors
Edit `assets/css/variables.css` and update CSS custom properties:
```css
:root {
    --water-300: #7FA489;  /* Primary color */
    --water-400: #6B8F76;  /* Hover state */
    /* ... */
}
```

### Adding New Pages
1. Create PHP file in appropriate directory (admin/ or cashier/)
2. Include required files (database, session, functions, header, sidebar)
3. Add requireRole() check
4. Add navigation link in `includes/sidebar.php`

### Modifying Container Prices
Edit `config/constants.php`:
```php
define('CONTAINER_PRICES', [
    '5-gallon' => 25.00,
    '3-gallon' => 15.00,
    '1-gallon' => 8.00
]);
```

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config/database.php`
- Ensure database exists

### Page Not Found (404)
- Check BASE_URL in `config/constants.php`
- Verify .htaccess if using Apache
- Check file permissions

### Session Issues
- Ensure PHP session is enabled
- Check session.save_path in php.ini
- Verify write permissions on session directory

### Styling Issues
- Clear browser cache
- Check CSS file paths in header.php
- Verify BASE_URL is correct

## License

This project is provided as-is for educational and commercial use.

## Support

For issues or questions, please refer to the documentation or contact support.

---

**Built with ❤️ using Pure PHP, MySQL, HTML, CSS, and JavaScript**
