# Tweak Order Online - Installation & User Guide

## Quick Start

1. **Database Setup**
   ```bash
   # Create and import database
   mysql -u root -p < database.sql
   ```

2. **Configuration**
   - Edit `config/database.php` with your MySQL credentials
   - Default: localhost, root user, no password, database: tweakorder

3. **File Permissions**
   ```bash
   chmod 755 assets/uploads
   ```

4. **Start Server**
   ```bash
   php -S localhost:8000
   ```

5. **Access Application**
   - Open browser: http://localhost:8000
   - Demo page: http://localhost:8000/demo.html

## User Guide

### Adding Products
1. Click "Add Product" from the main menu
2. Enter product name (required)
3. Optionally add: description, image, inventory count
4. Select a gradient color background
5. Click "Add Product"

### Creating Orders
1. Click "Create Order" from main menu
2. **Step 1**: Click product widgets to add items (click multiple times for quantity)
3. **Step 2**: Review and confirm selected items
4. **Step 3**: Select existing client or add new client
   - For new client: Click "Add New Client", enter first/last name
5. **Step 4**: Choose order status:
   - "Mark as Fulfilled" - Completes the order
   - "Waiting for Supplies" - Keeps order open

### Managing Orders
- **View Open Orders**: See all orders waiting for supplies
- **View All Orders**: Complete order history
- **Edit Orders**: Update order fulfillment status

### Adding Workers
1. Click "Add Worker" from main menu
2. Enter first and last name
3. Click "Add Worker"

## Features

### Product Widgets
- Visual representation with custom gradient backgrounds
- Click to add to order
- Multiple clicks increase quantity
- Real-time count display

### 15 Gradient Color Options
- Purple/Violet gradients
- Pink/Red gradients
- Blue/Cyan gradients
- Green/Teal gradients
- Orange/Yellow gradients
- Pastel gradients
- Dark gradient

### Mobile Responsive
- Fully responsive design
- Works on phones, tablets, and desktops
- Touch-friendly interface
- Optimized for screen sizes down to 375px

### No Authentication
- Streamlined access
- No login required
- No user management overhead

## Technical Architecture

### Database Schema
```
products (id, name, description, image, inventory, background_color)
workers (id, first_name, last_name)
clients (id, first_name, last_name)
orders (id, client_id, status, created_at, updated_at)
order_items (id, order_id, product_id, quantity)
```

### API Endpoints
- `api/products.php` - GET, POST, PUT, DELETE
- `api/workers.php` - GET, POST
- `api/clients.php` - GET, POST
- `api/orders.php` - GET, POST, PUT

### File Structure
```
/
├── index.html              # Landing page
├── add-product.html        # Add product form
├── add-worker.html         # Add worker form
├── create-order.html       # Multi-step order creation
├── open-orders.html        # View open orders
├── all-orders.html         # View all orders
├── edit-orders.html        # Edit order status
├── demo.html              # Interactive demo
├── database.sql           # Database schema
├── api/                   # PHP API endpoints
│   ├── products.php
│   ├── workers.php
│   ├── clients.php
│   └── orders.php
├── config/
│   └── database.php       # Database configuration
└── assets/
    ├── css/
    │   └── style.css      # All styles
    ├── js/
    │   └── app.js         # JavaScript utilities
    └── uploads/           # Product images
```

## Browser Support
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## Notes
- All fields except product name and client/worker names are optional
- Default inventory is 100
- Orders are tracked by status: waiting or fulfilled
- Images are stored in assets/uploads/
- Ajax is used for all data operations
