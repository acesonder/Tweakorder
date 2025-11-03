-- Dashboard Customization Schema
-- This schema supports customizable dashboard layouts with drag-and-drop, reordering, and resizing

-- Dashboard Layouts Table - Stores user-specific dashboard configurations
CREATE TABLE IF NOT EXISTS dashboard_layouts (
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

-- Dashboard Widgets Table - Stores individual widget configurations
CREATE TABLE IF NOT EXISTS dashboard_widgets (
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

-- Insert default dashboard layout for existing users
INSERT INTO dashboard_layouts (user_id, layout_name, is_active, grid_columns)
SELECT id, 'Default Layout', 1, 12 FROM users WHERE id NOT IN (SELECT user_id FROM dashboard_layouts);

-- Insert default widgets for the admin user (id=1) if they exist
INSERT INTO dashboard_widgets (layout_id, widget_type, widget_title, position_x, position_y, width, height, widget_order, settings)
SELECT 
    dl.id,
    'quick_stats',
    'Quick Statistics',
    0, 0, 12, 2, 1,
    '{"show_orders": true, "show_products": true, "show_clients": true}'
FROM dashboard_layouts dl
WHERE dl.user_id = 1 AND dl.is_active = 1
AND NOT EXISTS (SELECT 1 FROM dashboard_widgets WHERE layout_id = dl.id AND widget_type = 'quick_stats');

INSERT INTO dashboard_widgets (layout_id, widget_type, widget_title, position_x, position_y, width, height, widget_order, settings)
SELECT 
    dl.id,
    'recent_orders',
    'Recent Orders',
    0, 2, 6, 4, 2,
    '{"limit": 5}'
FROM dashboard_layouts dl
WHERE dl.user_id = 1 AND dl.is_active = 1
AND NOT EXISTS (SELECT 1 FROM dashboard_widgets WHERE layout_id = dl.id AND widget_type = 'recent_orders');

INSERT INTO dashboard_widgets (layout_id, widget_type, widget_title, position_x, position_y, width, height, widget_order, settings)
SELECT 
    dl.id,
    'quick_actions',
    'Quick Actions',
    6, 2, 6, 4, 3,
    '{}'
FROM dashboard_layouts dl
WHERE dl.user_id = 1 AND dl.is_active = 1
AND NOT EXISTS (SELECT 1 FROM dashboard_widgets WHERE layout_id = dl.id AND widget_type = 'quick_actions');

INSERT INTO dashboard_widgets (layout_id, widget_type, widget_title, position_x, position_y, width, height, widget_order, settings)
SELECT 
    dl.id,
    'analytics_summary',
    'Analytics Summary',
    0, 6, 8, 3, 4,
    '{"chart_type": "bar"}'
FROM dashboard_layouts dl
WHERE dl.user_id = 1 AND dl.is_active = 1
AND NOT EXISTS (SELECT 1 FROM dashboard_widgets WHERE layout_id = dl.id AND widget_type = 'analytics_summary');

INSERT INTO dashboard_widgets (layout_id, widget_type, widget_title, position_x, position_y, width, height, widget_order, settings)
SELECT 
    dl.id,
    'activity_feed',
    'Recent Activity',
    8, 6, 4, 3, 5,
    '{"limit": 10}'
FROM dashboard_layouts dl
WHERE dl.user_id = 1 AND dl.is_active = 1
AND NOT EXISTS (SELECT 1 FROM dashboard_widgets WHERE layout_id = dl.id AND widget_type = 'activity_feed');
