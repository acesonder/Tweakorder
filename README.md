# Tweak Order Online

A professional, mobile-responsive web application for managing products, workers, clients, and orders with a streamlined user experience.

## Features

- **Add Products** - Create products with images, descriptions, inventory, and custom gradient backgrounds
- **Add Workers** - Register workers in the system
- **Create Orders** - Multi-step order creation flow with product selection, client management, and fulfillment tracking
- **View Open Orders** - Monitor orders waiting for supplies
- **View All Orders** - Complete order history
- **Edit Orders** - Update order fulfillment status

## Technology Stack

- **Backend**: PHP, MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Features**: Ajax for smooth interactions, responsive design, smooth animations

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

3. **Configure database connection**
   - Edit `config/database.php` with your MySQL credentials
   - Default settings: host=localhost, user=root, password='', database=tweakorder

4. **Set up file permissions**
   ```bash
   chmod 755 assets/uploads
   ```

5. **Start your web server**
   - Point your web server document root to the project directory
   - For development, you can use PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```

6. **Access the application**
   - Open your browser and navigate to `http://localhost:8000`

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

### Order Creation Flow
1. **Select Products** - Click product widgets to add to order
2. **Confirm Selection** - Review selected items
3. **Select Client** - Choose existing client or add new one
4. **Set Status** - Mark as fulfilled or waiting for supplies

### Mobile Responsive
The application is fully responsive and works seamlessly on:
- Desktop computers
- Tablets
- Mobile phones

## Design Features

- Professional gradient designs
- Smooth animations and transitions
- No authentication required for streamlined access
- Intuitive user interface
- Real-time updates with Ajax

## Support

For issues or questions, please open an issue on GitHub.