# Tweak Order Online

A professional, mobile-responsive web application for managing products, workers, clients, and orders with a streamlined user experience. Now featuring a complete **Client Portal** for customer self-service ordering!

## 🌟 Features

### Staff Portal
- **Add Products** - Create products with images, descriptions, inventory, and custom gradient backgrounds
- **Add Workers** - Register workers in the system
- **Create Orders** - Multi-step order creation flow with product selection, client management, and fulfillment tracking
- **View Open Orders** - Monitor orders waiting for supplies
- **View All Orders** - Complete order history
- **Edit Orders** - Update order status with 11+ status options

### 🆕 Client Portal
- **Client Registration** - Self-service account creation with unique username generation
- **Secure Login** - Password-protected client accounts with session management
- **Forgot Password** - Security question-based password recovery
- **Client Dashboard** - View order history, statistics, and order status
- **Self-Service Ordering** - Unified single-page order placement
- **Pickup/Dropoff** - Choose delivery method and location
- **Order Scheduling** - Optional date/time selection
- **Order Tracking** - Real-time status updates on all orders
- **Special Instructions** - Add delivery notes and contact information

## Technology Stack

- **Backend**: PHP, MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Features**: Ajax for smooth interactions, responsive design, smooth animations, session-based authentication

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/acesonder/Tweakorder.git
   cd Tweakorder
   ```

2. **Create the database**
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Add sample data (optional)**
   ```bash
   mysql -u root -p < sample_data.sql
   ```

4. **Configure database connection**
   - Edit `config/database.php` with your MySQL credentials
   - Default settings: host=localhost, user=root, password='', database=tweakorder

5. **Grant database privileges**
   ```bash
   mysql -u root -p -e "GRANT ALL PRIVILEGES ON tweakorder.* TO 'root'@'localhost'; FLUSH PRIVILEGES;"
   ```

6. **Set up file permissions**
   ```bash
   chmod 755 assets/uploads
   ```

7. **Start your web server**
   - Point your web server document root to the project directory
   - For development, you can use PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```

8. **Access the application**
   - **Staff Portal**: `http://localhost:8000/index.html`
   - **Client Portal**: `http://localhost:8000/client-portal.html`

## Database Configuration

The default database configuration is:
- Host: localhost
- Username: root
- Password: (empty)
- Database: tweakorder

To modify these settings, edit `config/database.php`.

## Features Overview

### Product Management
- Add products with optional images, descriptions, and inventory
- Choose from 15 beautiful gradient color backgrounds
- Visual product widgets in order creation
- Category and SKU support

### Staff Order Creation Flow
1. **Select Products** - Click product widgets to add to order
2. **Confirm Selection** - Review selected items
3. **Select Client** - Choose existing client or add new one
4. **Set Status** - Choose from multiple status options

### Client Order Creation Flow
1. **Select Products** - Click product cards to add items
2. **Review Cart** - Adjust quantities or remove items
3. **Choose Delivery** - Select pickup or dropoff with location
4. **Schedule** - Optional date/time selection
5. **Add Instructions** - Special delivery notes
6. **Place Order** - One-click order submission

### Order Status Options
- **Processing** - Initial status for client orders
- **Accepted** - Order accepted by staff
- **Waiting for Supplies** - Items not in stock
- **Waiting for Customer** - Customer response needed
- **Further Information Needed** - Additional details required
- **Problems Unknown** - Issue being investigated
- **Ready for Pickup** - Order ready for collection
- **Ready for Dropoff** - Order ready for delivery
- **Awaiting Scheduled Time** - Order scheduled for future
- **Fulfilled** - Order completed
- **Cancelled** - Order cancelled

### Client Portal Authentication
- **Unique Username Generation** - Format: FIRSTLASTMMDDYY (e.g., MICBRO050684)
- **Secure Password Storage** - Passwords hashed with bcrypt
- **Security Questions** - For password recovery
- **Session Management** - Secure client sessions
- **Client Isolation** - Clients can only view their own orders

### Mobile Responsive
The application is fully responsive and works seamlessly on:
- Desktop computers
- Tablets
- Mobile phones

## User Guides

- [**Client Portal User Guide**](CLIENT_PORTAL_GUIDE.md) - Complete guide for customers
- [**Staff Workflow Guide**](WORKFLOW_GUIDE.md) - Guide for staff users

## API Endpoints

### Client APIs
- `GET/POST /api/auth.php` - Authentication (register, login, logout, forgot password)
- `GET/POST /api/client-orders.php` - Client order management
- `GET /api/locations.php` - Pickup/dropoff locations
- `GET /api/schedule.php` - Schedule availability

### Staff APIs
- `GET/POST/PUT/DELETE /api/products.php` - Product management
- `GET/POST /api/clients.php` - Client management
- `GET/POST /api/workers.php` - Worker management
- `GET/POST/PUT /api/orders.php` - Order management

## Design Features

- Professional gradient designs
- Smooth animations and transitions
- Intuitive user interface
- Real-time updates with Ajax
- Secure authentication system
- Client-specific order viewing
- Comprehensive order status tracking

## Database Schema

The application includes tables for:
- **products** - Product catalog with categories and SKUs
- **clients** - Client accounts with authentication
- **users** - Staff user accounts (Admin, Manager, Worker, Viewer)
- **workers** - Worker registry
- **orders** - Order records with enhanced status options
- **order_items** - Order line items
- **locations** - Pickup/dropoff locations
- **schedule_availability** - Time slot management
- **notifications** - Notification system (future use)
- **sessions** - Session management
- **audit_log** - Change tracking (future use)
- **product_categories** - Product categorization (future use)

## Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Security question verification for password recovery
- Client data isolation
- XSS protection

## Testing

Run the complete test workflow:
```bash
bash test_complete_workflow.sh
```

This tests all API endpoints, creates sample data, and verifies the complete order workflow.

## Support

For issues or questions, please open an issue on GitHub.

## License

This project is open source and available under the MIT License.