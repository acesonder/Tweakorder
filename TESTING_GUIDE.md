# Tweakorder - Complete Testing Guide

This guide provides step-by-step instructions for testing and using the Tweakorder application, including complete order workflows.

## Table of Contents
1. [Setup and Installation](#setup-and-installation)
2. [Creating Products](#creating-products)
3. [Creating Clients](#creating-clients)
4. [Creating Workers](#creating-workers)
5. [Creating Orders - Complete Workflow](#creating-orders---complete-workflow)
6. [Viewing Orders](#viewing-orders)
7. [Editing and Fulfilling Orders](#editing-and-fulfilling-orders)
8. [Troubleshooting](#troubleshooting)

## Setup and Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/acesonder/Tweakorder.git
   cd Tweakorder
   ```

2. **Create the database**
   ```bash
   mysql -u root -p < database.sql
   ```
   
   Or if using a different user:
   ```bash
   mysql -u your_username -p < database.sql
   ```

3. **Configure database connection**
   - Edit `config/database.php` with your MySQL credentials
   - Default settings:
     - Host: `localhost`
     - User: `root`
     - Password: (empty)
     - Database: `tweakorder`

4. **Set up file permissions**
   ```bash
   chmod 755 assets/uploads
   ```

5. **Start the server**
   
   For development (PHP built-in server):
   ```bash
   php -S localhost:8000
   ```
   
   For production, configure your web server to point to the project directory.

6. **Access the application**
   - Open browser: http://localhost:8000
   - You should see the main dashboard with 6 navigation cards

## Creating Products

Products are items that can be added to orders. Each product can have a name, description, image, inventory count, and gradient background color.

### Steps to Add a Product

1. From the main dashboard, click **"📦 Add Product"**
2. Fill in the product form:
   - **Product Name** (required): Enter a descriptive name
   - **Description** (optional): Add details about the product
   - **Product Image** (optional): Upload an image file (JPEG, PNG, GIF, WebP)
   - **Inventory** (default: 100): Set the stock quantity
   - **Background Color**: Select from 15 gradient color options
3. Click **"Add Product"**
4. Success message will appear, then redirect to home page

### Example Products to Create for Testing

Create these sample products:

1. **Widget Pro**
   - Description: "Professional-grade widget"
   - Inventory: 50
   - Color: Gradient 1 (Purple/Violet)

2. **Gadget Plus**
   - Description: "Advanced gadget with premium features"
   - Inventory: 30
   - Color: Gradient 5 (Blue/Cyan)

3. **Tool Basic**
   - Description: "Essential tool for daily use"
   - Inventory: 100
   - Color: Gradient 10 (Green/Teal)

## Creating Clients

Clients are customers who place orders.

### Steps to Add a Client

You can add clients in two ways:

#### Method 1: During Order Creation
1. While creating an order, click **"+ Add New Client"** in Step 3
2. Enter first and last name
3. Click **"Save Client"**
4. The new client will be automatically selected

#### Method 2: Direct API Call
For testing purposes, you can use the API:
```bash
curl -X POST http://localhost:8000/api/clients.php \
  -d "first_name=John&last_name=Smith"
```

### Example Clients for Testing

Create these sample clients:
1. **John Smith** - For first test order
2. **Jane Doe** - For second test order
3. **Bob Wilson** - For additional testing

## Creating Workers

Workers are employees registered in the system.

### Steps to Add a Worker

1. From main dashboard, click **"👤 Add Worker"**
2. Fill in the form:
   - **First Name** (required)
   - **Last Name** (required)
3. Click **"Add Worker"**
4. Success message appears, then redirect to home

### Example Workers for Testing

Create these sample workers:
1. **Alice Manager**
2. **Bob Technician**

## Creating Orders - Complete Workflow

This is the core feature of Tweakorder. Orders follow a 4-step process.

### Complete Order Workflow - Test Scenario 1

**Objective**: Create an order for a new client, mark as waiting for supplies, then fulfill it.

#### Step 1: Select Products

1. From main dashboard, click **"🛒 Create Order"**
2. You'll see Step 1 with all available products displayed as widgets
3. Click on products to add them to the order:
   - Click **"Widget Pro"** twice (adds quantity: 2)
   - Click **"Gadget Plus"** once (adds quantity: 1)
4. Each click increases the quantity counter shown on the widget
5. Click **"Next: Confirm Selection"**

#### Step 2: Confirm Selection

1. Review your selected items:
   - Widget Pro: Quantity 2
   - Gadget Plus: Quantity 1
2. Verify the items are correct
3. Click **"Next: Select Client"** to proceed
4. Or click **"← Back"** to change product selection

#### Step 3: Select Client

1. Choose an existing client OR add a new one

**To add a new client:**
1. Click **"+ Add New Client"** button
2. Modal window appears
3. Enter:
   - First Name: "John"
   - Last Name: "Smith"
4. Click **"Save Client"**
5. Modal closes, client is automatically selected
6. Click **"Next: Fulfillment Status"**

**To select existing client:**
1. Click on the client card (e.g., "Jane Doe")
2. Selected client will be highlighted
3. Click **"Next: Fulfillment Status"**

#### Step 4: Order Status

1. Review the complete order summary:
   - Client name
   - All items with quantities
2. Choose order status:
   - **"Mark as Fulfilled"**: Order is complete and ready
   - **"Waiting for Supplies"**: Order is pending items

For this test, click **"Waiting for Supplies"**

3. Success message appears
4. Redirects to home page
5. Order is now in the system

### Complete Order Workflow - Test Scenario 2

**Objective**: Create order for existing client and mark as fulfilled.

1. Click **"🛒 Create Order"**
2. **Step 1**: Select products
   - Click **"Tool Basic"** three times (quantity: 3)
   - Click **"Widget Pro"** once (quantity: 1)
3. **Step 2**: Review and click **"Next: Select Client"**
4. **Step 3**: Select existing client "John Smith"
5. **Step 4**: Click **"Mark as Fulfilled"**
6. Order created and marked as complete

## Viewing Orders

### View Open Orders

Open orders are those with status "waiting" (waiting for supplies).

1. From main dashboard, click **"📋 Open Orders"**
2. See all orders waiting for supplies
3. Each order card shows:
   - Order number
   - Client name
   - Creation date
   - Status badge
   - List of items with quantities

### View All Orders

1. From main dashboard, click **"📊 All Orders"**
2. See complete order history (both waiting and fulfilled)
3. Same information displayed as open orders
4. Useful for reviewing past orders

## Editing and Fulfilling Orders

### Update Order Status

1. From main dashboard, click **"✏️ Edit Orders"**
2. See all orders with action buttons
3. For orders with status "waiting":
   - Click **"Mark as Fulfilled"** to complete the order
4. For fulfilled orders:
   - Click **"Mark as Waiting"** to reopen if needed
5. Success message appears
6. Order list refreshes with updated status

### Complete Workflow Example

**Scenario**: Fulfill a waiting order

1. Navigate to **"Edit Orders"**
2. Find order #1 (John Smith, waiting)
3. Click **"Mark as Fulfilled"**
4. Success message: "Order updated successfully!"
5. Order status changes to "fulfilled"
6. To verify, go to **"Open Orders"** - order should no longer appear
7. Check **"All Orders"** - order shows as "fulfilled"

## Troubleshooting

### Common Issues and Solutions

#### Issue: Database Connection Error
**Symptom**: Pages show "Error loading data" or fail to load

**Solution**:
1. Check MySQL service is running:
   ```bash
   sudo service mysql status
   ```
2. Start if needed:
   ```bash
   sudo service mysql start
   ```
3. Verify credentials in `config/database.php`
4. Test connection:
   ```bash
   mysql -u root -p
   ```

#### Issue: Products Not Displaying
**Symptom**: Create Order page shows "No products available"

**Solution**:
1. Add products first using **"Add Product"** page
2. Verify API works:
   ```bash
   curl http://localhost:8000/api/products.php
   ```
3. Should return JSON with success: true

#### Issue: Image Upload Fails
**Symptom**: Product image doesn't save

**Solution**:
1. Check uploads directory exists and is writable:
   ```bash
   ls -la assets/uploads
   chmod 755 assets/uploads
   ```
2. Verify file type is allowed (JPEG, PNG, GIF, WebP)
3. Check file size isn't too large

#### Issue: Form Submission Does Nothing
**Symptom**: Click submit but nothing happens

**Solution**:
1. Open browser console (F12) and check for JavaScript errors
2. Verify `assets/js/app.js` is loading
3. Check PHP error logs for API issues
4. Ensure server is running on correct port

#### Issue: Orders Not Appearing
**Symptom**: Created order doesn't show in lists

**Solution**:
1. Verify order was created successfully (check for success message)
2. Check database:
   ```bash
   mysql -u root -p tweakorder -e "SELECT * FROM orders;"
   ```
3. Clear browser cache and refresh
4. Check API endpoint:
   ```bash
   curl http://localhost:8000/api/orders.php?status=all
   ```

### API Endpoints Reference

For debugging and testing:

- **Products**
  - GET: `api/products.php` - List all products
  - POST: `api/products.php` - Create product

- **Clients**
  - GET: `api/clients.php` - List all clients
  - POST: `api/clients.php` - Create client

- **Workers**
  - GET: `api/workers.php` - List all workers
  - POST: `api/workers.php` - Create worker

- **Orders**
  - GET: `api/orders.php?status=all` - All orders
  - GET: `api/orders.php?status=waiting` - Open orders only
  - POST: `api/orders.php` - Create order
  - PUT: `api/orders.php` - Update order status

### Browser Compatibility

Tested and working on:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

### Performance Tips

1. Regular database maintenance:
   ```bash
   mysql -u root -p tweakorder -e "OPTIMIZE TABLE products, orders, order_items, clients, workers;"
   ```

2. Clear old fulfilled orders periodically (backup first)
3. Keep product images optimized (<500KB recommended)

## Summary

This guide covers all major workflows in the Tweakorder application. For additional help or to report issues, please create a GitHub issue at: https://github.com/acesonder/Tweakorder/issues

Key points:
- Always create products before creating orders
- Clients can be added during order creation or separately
- Orders follow a clear 4-step process
- Use "Edit Orders" to update order status
- Check "Open Orders" for pending items
- Review "All Orders" for complete history
