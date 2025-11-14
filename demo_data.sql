-- =============================================
-- Tweakorder Demo Data
-- Comprehensive demo content for testing and showcasing
-- =============================================
-- Import this AFTER database_schema.sql and harm_reduction_products.sql

USE tweakorder;

-- Ensure products table has all required columns for compatibility
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS category VARCHAR(100) 
AFTER background_color;

ALTER TABLE products 
ADD COLUMN IF NOT EXISTS sku VARCHAR(100) 
AFTER category;

ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_favorite BOOLEAN DEFAULT 0 
AFTER sku;

-- =============================================
-- DEMO PRODUCTS (General Inventory)
-- =============================================

INSERT INTO products (name, description, image, inventory, background_color, category, sku, is_favorite) VALUES
-- Office Supplies
('Premium Widget', 'High-quality widget for professional use. Durable construction with lifetime warranty.', 'assets/uploads/widget.jpg', 50, 'gradient-1', 'Office Supplies', 'OFF-WID-001', 1),
('Basic Gadget', 'Essential gadget for everyday tasks. Cost-effective and reliable.', 'assets/uploads/gadget.jpg', 100, 'gradient-5', 'Office Supplies', 'OFF-GAD-001', 1),
('Deluxe Kit', 'Complete kit with everything you need. All-in-one solution for professionals.', 'assets/uploads/kit.jpg', 25, 'gradient-7', 'Office Supplies', 'OFF-KIT-001', 0),
('Standard Tool', 'Reliable tool for general purposes. Industry-standard quality.', 'assets/uploads/tool.jpg', 75, 'gradient-10', 'Tools', 'TOO-STD-001', 1),
('Advanced Module', 'Advanced module with extra features. Cutting-edge technology.', 'assets/uploads/module.jpg', 30, 'gradient-3', 'Electronics', 'ELE-MOD-001', 0),

-- Technology
('Laptop Pro 15"', 'Professional-grade laptop with high-performance specs. Perfect for demanding workloads.', 'assets/uploads/laptop.jpg', 15, 'gradient-2', 'Electronics', 'ELE-LAP-001', 1),
('Wireless Mouse', 'Ergonomic wireless mouse with precision tracking. 2-year battery life.', 'assets/uploads/mouse.jpg', 80, 'gradient-4', 'Electronics', 'ELE-MOU-001', 0),
('USB-C Hub', '7-in-1 USB-C hub with multiple ports. Supports 4K display output.', 'assets/uploads/hub.jpg', 45, 'gradient-6', 'Electronics', 'ELE-HUB-001', 0),
('Bluetooth Headset', 'Noise-canceling Bluetooth headset. 20-hour battery life.', 'assets/uploads/headset.jpg', 35, 'gradient-8', 'Electronics', 'ELE-HED-001', 1),

-- Furniture
('Office Chair Deluxe', 'Ergonomic office chair with lumbar support. Height-adjustable with 360° swivel.', 'assets/uploads/chair.jpg', 20, 'gradient-9', 'Furniture', 'FUR-CHA-001', 0),
('Standing Desk', 'Electric height-adjustable standing desk. Memory presets for easy adjustment.', 'assets/uploads/desk.jpg', 12, 'gradient-11', 'Furniture', 'FUR-DSK-001', 1),
('Monitor Stand', 'Wooden monitor stand with storage drawer. Raises screen to eye level.', 'assets/uploads/stand.jpg', 40, 'gradient-12', 'Furniture', 'FUR-STA-001', 0),

-- Books & Education
('Programming Guide', 'Comprehensive programming guide for beginners. 500+ pages of tutorials.', 'assets/uploads/book.jpg', 60, 'gradient-13', 'Books', 'BOO-PRO-001', 0),
('Design Principles', 'Modern design principles handbook. Full-color illustrations.', 'assets/uploads/design.jpg', 40, 'gradient-14', 'Books', 'BOO-DES-001', 0),

-- Accessories
('Water Bottle Insulated', 'Stainless steel insulated water bottle. Keeps drinks cold for 24 hours.', 'assets/uploads/bottle.jpg', 90, 'gradient-15', 'Accessories', 'ACC-BOT-001', 1),
('Backpack Professional', 'Professional backpack with laptop compartment. Water-resistant material.', 'assets/uploads/backpack.jpg', 35, 'gradient-1', 'Accessories', 'ACC-BAC-001', 0);

-- =============================================
-- DEMO CLIENTS
-- =============================================

INSERT INTO clients (first_name, last_name, date_of_birth, phone, email, address, emergency_contact, username, password_hash) VALUES
('John', 'Smith', '1985-03-15', '555-0101', 'john.smith@email.com', '123 Main St, Apt 4B, Springfield, ST 12345', 'Jane Smith - 555-0102', 'jsmith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Maria', 'Garcia', '1990-07-22', '555-0103', 'maria.garcia@email.com', '456 Oak Ave, Springfield, ST 12345', 'Carlos Garcia - 555-0104', 'mgarcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('David', 'Johnson', '1978-11-08', '555-0105', 'david.j@email.com', '789 Pine Rd, Springfield, ST 12345', 'Sarah Johnson - 555-0106', 'djohnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Lisa', 'Williams', '1995-05-30', '555-0107', 'lisa.w@email.com', '321 Elm St, Springfield, ST 12345', 'Mike Williams - 555-0108', 'lwilliams', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Robert', 'Brown', '1982-09-17', '555-0109', 'robert.brown@email.com', '654 Maple Dr, Springfield, ST 12345', 'Jennifer Brown - 555-0110', 'rbrown', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jennifer', 'Davis', '1988-12-03', '555-0111', 'jen.davis@email.com', '987 Cedar Ln, Springfield, ST 12345', 'Tom Davis - 555-0112', 'jdavis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Michael', 'Martinez', '1992-02-28', '555-0113', 'mike.m@email.com', '147 Birch Way, Springfield, ST 12345', 'Anna Martinez - 555-0114', 'mmartinez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Sarah', 'Anderson', '1986-08-19', '555-0115', 'sarah.a@email.com', '258 Spruce Ct, Springfield, ST 12345', 'Chris Anderson - 555-0116', 'sanderson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('James', 'Taylor', '1980-04-12', '555-0117', 'james.taylor@email.com', '369 Willow Rd, Springfield, ST 12345', 'Emma Taylor - 555-0118', 'jtaylor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Patricia', 'Thomas', '1993-10-25', '555-0119', 'patricia.t@email.com', '741 Ash Blvd, Springfield, ST 12345', 'Kevin Thomas - 555-0120', 'pthomas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Christopher', 'White', '1987-06-07', '555-0121', 'chris.white@email.com', '852 Hickory St, Springfield, ST 12345', 'Amanda White - 555-0122', 'cwhite', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Amanda', 'Harris', '1991-01-14', '555-0123', 'amanda.h@email.com', '963 Poplar Ave, Springfield, ST 12345', 'Daniel Harris - 555-0124', 'aharris', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note: All demo client passwords are "password"

-- =============================================
-- DEMO WORKERS
-- =============================================

INSERT INTO workers (first_name, last_name) VALUES
('Alice', 'Manager'),
('Bob', 'Technician'),
('Carol', 'Supervisor'),
('Dave', 'Coordinator'),
('Emma', 'Specialist'),
('Frank', 'Assistant');

-- =============================================
-- DEMO LOCATIONS
-- =============================================

INSERT INTO locations (name, address, type, is_active) VALUES
('Main Distribution Center', '123 Main St, Springfield, ST 12345', 'both', 1),
('Downtown Pickup Point', '456 Downtown Ave, Springfield, ST 12345', 'pickup', 1),
('East Side Dropoff', '789 East Rd, Springfield, ST 12345', 'dropoff', 1),
('North Branch Office', '321 North Blvd, Springfield, ST 12345', 'both', 1),
('West Community Center', '654 West Park Dr, Springfield, ST 12345', 'both', 1),
('South Mobile Unit', '987 South Highway, Springfield, ST 12345', 'both', 1);

-- =============================================
-- DEMO SCHEDULE AVAILABILITY
-- =============================================

INSERT INTO schedule_availability (day_of_week, start_time, end_time, location_id, is_active) VALUES
-- Main Distribution Center (Location 1) - Open all weekdays
('Monday', '09:00:00', '17:00:00', 1, 1),
('Tuesday', '09:00:00', '17:00:00', 1, 1),
('Wednesday', '09:00:00', '17:00:00', 1, 1),
('Thursday', '09:00:00', '17:00:00', 1, 1),
('Friday', '09:00:00', '17:00:00', 1, 1),

-- Downtown Pickup Point (Location 2) - MWF only
('Monday', '10:00:00', '18:00:00', 2, 1),
('Wednesday', '10:00:00', '18:00:00', 2, 1),
('Friday', '10:00:00', '18:00:00', 2, 1),

-- East Side Dropoff (Location 3) - Tue/Thu only
('Tuesday', '12:00:00', '20:00:00', 3, 1),
('Thursday', '12:00:00', '20:00:00', 3, 1),

-- North Branch (Location 4) - Full week including weekends
('Monday', '08:00:00', '16:00:00', 4, 1),
('Tuesday', '08:00:00', '16:00:00', 4, 1),
('Wednesday', '08:00:00', '16:00:00', 4, 1),
('Thursday', '08:00:00', '16:00:00', 4, 1),
('Friday', '08:00:00', '16:00:00', 4, 1),
('Saturday', '10:00:00', '14:00:00', 4, 1),
('Sunday', '10:00:00', '14:00:00', 4, 1);

-- =============================================
-- DEMO ORDERS WITH VARIOUS STATUSES
-- =============================================

-- Orders with different statuses for testing
INSERT INTO orders (client_id, status, pickup_or_dropoff, location_id, scheduled_time, other_instructions, created_by, updated_by, created_at) VALUES
-- Recent orders
(1, 'processing', 'pickup', 1, '2024-11-08 10:00:00', 'Please call when ready', 1, 1, NOW() - INTERVAL 2 HOUR),
(2, 'accepted', 'dropoff', 3, '2024-11-08 14:00:00', 'Ring doorbell', 1, 1, NOW() - INTERVAL 5 HOUR),
(3, 'waiting_for_supplies', 'pickup', 2, '2024-11-09 11:00:00', 'Will pick up tomorrow', 1, 1, NOW() - INTERVAL 1 DAY),
(4, 'ready_for_pickup', 'pickup', 1, '2024-11-07 15:00:00', 'Available anytime today', 1, 1, NOW() - INTERVAL 6 HOUR),
(5, 'ready_for_dropoff', 'dropoff', 3, '2024-11-07 16:00:00', 'Leave at door if not home', 1, 1, NOW() - INTERVAL 8 HOUR),
(6, 'fulfilled', 'pickup', 2, '2024-11-06 10:00:00', 'Completed successfully', 1, 1, NOW() - INTERVAL 1 DAY),
(7, 'fulfilled', 'dropoff', 3, '2024-11-05 14:00:00', 'Delivered on time', 1, 1, NOW() - INTERVAL 2 DAY),

-- Historical orders for analytics
(8, 'fulfilled', 'pickup', 1, '2024-11-04 09:00:00', 'No special instructions', 1, 1, NOW() - INTERVAL 3 DAY),
(9, 'fulfilled', 'pickup', 4, '2024-11-03 11:00:00', 'Regular order', 1, 1, NOW() - INTERVAL 4 DAY),
(10, 'cancelled', 'dropoff', 3, '2024-11-02 13:00:00', 'Client requested cancellation', 1, 1, NOW() - INTERVAL 5 DAY),
(11, 'fulfilled', 'pickup', 2, '2024-11-01 10:00:00', 'Picked up on time', 1, 1, NOW() - INTERVAL 6 DAY),
(12, 'fulfilled', 'dropoff', 1, '2024-10-31 15:00:00', 'Successful delivery', 1, 1, NOW() - INTERVAL 7 DAY),
(1, 'fulfilled', 'pickup', 1, '2024-10-30 12:00:00', 'Repeat client', 1, 1, NOW() - INTERVAL 8 DAY),
(2, 'fulfilled', 'pickup', 2, '2024-10-29 14:00:00', 'No issues', 1, 1, NOW() - INTERVAL 9 DAY),
(3, 'fulfilled', 'dropoff', 3, '2024-10-28 16:00:00', 'Completed', 1, 1, NOW() - INTERVAL 10 DAY),

-- More orders for comprehensive testing
(4, 'processing', 'pickup', 1, '2024-11-08 09:00:00', 'Urgent order', 1, 1, NOW() - INTERVAL 3 HOUR),
(5, 'waiting_for_customer', 'pickup', 2, '2024-11-08 13:00:00', 'Awaiting client response', 1, 1, NOW() - INTERVAL 4 HOUR),
(6, 'awaiting_scheduled_time', 'dropoff', 3, '2024-11-10 10:00:00', 'Scheduled for future', 1, 1, NOW() - INTERVAL 2 HOUR),
(7, 'further_info_needed', 'pickup', 1, '2024-11-08 11:00:00', 'Need to clarify order details', 1, 1, NOW() - INTERVAL 5 HOUR),
(8, 'problems_unknown', 'dropoff', 4, '2024-11-08 15:00:00', 'Investigating issue', 1, 1, NOW() - INTERVAL 7 HOUR);

-- =============================================
-- DEMO ORDER ITEMS
-- =============================================

-- Order 1 items (processing)
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(1, 1, 2),  -- Premium Widget x2
(1, 4, 1);  -- Standard Tool x1

-- Order 2 items (accepted)
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(2, 2, 3),  -- Basic Gadget x3
(2, 15, 1); -- Water Bottle x1

-- Order 3 items (waiting_for_supplies)
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(3, 3, 1),  -- Deluxe Kit x1
(3, 9, 1);  -- Bluetooth Headset x1

-- Order 4 items (ready_for_pickup)
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(4, 6, 1),  -- Laptop Pro x1
(4, 7, 1),  -- Wireless Mouse x1
(4, 8, 1);  -- USB-C Hub x1

-- Order 5 items (ready_for_dropoff)
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(5, 10, 1), -- Office Chair x1
(5, 12, 1); -- Monitor Stand x1

-- Order 6 items (fulfilled)
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(6, 13, 2), -- Programming Guide x2
(6, 14, 1); -- Design Principles x1

-- Order 7 items (fulfilled)
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(7, 5, 1),  -- Advanced Module x1
(7, 16, 1); -- Backpack x1

-- Add items to remaining orders
INSERT INTO order_items (order_id, product_id, quantity) VALUES
(8, 1, 1), (8, 2, 2),
(9, 4, 1), (9, 15, 2),
(10, 3, 1),
(11, 6, 1),
(12, 7, 1), (12, 8, 1),
(13, 10, 1),
(14, 13, 1), (14, 14, 1),
(15, 15, 3),
(16, 1, 5),
(17, 2, 2), (17, 4, 1),
(18, 3, 1), (18, 5, 1),
(19, 9, 1),
(20, 11, 1);

-- =============================================
-- DEMO PRODUCT CATEGORIES
-- =============================================

INSERT INTO product_categories (name, description) VALUES
('Office Supplies', 'General office and workspace supplies'),
('Electronics', 'Electronic devices and accessories'),
('Furniture', 'Office and workspace furniture'),
('Tools', 'Professional tools and equipment'),
('Books', 'Educational and reference materials'),
('Accessories', 'Personal accessories and items'),
('Harm Reduction', 'Harm reduction supplies and materials');

-- =============================================
-- DEMO NOTIFICATIONS
-- =============================================

INSERT INTO notifications (user_id, message, type, is_read, created_at) VALUES
(1, 'New order #1 created by John Smith', 'order_confirmation', 0, NOW() - INTERVAL 2 HOUR),
(1, 'Order #4 status changed to ready_for_pickup', 'status_change', 0, NOW() - INTERVAL 6 HOUR),
(1, 'Low stock alert: Premium Widget inventory below 60 units', 'low_stock', 1, NOW() - INTERVAL 1 DAY),
(2, 'Order #2 assigned to you for processing', 'system', 0, NOW() - INTERVAL 5 HOUR),
(2, 'New client Maria Garcia registered', 'system', 1, NOW() - INTERVAL 2 DAY);

INSERT INTO notifications (client_id, message, type, is_read, created_at) VALUES
(1, 'Your order #1 is being processed', 'order_confirmation', 0, NOW() - INTERVAL 2 HOUR),
(4, 'Your order #4 is ready for pickup', 'status_change', 0, NOW() - INTERVAL 6 HOUR),
(6, 'Your order #6 has been fulfilled', 'status_change', 1, NOW() - INTERVAL 1 DAY);

-- =============================================
-- DEMO CASE NOTES
-- =============================================

INSERT INTO case_notes (client_id, note_content, category, created_by, follow_up_date, is_confidential, created_at) VALUES
(1, 'Initial intake completed. Client requested harm reduction supplies. Provided educational materials and naloxone training.', 'general', 1, '2024-11-15', 0, NOW() - INTERVAL 7 DAY),
(2, 'Follow-up visit. Client reported stable housing situation. Connected with local resources for continued support.', 'homelessness', 1, '2024-11-20', 0, NOW() - INTERVAL 5 DAY),
(3, 'Crisis intervention provided. Client in stable condition. Referral made to mental health services.', 'mental_health', 1, '2024-11-12', 1, NOW() - INTERVAL 3 DAY),
(4, 'Routine check-in. Client doing well with current support plan. No changes needed at this time.', 'general', 2, NULL, 0, NOW() - INTERVAL 2 DAY),
(5, 'New referral received from partner agency. Initial assessment scheduled for next week.', 'referral', 1, '2024-11-14', 0, NOW() - INTERVAL 1 DAY);

-- =============================================
-- DEMO ACTIVITY LOG
-- =============================================

INSERT INTO activity_log (user_id, action, description, ip_address, created_at) VALUES
(1, 'login', 'Admin user logged in', '192.168.1.100', NOW() - INTERVAL 2 HOUR),
(1, 'create_order', 'Created order #1 for client John Smith', '192.168.1.100', NOW() - INTERVAL 2 HOUR),
(1, 'update_order', 'Updated order #4 status to ready_for_pickup', '192.168.1.100', NOW() - INTERVAL 6 HOUR),
(2, 'login', 'Manager user logged in', '192.168.1.101', NOW() - INTERVAL 5 HOUR),
(2, 'view_orders', 'Viewed all orders list', '192.168.1.101', NOW() - INTERVAL 5 HOUR),
(3, 'login', 'Worker user logged in', '192.168.1.102', NOW() - INTERVAL 4 HOUR),
(3, 'add_product', 'Added new product to inventory', '192.168.1.102', NOW() - INTERVAL 4 HOUR);

-- =============================================
-- DEMO DATA COMPLETE
-- =============================================
-- Database is now populated with comprehensive demo data
-- Ready for testing and demonstration
-- =============================================
