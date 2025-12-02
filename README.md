# AquaFlow - Water Refilling System Management

A comprehensive PHP/MySQL-based water refilling business management system designed to streamline operations, manage customers, process orders, and track deliveries with a modern, intuitive interface.

## 🌟 Key Features

### 👨‍💼 Admin Panel
- **Dashboard**: Real-time business insights and analytics
- **Customer Management**: Complete customer database with order history
- **Order Processing**: Create, view, and manage water refill orders
- **Delivery Management**: Schedule and track delivery status in real-time
- **Point of Sale**: Process both walk-in and pre-orders efficiently
- **User Management**: Control access with role-based permissions
- **Reporting**: Generate sales and delivery reports

### 💰 Cashier Interface
- **Quick Order Processing**: Fast and efficient order entry
- **Customer Lookup**: Access to customer information and order history
- **Delivery Scheduling**: Schedule and update delivery details
- **Transaction History**: View and manage sales records

## 🛠 Technology Stack

- **Frontend**: 
  - HTML5, CSS3 with Custom Properties
  - Vanilla JavaScript (ES6+)
  - Responsive Design (Mobile & Desktop)
  - Inline SVG Icons (Lucide-inspired)

- **Backend**:
  - PHP 7.4+
  - MySQL 5.7+ (InnoDB)
  - PDO for database operations
  - Secure session management

- **Security**:
  - Password hashing (bcrypt)
  - Prepared statements
  - Input validation and sanitization
  - CSRF protection

## 🚀 Installation Guide

### Prerequisites
- Web Server: Apache/Nginx (WAMP/XAMPP/MAMP recommended)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Required PHP Extensions:
  - mysqli
  - PDO
  - JSON
  - Session
  - cURL (recommended)

### 🛠 Setup Instructions

1. **Download and Extract**
   - Download the latest release or clone the repository
   - Place the files in your web server's root directory:
     - WAMP: `C:\wamp64\www\Water-Refilling-System`
     - XAMPP: `C:\xampp\htdocs\Water-Refilling-System`
     - MAMP: `/Applications/MAMP/htdocs/Water-Refilling-System`

2. **Database Setup**
   - Create a new MySQL database named `water_refilling_system`
   - Import the database schema using one of these methods:
     
     **Using phpMyAdmin:**
     1. Open phpMyAdmin in your browser
     2. Select the created database
     3. Click "Import"
     4. Choose `database/schema.sql`
     5. Click "Go"

     **Using MySQL Command Line:**
     ```bash
     mysql -u [username] -p water_refilling_system < database/schema.sql
     ```

3. **Configuration**
   - Open `config/database.php` and update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'water_refilling_system');
     ```

4. **Web Server Configuration (Optional)**
   - Ensure mod_rewrite is enabled for clean URLs
   - Set the document root to the project's public directory
   - Configure proper file permissions (755 for directories, 644 for files)

5. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/Water-Refilling-System`
   - Use the default credentials to log in:
     - Admin: `admin@aquaflow.com` / `password123`
     - Cashier: `cashier@aquaflow.com` / `password123`

   ⚠️ **Important:** Change the default passwords after first login
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

## 📖 User Guide

This system is designed to be intuitive for both administrators and cashiers. Here is how to handle common daily tasks.

### 1. Admin Interface (Business Owners & Managers)

**📊 Dashboard Overview**
The Admin Dashboard provides a real-time snapshot of your business:
- **Key Metrics**: Instantly see total active customers, today's revenue, and pending deliveries.
- **Recent Activity**: Monitor the latest orders and upcoming delivery schedules.

**👥 Managing Customers**
- **Add New**: Go to **Customers** > **Add Customer**. Enter their Name, Phone, and Address.
- **View History**: Click on any customer to see their total orders and activity logs.
- **Edit/Delete**: Easily update customer details or remove inactive records.

**📝 Processing Orders (Refills)**
1. Navigate to **Order Forms**.
2. Click **"New Order"**.
3. **Select Customer**: Choose from your registered list.
4. **Order Details**: Pick the container size (5-gal, 3-gal, 1-gal) and quantity.
5. **Schedule**: Set the **Delivery Date & Time**.
6. **Status**: Start as "Pending" and update as the order progresses.

**🚚 Managing Deliveries**
1. Go to the **Deliveries** page.
2. View a chronological list of all scheduled deliveries.
3. **Update Status**: Move deliveries from "Pending" → "In-Transit" → "Completed" as your drivers fulfill them.

### 2. Cashier Interface (Front Desk)

**💰 Point of Sale (POS)**
Ideal for walk-in customers who pay immediately:
1. Go to **Point of Sale**.
2. **Select Items**: Click products (e.g., 5-Gallon Refill) to add them to the cart.
3. **Customer**: Link to a registered customer or leave as "Walk-in".
4. **Checkout**: Review total, select payment method (Cash, Card, GCash), and complete the sale.

**⚡ Quick Dashboard**
Focuses on daily performance:
- **Today's Sales**: Track cash collected today.
- **Transaction Count**: See how many customers have been served.

### 3. Common Workflows

**Scenario A: A New Customer Calls for Delivery**
1. **Log in** as Admin.
2. Go to **Customers** > **Add Customer** and save their details.
3. Go to **Order Forms** > **New Order**.
4. Select the new customer, enter order details, and set the **Delivery Date**.
5. Click **Create Order**. It now appears on the Delivery Schedule.

**Scenario B: A Walk-in Customer Buys a Bottle**
1. **Log in** as Cashier.
2. Go to **Point of Sale**.
3. Tap the product (e.g., "5-Gallon Round").
4. Click **Pay**, enter the amount received, and confirm.
5. The transaction is recorded, and inventory/sales stats update automatically.

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

## 🗃 Database Schema

The system uses a relational database with the following main tables:

### Core Tables
1. **users**
   - Stores system user accounts (admin and cashier roles)
   - Tracks login activity and user status

2. **customers**
   - Maintains customer information and order history
   - Tracks customer activity and preferences

3. **orders**
   - Manages all water refill orders
   - Tracks order status and delivery information

4. **deliveries**
   - Schedules and tracks delivery status
   - Links to orders and customers

5. **pos_transactions**
   - Records point-of-sale transactions
   - Tracks payment methods and totals

## 🔒 Security Best Practices

- All passwords are hashed using bcrypt
- SQL injection prevention using prepared statements
- CSRF protection for all forms
- Input validation and sanitization
- Session security measures
- Role-based access control
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
