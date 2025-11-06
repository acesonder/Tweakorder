# Tweak Order Online v2.0

A professional, enterprise-grade web application for managing products, workers, clients, and orders with comprehensive analytics, authentication, and automation. Now featuring **Analytics Dashboard**, **Staff Authentication**, **Bulk Import/Export**, **Calendar View**, **Interactive Tutorials**, and much more!

## 🌟 Features

### 🆕 **NEW in v2.0**

#### Database Import Manager 🚀
- **Web-Based Import Interface** - User-friendly HTML interface for SQL imports at `import-data.html`
- **Connection Testing** - Test database credentials before importing
- **File Validation** - Verify SQL files exist and syntax is correct
- **Quick Actions** - One-click buttons to verify, test, import, and troubleshoot
- **Comprehensive SQL Files**:
  - `database_schema.sql` - Complete database structure with all tables
  - `harm_reduction_products.sql` - 80+ harm reduction products catalog
  - `demo_data.sql` - Sample data with 12 clients, 20 orders, locations, schedules
- **Smart Import Order** - Guided import process with recommended file sequence
- **Database Management** - Clear database, change credentials, troubleshoot issues
- **Status Feedback** - Real-time import status and error reporting

#### Favorite Products
- **Mark as Favorite** - Admin and staff can mark products as favorites when creating or editing
- **Quick Filter** - Filter order product selection to show only favorites
- **Visual Indicator** - Favorite products display with ★ symbol
- **Easy Access** - Streamlines order creation for frequently used items

#### Staff Authentication & Security
- **Multi-User Authentication** - Role-based access control (Admin, Manager, Worker, Viewer)
- **Staff Dashboard** - Personalized portal with quick actions and recent activity
- **Activity Logging** - Complete audit trail of all user actions
- **Secure Sessions** - Password hashing and session management
- **Demo Accounts** - Pre-configured for testing (admin/admin123, manager/manager123, worker/worker123)

#### Analytics & Reporting
- **Visual Analytics Dashboard** - Real-time statistics with interactive charts
- **Order Trends** - Line charts showing order patterns over time
- **Status Distribution** - Pie charts for order status breakdown
- **Top Products** - Bar charts highlighting popular items
- **Client Activity** - Track client engagement and patterns
- **Key Insights** - Automatic insight generation and recommendations
- **Time Filters** - View data by today, week, month, year, or custom range
- **Export Reports** - Download reports for external analysis

#### Bulk Operations
- **Import/Export System** - CSV import/export for products, clients, orders, workers
- **Drag & Drop Upload** - Easy file uploads with preview
- **Data Validation** - Preview data before importing
- **Progress Tracking** - Visual progress bars for bulk operations
- **CSV Templates** - Downloadable templates with sample data
- **Batch Processing** - Handle large datasets efficiently

#### Calendar & Scheduling
- **Order Calendar** - Visual monthly calendar view of orders
- **Color-Coded** - Different colors for pickup, dropoff, and fulfilled orders
- **List View** - Alternative chronological list display
- **Day Details** - Click any day to see order details
- **Order Count Badges** - See number of orders at a glance
- **Navigation** - Easy month-to-month navigation

#### Interactive Tutorials
- **Welcome Tour** - Step-by-step introduction for new users
- **Feature Guides** - Tutorials for orders, clients, mobile, analytics, case management
- **Progress Indicators** - Visual progress through tutorial steps
- **Quick Tips** - Helpful tips and keyboard shortcuts
- **First-Visit Prompt** - Automatic tutorial offer for new users

#### Notifications Center
- **In-App Notifications** - Real-time notification feed
- **Email Notifications** - Configurable email alerts
- **Push Notifications** - Browser push notifications support
- **SMS Framework** - Foundation for SMS notifications (Twilio, AWS SNS ready)
- **Notification Settings** - Granular control over notification preferences
- **Filter & Organize** - Filter by type (orders, clients, system)
- **Unread Tracking** - Clear unread count and status

#### CI/CD & Automation
- **GitHub Actions** - Automated testing pipeline
- **Syntax Checking** - PHP syntax validation
- **API Testing** - Automated API endpoint verification
- **Security Scanning** - SQL injection and XSS detection
- **Database Tests** - Automated schema validation

#### API Documentation
- **Complete API Reference** - Full documentation of all endpoints
- **Request/Response Examples** - Code examples for each endpoint
- **Authentication Guide** - Security and auth documentation
- **Error Handling** - Comprehensive error response guide
- **Integration Guide** - Third-party integration instructions

#### Customizable Dashboard
- **Drag & Drop Layouts** - Reorder widgets by dragging them
- **Widget Resizing** - Adjust widget sizes to fit your workflow
- **Persistent Layouts** - Saves your customizations across sessions
- **Smooth Animations** - Polished transitions for all interactions
- **Quick Stats Widget** - View key metrics at a glance
- **Recent Orders Widget** - Monitor latest order activity
- **Quick Actions Widget** - Fast access to common tasks
- **Analytics Summary** - Visual charts and trends
- **Activity Feed** - Real-time system updates
- **Reset to Default** - One-click restore of original layout

### Staff Portal
- **Add Products** - Create products with images, descriptions, inventory, and custom gradient backgrounds
- **Add Workers** - Register workers in the system
- **Create Orders** - Multi-step order creation flow with product selection, client management, and fulfillment tracking
- **View Open Orders** - Monitor orders waiting for supplies
- **View All Orders** - Complete order history
- **Edit Orders** - Update order status with 11+ status options

### 🆕 Mobile Order Systems
- **Primary Mobile Flow** - Streamlined single-page order creation optimized for mobile
- **Client Quick-Add** - Inline client creation with contact information
- **Smart Product Selection** - Alphabetically sorted with quantity badges
- **Order Confirmation** - Tap-to-remove single items, swipe-to-remove all
- **5 Alternative UI Styles** - Different layouts for different workflows:
  - Card-Based: Vibrant gradient cards for visual workflows
  - List View: Compact list with inline +/- controls
  - Grid Categories: Dark theme with category filtering
  - Compact: Minimal single-column with FAB
  - Tabbed: Three-step guided process

### 📋 Case Management System
- **50+ Pre-built Templates** for case documentation:
  - 15 Addiction Support templates
  - 15 Homelessness Services templates
  - 20 Mental Health templates
- **Case Notes** - Document client interactions with template support
- **Follow-up Tracking** - Set and track follow-up dates
- **Confidential Notes** - Mark sensitive information
- **Category Filtering** - Filter by Addiction, Homelessness, Mental Health, General, or Referral

### ⚠️ Error Logging & Monitoring
- **Real-time Error Dashboard** - Monitor system errors as they occur
- **Severity Levels** - Critical, High, Medium, Low prioritization
- **Error Statistics** - Visual dashboard of error trends
- **Resolution Tracking** - Mark errors as resolved
- **Auto-refresh** - Stay updated with 30-second refresh

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

- **Backend**: PHP 7.4+, MySQL 5.7+ (MySQLi + PDO)
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Charts**: Chart.js for data visualization
- **Testing**: GitHub Actions CI/CD
- **Security**: bcrypt password hashing, prepared statements, CSRF protection
- **Features**: Ajax for smooth interactions, responsive design, smooth animations, session-based authentication

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Instructions

#### Quick Setup with Web Interface (Recommended) 🚀

1. **Clone the repository**
   ```bash
   git clone https://github.com/acesonder/Tweakorder.git
   cd Tweakorder
   ```

2. **Start your web server**
   ```bash
   php -S localhost:8000
   ```

3. **Use the Database Import Manager**
   - Navigate to: `http://localhost:8000/import-data.html`
   - Enter your database credentials
   - Test connection
   - Import files in recommended order:
     1. **database_schema.sql** - Creates all tables
     2. **harm_reduction_products.sql** - Adds 80+ harm reduction products
     3. **demo_data.sql** - Loads sample data for testing
   - Or click "Import All (In Order)" for automatic import

4. **Access the application**
   - **Staff Login**: `http://localhost:8000/staff-login.html` (admin/password)
   - **Staff Portal**: `http://localhost:8000/index.html`
   - **Client Portal**: `http://localhost:8000/client-portal.html`
   - **Database Import**: `http://localhost:8000/import-data.html`
   - **Mobile Order**: `http://localhost:8000/mobile-order.html`
   - **Analytics**: `http://localhost:8000/analytics.html`

#### Manual Command Line Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/acesonder/Tweakorder.git
   cd Tweakorder
   ```

2. **Create the database**
   ```bash
   mysql -u root -p < database_schema.sql
   ```

3. **Load harm reduction products**
   ```bash
   mysql -u root -p tweakorder < harm_reduction_products.sql
   ```

4. **Load demo data (optional)**
   ```bash
   mysql -u root -p tweakorder < demo_data.sql
   ```

5. **Configure database connection**
   - Edit `config/database.php` with your MySQL credentials
   - Default settings: host=localhost, user=root, password='', database=tweakorder

6. **Set up file permissions**
   ```bash
   chmod 755 assets/uploads
   chmod 755 harm-reduction-icons
   ```

7. **Start your web server**
   - Point your web server document root to the project directory
   - For development, you can use PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```

8. **Access the application**
   - **Staff Login**: `http://localhost:8000/staff-login.html`
   - **Database Import**: `http://localhost:8000/import-data.html`
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

### Mobile Order Creation Flow (NEW)
1. **Select Client** - Choose from dropdown or tap "+" to create new client
2. **Add Products** - Tap product tiles to add (shows quantity badge)
3. **Tap Next** - Review order on confirmation page
4. **Remove Items** - Tap to remove one, swipe right-to-left to remove all
5. **Confirm Order** - Submit order to system

### Case Management Workflow (NEW)
1. **Select Client** - Filter notes by client or category
2. **Choose Template** - Browse 50+ templates organized by category
3. **Create Note** - Use template or write custom note
4. **Set Follow-up** - Optional follow-up date tracking
5. **Mark Confidential** - Flag sensitive information

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

- 🆕 [**Welcome Guide**](WELCOME_GUIDE.md) - **START HERE!** Complete user guide with screenshots and step-by-step instructions
- [**Dashboard Customization Guide**](DASHBOARD_CUSTOMIZATION_GUIDE.md) - Complete guide for customizing your dashboard
- [**Mobile Features Guide**](MOBILE_FEATURES_GUIDE.md) - Complete guide for new mobile and case management features
- [**Client Portal User Guide**](CLIENT_PORTAL_GUIDE.md) - Complete guide for customers
- [**Staff Workflow Guide**](WORKFLOW_GUIDE.md) - Guide for staff users
- [**HTML Validation Report**](HTML_VALIDATION_REPORT.md) - System validation results

## Database Setup

### Quick Web-Based Setup
1. Navigate to `import-data.html` in your browser
2. Enter database credentials
3. Click "Test Connection"
4. Click "Import All (In Order)" - imports all three SQL files automatically

### SQL Files
- `database_schema.sql` - Complete database structure (300+ lines)
- `harm_reduction_products.sql` - 80+ harm reduction products with SKUs
- `demo_data.sql` - 12 demo clients, 20 orders, 6 locations, sample data

### Assets Included
- 25 harm reduction product icons (SVG) in `harm-reduction-icons/`
- 16 demo product images in `assets/uploads/`
- All assets have placeholder graphics ready for customization

## API Endpoints

### Client APIs
- `GET/POST /api/auth.php` - Authentication (register, login, logout, forgot password)
- `GET/POST /api/client-orders.php` - Client order management
- `GET /api/locations.php` - Pickup/dropoff locations
- `GET /api/schedule.php` - Schedule availability

### Staff APIs
- `GET/POST/PUT/DELETE /api/products.php` - Product management
- `GET/POST /api/clients.php` - Client management (now includes contact fields)
- `GET/POST /api/workers.php` - Worker management
- `GET/POST/PUT /api/orders.php` - Order management

### NEW - Case Management APIs
- `GET/POST /api/case-notes.php` - Case note management
- `GET/POST /api/case-templates.php` - Template management

### NEW - Error Logging API
- `GET/POST/PUT /api/error-logs.php` - Error log management

## Design Features

- Professional gradient designs
- Smooth animations and transitions
- Intuitive user interface
- Real-time updates with Ajax
- Secure authentication system
- Client-specific order viewing
- Comprehensive order status tracking
- **NEW**: Touch-optimized mobile interfaces
- **NEW**: Swipe gestures for item removal
- **NEW**: Case note templates for faster documentation
- **NEW**: Error monitoring and tracking

## Database Schema

The application includes tables for:
- **products** - Product catalog with categories and SKUs
- **clients** - Client accounts with authentication and contact information (phone, email, address)
- **users** - Staff user accounts (Admin, Manager, Worker, Viewer)
- **workers** - Worker registry
- **orders** - Order records with enhanced status options
- **order_items** - Order line items
- **locations** - Pickup/dropoff locations
- **schedule_availability** - Time slot management
- **notifications** - Notification system (future use)
- **sessions** - Session management
- **NEW: error_logs** - System error tracking with severity levels
- **NEW: case_templates** - 50+ pre-built case management templates
- **NEW: case_notes** - Client case documentation and notes
- **NEW: dashboard_layouts** - User-specific dashboard configurations
- **NEW: dashboard_widgets** - Widget positions, sizes, and settings
- **NEW: worker_preferences** - User UI style preferences
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