# Customizable Dashboard Setup Guide

This guide covers the setup and usage of the new Customizable Dashboard feature for Tweakorder.

## Overview

The Customizable Dashboard allows administrators to:
- Drag and drop widgets to reorder them
- Resize widgets to fit their needs
- Save layouts that persist across sessions
- Reset to default layout
- Smooth animations for all interactions

## Installation

### 1. Database Setup

Run the dashboard customization SQL schema to create the necessary tables:

```bash
mysql -u root -p tweakorder < dashboard_customization.sql
```

This will create:
- `dashboard_layouts` - Stores user-specific dashboard configurations
- `dashboard_widgets` - Stores individual widget settings and positions

### 2. Verify Installation

Check that the tables were created successfully:

```bash
mysql -u root -p tweakorder -e "SHOW TABLES LIKE 'dashboard_%';"
```

You should see:
```
dashboard_layouts
dashboard_widgets
```

### 3. Test the Feature

1. Log in to the staff dashboard at `http://localhost:8000/staff-dashboard.html`
2. Click on the "Custom Dashboard" quick action
3. You'll be taken to `http://localhost:8000/dashboard-custom.html`

## Features

### Available Widgets

1. **Quick Statistics** - Shows total orders, today's orders, pending orders, products, and clients
2. **Recent Orders** - Displays the most recent orders with status
3. **Quick Actions** - Fast access to common tasks (Add Product, New Order, Analytics, Cases)
4. **Analytics Summary** - Visual charts showing order trends
5. **Activity Feed** - Recent activity in the system

### Customization Options

#### Edit Mode
- Click "✏️ Enable Edit Mode" to enter customization mode
- Drag widgets to reorder them
- Widget borders will appear when in edit mode
- Exit edit mode by clicking "✓ Exit Edit Mode"

#### Resizing Widgets
- In edit mode, click the "-" button to make widgets smaller
- Click the "+" button to make widgets larger
- Widgets can be 3, 6, 8, or 12 columns wide

#### Saving Changes
- After making changes, click "💾 Save Layout"
- Your layout will be saved to the database
- The layout persists across browser sessions

#### Resetting Layout
- Click "🔄 Reset Layout" to restore default widget positions
- Confirms before resetting to prevent accidental data loss

## Widget Sizes

Widgets use a 12-column grid system:
- **w-3**: 1/4 width (3 columns)
- **w-4**: 1/3 width (4 columns)
- **w-6**: 1/2 width (6 columns)
- **w-8**: 2/3 width (8 columns)
- **w-12**: Full width (12 columns)

Heights:
- **h-2**: Small (200px)
- **h-3**: Medium (300px)
- **h-4**: Large (400px)

## API Endpoints

The dashboard uses the following API endpoints:

### GET /api/dashboard.php?action=get_layout
Returns the current user's dashboard layout and widgets.

**Response:**
```json
{
  "success": true,
  "layout": {
    "id": 1,
    "user_id": 1,
    "layout_name": "My Dashboard",
    "grid_columns": 12
  },
  "widgets": [
    {
      "id": 1,
      "widget_type": "quick_stats",
      "widget_title": "Quick Statistics",
      "position_x": 0,
      "position_y": 0,
      "width": 12,
      "height": 2,
      "widget_order": 1,
      "settings": {}
    }
  ]
}
```

### POST /api/dashboard.php (action=save_widgets)
Saves widget positions and settings.

**Request:**
```json
{
  "action": "save_widgets",
  "layout_id": 1,
  "widgets": [...]
}
```

### POST /api/dashboard.php (action=reset_layout)
Resets the dashboard to default layout.

### GET /api/dashboard.php?action=get_widget_data&widget_type={type}
Returns data for a specific widget type.

## Database Schema

### dashboard_layouts Table
```sql
CREATE TABLE dashboard_layouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    layout_name VARCHAR(100) DEFAULT 'My Dashboard',
    is_active BOOLEAN DEFAULT 1,
    grid_columns INT DEFAULT 12,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_active (user_id, is_active)
);
```

### dashboard_widgets Table
```sql
CREATE TABLE dashboard_widgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    layout_id INT NOT NULL,
    widget_type VARCHAR(50) NOT NULL,
    widget_title VARCHAR(100),
    position_x INT DEFAULT 0,
    position_y INT DEFAULT 0,
    width INT DEFAULT 4,
    height INT DEFAULT 3,
    widget_order INT DEFAULT 0,
    settings JSON,
    is_visible BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (layout_id) REFERENCES dashboard_layouts(id) ON DELETE CASCADE
);
```

## Troubleshooting

### Widgets Not Loading
- Check browser console for JavaScript errors
- Verify API endpoint is accessible: `curl http://localhost:8000/api/dashboard.php?action=get_layout`
- Ensure you're logged in as a staff member

### Changes Not Saving
- Check that you clicked the "💾 Save Layout" button
- Verify database connection in `config/database.php`
- Check PHP error logs for API errors

### Default Layout Not Appearing
- Run the SQL schema again to create default widgets
- Check that your user_id exists in the users table
- Manually insert default layout:
  ```sql
  INSERT INTO dashboard_layouts (user_id, layout_name, is_active) VALUES (1, 'My Dashboard', 1);
  ```

## Security Considerations

- Dashboard customization requires staff authentication
- Session-based authentication checks on all API calls
- User can only modify their own layouts
- SQL injection prevention using prepared statements
- XSS prevention in widget rendering

## Future Enhancements

Potential additions to the customizable dashboard:
- Widget library with add/remove functionality
- Layout templates (compact, analytics-focused, mobile-optimized)
- Export/import dashboard configurations
- Shared team layouts
- Custom widget creation
- Color themes for widgets
- Advanced widget settings panel
- Keyboard shortcuts for power users

## Support

For issues or questions about the customizable dashboard:
1. Check this guide first
2. Review the API documentation
3. Check server and browser console logs
4. Open an issue on GitHub with reproduction steps
