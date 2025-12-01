# Implementation Plan: Add Edit & Delete Actions to Forms Page

## Objective
Add Edit and Delete functionality to the Orders table on the Forms page (`admin/forms.php`), matching the design and functionality of the Customers page.

## Changes Required

### 1. PHP Backend Updates (forms.php)

#### A. Update Form Handler (Lines 15-45)
- Change from simple `if` to `switch` statement
- Add `case 'update':` handler for editing orders
- Add `case 'delete':` handler for deleting orders

#### B. Add "Actions" Column to Table (Line ~99)
- Add `<th>Actions</th>` to table header
- Add action buttons cell with Edit and Delete icons to each row
- Use same SVG icons as customers page

### 2. Add Edit Order Modal
- Duplicate the "Add Order" modal structure
- Change ID to `editOrderModal`
- Change title to "Edit Order"
- Add hidden input for order ID
- Change action to "update"
- Change button text to "Update Order"
- Pre-populate form fields with JavaScript

### 3. Add Delete Confirmation
- Add hidden delete form (same as customers page)
- Form submits with action="delete" and order ID

### 4. Add JavaScript Functions
- `editOrder(order)` - Populates edit modal with order data
- `deleteOrder(id, customerName)` - Confirms and submits delete

## Files to Modify
1. `admin/forms.php` - Main file with all changes

## Implementation Steps
1. Update PHP form handler with switch statement
2. Add "Actions" column header to table
3. Add action buttons to table rows
4. Add Edit Order modal HTML
5. Add delete form HTML
6. Add JavaScript functions at end of file

## Testing
1. Test creating new order
2. Test editing existing order
3. Test deleting order with confirmation
4. Verify all modals scroll properly
5. Check that customer total_orders updates correctly

Would you like me to proceed with implementing these changes?
