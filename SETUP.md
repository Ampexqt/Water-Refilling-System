# Quick Setup Guide

## Step 1: Import Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a database
3. Name it: `water_refilling_system`
4. Click on the database
5. Go to "Import" tab
6. Choose file: `database/schema.sql`
7. Click "Go"

## Step 2: Verify Configuration

The system is pre-configured for WAMP with these settings:
- Database Host: localhost
- Database User: root
- Database Password: (empty)
- Database Name: water_refilling_system
- Base URL: /Water-Refilling-System

If you need to change these, edit:
- `config/database.php` - for database settings
- `config/constants.php` - for BASE_URL

## Step 3: Access the System

1. Make sure WAMP is running (green icon)
2. Open browser and go to: http://localhost/Water-Refilling-System
3. You'll be redirected to the login page

## Step 4: Login

### Admin Account
- Email: admin@aquaflow.com
- Password: password123

### Cashier Account
- Email: cashier@aquaflow.com
- Password: password123

## What's Included

✅ Complete authentication system
✅ Role-based access control (Admin & Cashier)
✅ Customer management (CRUD)
✅ Order/Forms management
✅ Delivery tracking
✅ Point of Sale system
✅ User management (Admin only)
✅ Responsive design
✅ Modern UI with exact color palette
✅ Sample data included

## Sample Data

The database includes:
- 2 users (admin and cashier)
- 4 sample customers
- 4 sample orders
- 3 sample deliveries

## Testing Checklist

- [ ] Login with admin account
- [ ] View dashboard statistics
- [ ] Navigate to Customers page
- [ ] Add a new customer
- [ ] Edit a customer
- [ ] Create a new order in Forms
- [ ] View deliveries
- [ ] Test Point of Sale
- [ ] Manage users
- [ ] Logout and login with cashier account
- [ ] Verify cashier has limited access

## Troubleshooting

**Problem**: Can't connect to database
**Solution**: 
- Make sure WAMP is running
- Verify database exists in phpMyAdmin
- Check credentials in config/database.php

**Problem**: Page shows 404
**Solution**:
- Verify BASE_URL in config/constants.php matches your folder name
- Check that files are in the correct directory

**Problem**: Blank page
**Solution**:
- Enable error reporting in php.ini
- Check Apache error logs
- Verify PHP version is 7.4 or higher

## Next Steps

1. Change default passwords
2. Add your own customers
3. Customize colors in assets/css/variables.css
4. Update container prices in config/constants.php
5. Add your company logo

## Need Help?

Check the main README.md for detailed documentation.
