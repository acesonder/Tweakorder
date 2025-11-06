# 🎉 Welcome to Tweakorder - Complete User Guide

**Version 2.0** | Last Updated: November 2024

---

## 📚 Table of Contents

1. [Getting Started](#getting-started)
2. [Database Setup & Import](#database-setup--import)
3. [Staff Portal Overview](#staff-portal-overview)
4. [Client Portal](#client-portal)
5. [Key Features Guide](#key-features-guide)
6. [Mobile Interface](#mobile-interface)
7. [Administration](#administration)
8. [Troubleshooting](#troubleshooting)

---

## 🚀 Getting Started

### What is Tweakorder?

Tweakorder is a comprehensive web-based order management system designed for harm reduction organizations, social services, and community support programs. It streamlines the process of managing products, clients, orders, and staff with a focus on ease of use and accessibility.

### Key Capabilities

- **Product Management** - Track inventory for harm reduction supplies and general products
- **Client Management** - Maintain client records with privacy and security
- **Order Processing** - Create, track, and fulfill orders with multiple status options
- **Staff Portal** - Role-based access for administrators, managers, and workers
- **Client Portal** - Self-service portal for clients to place and track orders
- **Analytics & Reporting** - Visual dashboards and detailed reports
- **Mobile Optimized** - Responsive design works on phones, tablets, and desktops

---

## 💾 Database Setup & Import

### Step 1: Access the Import Manager

Navigate to: **`import-data.html`**

The Database Import Manager provides a user-friendly interface for setting up your database.

### Step 2: Configure Database Credentials

Enter your database connection details:

- **Database Host**: Usually `localhost` (or your server IP)
- **Database User**: Your MySQL username (default: `root`)
- **Database Password**: Your MySQL password (leave empty if none)
- **Database Name**: `tweakorder` (recommended)

Click **"Test Database Connection"** to verify your credentials.

### Step 3: Import SQL Files in Order

The system includes three SQL files that should be imported in this order:

#### 1. Database Schema (`database_schema.sql`)
**Import First** - Creates all database tables and structure
- Products, Clients, Orders, Users tables
- Sessions, Notifications, Audit logs
- Case management tables
- Chat system tables

**Actions Available:**
- ✓ **Verify** - Check if file exists and is readable
- 🧪 **Test** - Validate SQL syntax without importing
- 📥 **Import** - Import into your database

#### 2. Harm Reduction Products (`harm_reduction_products.sql`)
**Import Second** - Adds comprehensive product catalog
- 80+ harm reduction products
- Naloxone and overdose prevention supplies
- Injection supplies (syringes, needles, cookers, filters)
- Safer sex supplies (condoms, lubricant, dental dams)
- Testing supplies (fentanyl test strips, drug checking)
- Smoking supplies (safe smoking kits, glass stems, screens)
- Wound care and first aid
- Personal protective equipment
- Vitamins and nutrition support
- Educational materials and resource cards

#### 3. Demo Data (`demo_data.sql`)
**Import Third** - Populates with sample data for testing
- 12 demo clients with contact information
- 20 sample orders in various statuses
- 15 general products (office supplies, electronics, furniture)
- 6 locations with pickup/dropoff options
- Schedule availability for each location
- Sample notifications and activity logs
- Case notes and templates

### Quick Import Option

Click **"Import All (In Order)"** to automatically import all three files in the recommended sequence.

### Troubleshooting Database Import

**Connection Failed:**
- Verify MySQL/MariaDB is running
- Check credentials are correct
- Ensure user has necessary permissions

**Import Errors:**
- Import files in the correct order
- Clear database and start fresh if needed
- Check error logs for specific issues

**Table Already Exists:**
- Use "Clear Database" to remove all tables
- Or manually drop tables in MySQL

---

## 🏢 Staff Portal Overview

### Accessing the Staff Portal

1. Navigate to **`staff-login.html`**
2. Use demo credentials:
   - **Admin**: `admin` / `password`
   - **Manager**: `manager` / `password`
   - **Worker**: `worker` / `password`
3. After login, you'll be redirected to the staff dashboard

### Staff Dashboard (`staff-dashboard.html`)

The main hub for all staff activities with:

**Quick Actions:**
- Create New Order
- Add Product
- Add Client
- View All Orders

**Statistics:**
- Active Orders
- Pending Orders
- Today's Orders
- Low Stock Items

**Recent Activity:**
- Latest orders created
- Client registrations
- Product updates

### Main Navigation

Access all features from the **index.html** page:

- 📦 **Add Product** - Create new products
- 👤 **Add Worker** - Register staff members
- 🛒 **Create Order** - Multi-step order creation
- 📋 **Open Orders** - View pending orders
- 📊 **All Orders** - Complete order history
- ✏️ **Edit Orders** - Update order status
- 📱 **Mobile Order** - Streamlined mobile interface
- 👥 **Client Management** - Manage client records
- 📈 **Analytics** - Visual reports and insights
- 📅 **Calendar** - Order calendar view
- 💬 **Chat** - Messaging system
- 📝 **Case Management** - Client case notes
- ⚙️ **Settings** - System configuration

---

## 👤 Client Portal

### Client Registration

New clients can register at **`client-portal.html`**:

1. Fill out registration form:
   - Personal information
   - Contact details
   - Emergency contact
2. Create username and password
3. Set security question for account recovery

### Client Dashboard (`client-dashboard.html`)

Clients have access to:

**Personal Information:**
- View and update contact details
- Manage account settings

**Order Management:**
- Place new orders
- View order history
- Track order status
- View scheduled pickup/dropoff times

**Notifications:**
- Order confirmations
- Status updates
- System messages

### Placing an Order

From the client dashboard:

1. Click **"Place New Order"**
2. Select products from catalog
3. Choose quantity for each item
4. Select pickup or dropoff
5. Choose location and schedule time
6. Add any special instructions
7. Submit order

Orders are immediately visible to staff for processing.

---

## 🎯 Key Features Guide

### 1. Product Management

**Add Product** (`add-product.html`):
- Product name and description
- SKU for inventory tracking
- Category assignment
- Initial inventory count
- Product image upload
- Custom gradient background
- Mark as favorite for quick access

**Favorite Products:**
- Mark frequently used items as favorites
- Filter order creation to show only favorites
- Visual ★ indicator on favorite items

### 2. Order Creation (`create-order.html`)

Multi-step process:

**Step 1: Client Selection**
- Search existing clients
- Quick add new client inline
- View client contact information

**Step 2: Product Selection**
- Browse all products or filter by category
- View inventory levels
- Add multiple products with quantities
- Toggle "Show Favorites Only" for quick selection

**Step 3: Order Details**
- Choose pickup or dropoff
- Select location from available options
- Schedule date and time
- Add special instructions or notes

**Step 4: Review & Submit**
- Review all order details
- Confirm client information
- Submit for processing

### 3. Order Status Management (`edit-orders.html`)

11 Status Options:
- **Processing** - Initial state, order received
- **Accepted** - Order confirmed and being prepared
- **Waiting for Supplies** - Items not currently in stock
- **Waiting for Customer** - Awaiting client response
- **Further Info Needed** - Additional details required
- **Problems Unknown** - Issue needs investigation
- **Ready for Pickup** - Order prepared, ready to collect
- **Ready for Dropoff** - Order prepared for delivery
- **Awaiting Scheduled Time** - Order ready, waiting for appointment
- **Fulfilled** - Order completed successfully
- **Cancelled** - Order cancelled

### 4. Analytics Dashboard (`analytics.html`)

**Key Metrics:**
- Total orders processed
- Active client count
- Popular products
- Revenue tracking (if applicable)

**Visual Charts:**
- Order trends over time (line chart)
- Status distribution (pie chart)
- Top products (bar chart)
- Client activity patterns

**Time Filters:**
- Today
- This Week
- This Month
- This Year
- Custom Date Range

**Export Options:**
- Download reports as CSV
- Print reports
- Share via email

### 5. Calendar View (`calendar.html`)

**Features:**
- Monthly calendar grid
- Color-coded orders:
  - 🔵 Blue: Pickup orders
  - 🟢 Green: Dropoff orders
  - 🟡 Yellow: Fulfilled orders
- Order count badges on each day
- Click any day to see order details
- Navigate between months easily

**List View:**
- Alternative chronological view
- See all orders for a date range
- Sort by date, status, client

### 6. Bulk Import/Export (`bulk-import-export.html`)

**Import Features:**
- CSV upload with drag & drop
- Preview data before importing
- Validation and error reporting
- Progress tracking

**Export Features:**
- Export products to CSV
- Export clients to CSV
- Export orders to CSV
- Download templates with examples

**Supported Entities:**
- Products
- Clients
- Orders
- Workers

### 7. Case Management (`case-management.html`)

**Case Notes:**
- Document client interactions
- Category assignment (addiction, homelessness, mental health, etc.)
- Follow-up date tracking
- Confidentiality flags

**Case Templates:**
- Pre-written templates for common scenarios
- Standardize documentation
- Save time on routine notes

**Search & Filter:**
- Find case notes by client
- Filter by category or date
- Export case notes for reporting

### 8. Chat System (`chat.html`)

**Direct Messaging:**
- Staff-to-staff communication
- Staff-to-client messaging
- Real-time updates

**Features:**
- Unread message indicators
- Message history
- File attachments (if enabled)
- Notification on new messages

### 9. Mobile Interfaces

**Mobile Order** (`mobile-order.html`):
- Single-page order creation
- Optimized for touch
- Quick client lookup
- Streamlined product selection

**Alternative Mobile Styles:**
- Card-based layout (`mobile-style-card.html`)
- Compact view (`mobile-style-compact.html`)
- Grid categories (`mobile-style-grid.html`)
- List view (`mobile-style-list.html`)
- Tabbed interface (`mobile-style-tabbed.html`)

### 10. Notifications System (`notifications.html`)

**Notification Types:**
- Order confirmations
- Status changes
- Low stock alerts
- System messages

**Features:**
- Unread count badge
- Mark as read/unread
- Filter by type
- Clear all notifications

**Settings:**
- Email notifications (configurable)
- Push notifications (if supported)
- SMS notifications (framework ready)

---

## 📱 Mobile Interface

### Responsive Design

All pages automatically adapt to screen size:
- **Desktop**: Full navigation and multi-column layouts
- **Tablet**: Optimized two-column layouts
- **Mobile**: Single-column, touch-optimized interface

### Mobile-Specific Features

- Large touch targets (minimum 44x44 pixels)
- Swipe gestures support
- Pull-to-refresh (where applicable)
- Auto-save on field changes
- Offline mode indicators

### Mobile Order Flow

1. Open `mobile-order.html` on your device
2. Search or add client
3. Select products with + / - buttons
4. Choose fulfillment details
5. Submit with one tap

---

## ⚙️ Administration

### Admin Panel (`admin-panel.html`)

**User Management:**
- Create staff accounts
- Assign roles and permissions
- Deactivate/reactivate users
- Reset passwords

**System Settings:**
- Configure locations
- Set schedule availability
- Manage product categories
- System-wide preferences

**Security Settings** (`security-settings.html`):
- Password policies
- Session timeout settings
- Two-factor authentication (if enabled)
- Audit log review

### Backup Management (`backup-management.html`)

**Database Backups:**
- Schedule automatic backups
- Manual backup creation
- Download backup files
- Restore from backup

**Best Practices:**
- Daily automatic backups
- Store backups off-site
- Test restore procedures regularly
- Keep at least 30 days of backups

### Error Logs (`error-logs.html`)

**Monitor System Health:**
- View all error logs
- Filter by severity (low, medium, high, critical)
- Mark errors as resolved
- Track resolution status

**Error Types:**
- Application errors
- Database errors
- Authentication failures
- API errors

### Reports (`admin-reports.html`)

**Available Reports:**
- Order summary reports
- Client activity reports
- Inventory reports
- Staff performance reports
- Financial reports (if applicable)

**Export Formats:**
- PDF
- CSV
- Excel
- Print-friendly HTML

---

## 🔧 Troubleshooting

### Common Issues

#### Cannot Login

**Problem**: Login fails with correct credentials
**Solutions:**
- Verify username is exactly correct (case-sensitive)
- Clear browser cache and cookies
- Check if user account is active
- Reset password if necessary

#### Database Connection Error

**Problem**: "Database connection failed" message
**Solutions:**
- Verify MySQL/MariaDB is running
- Check config/database.php credentials
- Ensure database exists
- Verify user has necessary permissions

#### Orders Not Saving

**Problem**: Order creation fails or doesn't save
**Solutions:**
- Check browser console for JavaScript errors
- Verify all required fields are filled
- Ensure database connection is working
- Check that products and clients exist

#### Images Not Loading

**Problem**: Product images show as broken
**Solutions:**
- Verify image file exists in assets/uploads/
- Check file permissions
- Ensure image path is correct
- Use supported formats (JPG, PNG, GIF, SVG)

#### Slow Performance

**Problem**: Pages load slowly
**Solutions:**
- Clear browser cache
- Optimize database (run maintenance)
- Check server resources
- Review error logs for issues

### Getting Help

**Documentation:**
- README.md - Overview and installation
- API_DOCUMENTATION.md - API reference
- USER_GUIDE.md - This guide
- Various feature-specific guides

**Support:**
- Check GitHub issues for known problems
- Create a new issue for bugs
- Include error messages and steps to reproduce

---

## 📊 Demo Data Overview

### Demo Users

**Staff Accounts:**
- **admin** / password - Full admin access
- **manager** / password - Manager permissions
- **worker** / password - Worker permissions

**Client Accounts:**
- **jsmith** / password - John Smith
- **mgarcia** / password - Maria Garcia
- **djohnson** / password - David Johnson
- And 9 more demo clients

### Demo Orders

20 sample orders in various states:
- Processing, accepted, waiting orders
- Ready for pickup/dropoff
- Fulfilled orders for analytics
- Cancelled orders for testing

### Demo Products

**General Products** (15 items):
- Office supplies
- Electronics
- Furniture
- Books
- Accessories

**Harm Reduction Products** (80+ items):
- Naloxone kits
- Injection supplies
- Safer sex supplies
- Testing supplies
- Smoking supplies
- Wound care
- PPE
- And more...

### Demo Locations

6 locations with different schedules:
- Main Distribution Center (M-F 9-5)
- Downtown Pickup Point (M/W/F 10-6)
- East Side Dropoff (Tu/Th 12-8)
- North Branch Office (7 days, varied hours)
- West Community Center
- South Mobile Unit

---

## 🎓 Training Recommendations

### For New Staff

1. **Day 1**: Familiarize with dashboard and navigation
2. **Day 2**: Practice order creation with demo data
3. **Day 3**: Learn client management and case notes
4. **Day 4**: Review analytics and reporting
5. **Day 5**: Master order status updates and fulfillment

### For Administrators

1. Set up database and import data
2. Configure locations and schedules
3. Create staff accounts with appropriate roles
4. Set up backup procedures
5. Review security settings
6. Train staff on system usage

### Best Practices

- **Regular Backups**: Daily automated backups
- **Data Privacy**: Follow HIPAA/privacy guidelines
- **Access Control**: Use role-based permissions
- **Documentation**: Keep client notes up-to-date
- **Inventory**: Regular stock counts
- **Reporting**: Weekly review of analytics

---

## 🚀 Next Steps

1. ✅ **Import Database**: Use import-data.html to set up your database
2. ✅ **Create Staff Accounts**: Add your team members
3. ✅ **Configure Locations**: Set up your pickup/dropoff locations
4. ✅ **Add Products**: Import or add your product catalog
5. ✅ **Test System**: Create test orders and practice workflow
6. ✅ **Train Staff**: Ensure everyone knows how to use the system
7. ✅ **Go Live**: Start processing real orders!

---

## 📝 Additional Resources

- **Feature Showcase**: FEATURE_SHOWCASE.md
- **API Documentation**: API_DOCUMENTATION.md
- **Mobile Features**: MOBILE_FEATURES_GUIDE.md
- **Chat System**: CHAT_SYSTEM_DOCUMENTATION.md
- **Dashboard Customization**: DASHBOARD_CUSTOMIZATION_GUIDE.md
- **Harm Reduction Products**: HARM_REDUCTION_PRODUCTS.md

---

## 📧 Support

For additional help, issues, or feature requests:
- GitHub Repository: [Tweakorder](https://github.com/acesonder/Tweakorder)
- Documentation: Check the /docs folder
- Community: Join discussions in GitHub Issues

---

**Thank you for using Tweakorder! Together, we're making a difference in harm reduction and community support.** 💙
