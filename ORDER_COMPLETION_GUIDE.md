# Order Completion Guide - Tweak Order Online

This guide provides step-by-step instructions for staff on how to complete orders in the Tweak Order Online system.

## Table of Contents
1. [Getting Started](#getting-started)
2. [Creating Products](#creating-products)
3. [Creating Orders](#creating-orders)
4. [Managing Orders](#managing-orders)
5. [Troubleshooting](#troubleshooting)

---

## Getting Started

### Accessing the Application
1. Open your web browser
2. Navigate to the application URL (e.g., `http://localhost:8000` for local development)
3. You will see the main dashboard with six options:
   - 📦 Add Product
   - 👤 Add Worker
   - 🛒 Create Order
   - 📋 Open Orders
   - 📊 All Orders
   - ✏️ Edit Orders

![Main Dashboard](https://github.com/user-attachments/assets/5f3c86a0-0120-4472-8efd-60766bcf5d0b)

---

## Creating Products

Before creating orders, you need to add products to your inventory.

### Steps to Add a Product:
1. Click **"📦 Add Product"** from the main dashboard
2. Fill in the product information:
   - **Product Name** (required): Enter a descriptive name
   - **Description** (optional): Add details about the product
   - **Product Image** (optional): Upload an image
   - **Inventory** (optional): Set initial stock quantity (default: 100)
   - **Background Color**: Select from 15 beautiful gradient options
3. Click **"Add Product"** to save

### Product Features:
- Visual product widgets with custom gradient backgrounds
- Inventory tracking
- Optional images and descriptions
- Multiple color gradient options for easy visual identification

---

## Creating Orders

Orders are created through a 4-step process that makes it easy to select products, choose clients, and set fulfillment status.

### Step 1: Select Products

![Step 1 - Select Products](https://github.com/user-attachments/assets/160dd1fa-77c6-4977-8f24-05a19c8591a9)

1. Click **"🛒 Create Order"** from the main dashboard
2. You'll see all available products displayed as cards with gradient backgrounds
3. Click on a product card to add it to your order
4. Click multiple times to increase quantity (each click adds 1)
5. The quantity badge appears in the top-right corner of the product card
6. Click **"Next: Confirm Selection"** when done

**Tips:**
- Product cards show the product name and background gradient
- The quantity counter shows how many of each item you've selected
- You can select multiple different products

![Products Selected](https://github.com/user-attachments/assets/f4e8cd15-447c-4ba5-b4e8-d5e4cdc5e5fa)

### Step 2: Confirm Your Selection

![Step 2 - Confirmation](https://github.com/user-attachments/assets/e823e507-243e-4dc1-883d-7c8ccbfe71f3)

1. Review the list of selected items and quantities
2. Verify that all items are correct
3. Click **"← Back"** if you need to modify your selection
4. Click **"Next: Select Client"** to proceed

**What you'll see:**
- Product names in bold
- Quantity for each item
- Clean, organized list format

### Step 3: Select Client

![Step 3 - Select Client](https://github.com/user-attachments/assets/54d9ba61-9fca-420a-a5c3-609c4c7136c8)

1. Choose an existing client by clicking their name card
2. **OR** click **"+ Add New Client"** to create a new client:
   - Enter First Name (required)
   - Enter Last Name (required)
   - Click **"Save Client"**
3. The selected client card will be highlighted
4. Click **"Next: Fulfillment Status"** to proceed

**Client Management:**
- All existing clients are displayed as cards
- Easy to add new clients on-the-fly
- Client information is saved for future orders

### Step 4: Order Status & Completion

![Step 4 - Order Summary](https://github.com/user-attachments/assets/e823e507-243e-4dc1-883d-7c8ccbfe71f3)

1. Review the complete order summary:
   - Client name
   - All items and quantities
2. Choose the order status:
   - **"Mark as Fulfilled"**: Order is complete and ready
   - **"Waiting for Supplies"**: Order is pending supplies
3. Click your chosen status button to create the order
4. You'll see a success message: "Order created successfully!"

**Order Status Options:**
- **Fulfilled**: Use when order is complete and can be delivered
- **Waiting for Supplies**: Use when waiting for inventory or supplies

---

## Managing Orders

### Viewing Open Orders
1. Click **"📋 Open Orders"** from the main dashboard
2. See all orders with status "Waiting for Supplies"
3. Review order details including:
   - Order ID
   - Client name
   - Items and quantities
   - Created date

### Viewing All Orders
1. Click **"📊 All Orders"** from the main dashboard
2. See complete order history (both fulfilled and waiting)
3. Review all order details
4. Use this for reporting and tracking

### Editing Orders
1. Click **"✏️ Edit Orders"** from the main dashboard
2. View list of orders
3. Change order status:
   - Update from "Waiting" to "Fulfilled"
   - Update from "Fulfilled" to "Waiting"
4. Save changes

---

## Complete Order Workflow Example

### Scenario: Creating an Order for John Smith

**Step 1: Add Products (if needed)**
- Navigate to Add Product
- Create: Premium Widget, Standard Gadget, Deluxe Tool

**Step 2: Create the Order**
1. Go to Create Order
2. Select products:
   - Premium Widget (click twice for quantity 2)
   - Standard Gadget (click once for quantity 1)
   - Deluxe Tool (click once for quantity 1)
3. Click "Next: Confirm Selection"
4. Review items, click "Next: Select Client"
5. Select "John Smith" or add new client
6. Click "Next: Fulfillment Status"
7. Review order summary
8. Click "Mark as Fulfilled" (or "Waiting for Supplies" if needed)
9. Order created successfully!

**Step 3: Verify the Order**
1. Go to "All Orders" to see the completed order
2. Verify client name and items are correct

---

## Troubleshooting

### Common Issues and Solutions

**Issue: Products not loading**
- Solution: Check database connection in `config/database.php`
- Verify MySQL service is running
- Check browser console for errors

**Issue: Cannot create order**
- Solution: Ensure at least one product is selected
- Verify a client is selected
- Check that products exist in the database

**Issue: Client not found**
- Solution: Use "Add New Client" to create the client first
- Verify client was successfully saved

**Issue: Order not appearing in Open Orders**
- Solution: Check if order status is "Waiting for Supplies"
- Fulfilled orders appear only in "All Orders"

**Issue: Image upload not working**
- Solution: Verify `assets/uploads/` directory exists
- Check directory permissions: `chmod 755 assets/uploads`
- Verify file type is allowed (JPEG, PNG, GIF, WebP)

---

## Database Configuration

The application uses a secure configuration system:

### For Development:
1. Copy `config/database.local.php.example` to `config/database.local.php`
2. Update credentials in the local file
3. This file is excluded from version control

### For Production:
Use environment variables:
```bash
export DB_HOST=localhost
export DB_USER=your_username
export DB_PASS=your_password
export DB_NAME=tweakorder
```

---

## API Endpoints Reference

For advanced users and developers:

- **Products**: `api/products.php` (GET, POST, PUT, DELETE)
- **Clients**: `api/clients.php` (GET, POST)
- **Workers**: `api/workers.php` (GET, POST)
- **Orders**: `api/orders.php` (GET, POST, PUT)

---

## Support

For additional help:
- Check the main README.md for installation instructions
- Review USER_GUIDE.md for technical details
- Open an issue on GitHub for bugs or feature requests

---

## Summary

The Tweak Order Online system provides a streamlined workflow for:
✅ Managing product inventory
✅ Creating orders with visual product selection
✅ Managing client information
✅ Tracking order fulfillment status
✅ Viewing order history

The 4-step order creation process ensures accuracy and ease of use for staff members.
