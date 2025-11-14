-- Order Workflow Customization Schema
-- This schema supports customizable order creation workflows for staff members

-- Order Workflow Templates Table - Global templates for different types of workflows
CREATE TABLE IF NOT EXISTS order_workflow_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_default BOOLEAN DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Order Workflow Steps Table - Defines the steps in a workflow
CREATE TABLE IF NOT EXISTS order_workflow_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT NOT NULL,
    step_order INT NOT NULL,
    step_type ENUM('product_selection', 'client_selection', 'location_selection', 
                   'schedule_selection', 'status_selection', 'custom_fields', 
                   'confirmation', 'notes') NOT NULL,
    step_title VARCHAR(100) NOT NULL,
    is_required BOOLEAN DEFAULT 1,
    is_visible BOOLEAN DEFAULT 1,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES order_workflow_templates(id) ON DELETE CASCADE,
    INDEX idx_template_order (template_id, step_order)
);

-- User Workflow Preferences Table - Staff-specific workflow customizations
CREATE TABLE IF NOT EXISTS user_workflow_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_id INT,
    custom_settings JSON,
    product_display_mode ENUM('grid', 'list', 'compact', 'card', 'favorites-first') DEFAULT 'grid',
    product_sort_order ENUM('name', 'recent', 'popular', 'custom', 'category') DEFAULT 'name',
    show_product_images BOOLEAN DEFAULT 1,
    show_product_descriptions BOOLEAN DEFAULT 1,
    quick_order_enabled BOOLEAN DEFAULT 0,
    default_order_status VARCHAR(50) DEFAULT 'processing',
    auto_advance_steps BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES order_workflow_templates(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_workflow (user_id)
);

-- Product Display Customization Table - User-specific product ordering and visibility
CREATE TABLE IF NOT EXISTS user_product_display (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    display_order INT DEFAULT 0,
    is_pinned BOOLEAN DEFAULT 0,
    is_hidden BOOLEAN DEFAULT 0,
    custom_label VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user_order (user_id, display_order)
);

-- Order Quick Templates Table - Saved order templates for common orders
CREATE TABLE IF NOT EXISTS order_quick_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_name VARCHAR(100) NOT NULL,
    description TEXT,
    client_id INT,
    product_items JSON NOT NULL,
    default_status VARCHAR(50),
    default_location_id INT,
    other_settings JSON,
    use_count INT DEFAULT 0,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (default_location_id) REFERENCES locations(id) ON DELETE SET NULL,
    INDEX idx_user_templates (user_id, last_used_at)
);

-- Client Order Preferences Table - For client-specific customization
CREATE TABLE IF NOT EXISTS client_order_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    product_display_mode ENUM('grid', 'list', 'compact', 'card') DEFAULT 'grid',
    favorite_products JSON,
    preferred_location_id INT,
    preferred_pickup_method ENUM('pickup', 'dropoff') DEFAULT 'pickup',
    custom_settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (preferred_location_id) REFERENCES locations(id) ON DELETE SET NULL,
    UNIQUE KEY unique_client_prefs (client_id)
);

-- Order Workflow Field Customization Table - Custom fields for orders
CREATE TABLE IF NOT EXISTS order_custom_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_label VARCHAR(100) NOT NULL,
    field_type ENUM('text', 'textarea', 'number', 'date', 'select', 'checkbox', 'radio') NOT NULL,
    field_options JSON,
    is_required BOOLEAN DEFAULT 0,
    display_order INT DEFAULT 0,
    validation_rules JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES order_workflow_templates(id) ON DELETE CASCADE,
    INDEX idx_template_fields (template_id, display_order)
);

-- Insert default workflow template
INSERT INTO order_workflow_templates (name, description, is_default, created_by) VALUES
('Standard Order Workflow', 'Default workflow with all standard steps', 1, 1);

-- Insert default workflow steps
SET @template_id = LAST_INSERT_ID();

INSERT INTO order_workflow_steps (template_id, step_order, step_type, step_title, is_required, settings) VALUES
(@template_id, 1, 'product_selection', 'Select Products', 1, 
 '{"allow_favorites_filter": true, "show_inventory": true, "enable_search": true}'),
(@template_id, 2, 'client_selection', 'Select Client', 1, 
 '{"allow_new_client": true, "show_recent_clients": true, "enable_search": true}'),
(@template_id, 3, 'location_selection', 'Choose Location', 0, 
 '{"show_only_active": true, "remember_last_location": true}'),
(@template_id, 4, 'schedule_selection', 'Schedule Pickup/Dropoff', 0, 
 '{"allow_immediate": true, "show_availability": true}'),
(@template_id, 5, 'notes', 'Additional Instructions', 0, 
 '{"placeholder": "Enter any special instructions or notes"}'),
(@template_id, 6, 'status_selection', 'Set Order Status', 1, 
 '{"default_status": "processing", "available_statuses": ["processing", "fulfilled", "waiting_for_supplies"]}'),
(@template_id, 7, 'confirmation', 'Review & Confirm', 1, 
 '{"show_summary": true, "allow_edit": true}');

-- Create default preferences for existing users
INSERT INTO user_workflow_preferences (user_id, template_id, product_display_mode, product_sort_order)
SELECT id, @template_id, 'grid', 'name' FROM users 
WHERE id NOT IN (SELECT user_id FROM user_workflow_preferences);

-- Create streamlined workflow template
INSERT INTO order_workflow_templates (name, description, is_default, created_by) VALUES
('Quick Order Workflow', 'Simplified workflow for experienced staff', 0, 1);

SET @quick_template_id = LAST_INSERT_ID();

INSERT INTO order_workflow_steps (template_id, step_order, step_type, step_title, is_required, settings) VALUES
(@quick_template_id, 1, 'product_selection', 'Select Products', 1, 
 '{"allow_favorites_filter": true, "default_to_favorites": true, "compact_view": true}'),
(@quick_template_id, 2, 'client_selection', 'Select Client', 1, 
 '{"show_recent_clients": true, "quick_search": true}'),
(@quick_template_id, 3, 'status_selection', 'Complete Order', 1, 
 '{"default_status": "fulfilled", "available_statuses": ["fulfilled", "waiting_for_supplies"]}');

-- Create detailed workflow template for new staff or training
INSERT INTO order_workflow_templates (name, description, is_default, created_by) VALUES
('Detailed Training Workflow', 'Comprehensive workflow with guidance for new staff', 0, 1);

SET @detailed_template_id = LAST_INSERT_ID();

INSERT INTO order_workflow_steps (template_id, step_order, step_type, step_title, is_required, settings) VALUES
(@detailed_template_id, 1, 'product_selection', 'Select Products', 1, 
 '{"show_descriptions": true, "show_inventory": true, "enable_search": true, "help_text": "Select all products the client needs"}'),
(@detailed_template_id, 2, 'client_selection', 'Select or Add Client', 1, 
 '{"allow_new_client": true, "require_complete_info": true, "help_text": "Search for existing client or add new"}'),
(@detailed_template_id, 3, 'location_selection', 'Choose Pickup Location', 1, 
 '{"show_only_active": true, "show_location_details": true, "help_text": "Select where client will receive items"}'),
(@detailed_template_id, 4, 'schedule_selection', 'Schedule Pickup Time', 1, 
 '{"show_availability": true, "require_scheduling": true, "help_text": "Choose when client will pick up order"}'),
(@detailed_template_id, 5, 'notes', 'Additional Instructions', 0, 
 '{"placeholder": "Any special instructions, allergies, or notes", "help_text": "Document any important information"}'),
(@detailed_template_id, 6, 'status_selection', 'Set Initial Status', 1, 
 '{"default_status": "processing", "show_status_descriptions": true}'),
(@detailed_template_id, 7, 'confirmation', 'Review All Details', 1, 
 '{"show_complete_summary": true, "require_confirmation": true}');
