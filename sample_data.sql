-- Sample data for testing

USE tweakorder;

-- Insert sample products
INSERT INTO products (name, description, inventory, background_color, category) VALUES
('Premium Widget', 'High-quality widget for professional use', 50, 'gradient-1', 'Tools'),
('Basic Gadget', 'Essential gadget for everyday tasks', 100, 'gradient-5', 'Accessories'),
('Deluxe Kit', 'Complete kit with everything you need', 25, 'gradient-7', 'Kits'),
('Standard Tool', 'Reliable tool for general purposes', 75, 'gradient-10', 'Tools'),
('Advanced Module', 'Advanced module with extra features', 30, 'gradient-3', 'Modules');

-- Insert sample locations
INSERT INTO locations (name, address, type, is_active) VALUES
('Main Warehouse', '123 Main St, City, ST 12345', 'both', 1),
('Downtown Pickup Point', '456 Downtown Ave, City, ST 12345', 'pickup', 1),
('East Side Dropoff', '789 East Rd, City, ST 12345', 'dropoff', 1),
('North Branch', '321 North Blvd, City, ST 12345', 'both', 1);

-- Insert sample schedule availability
INSERT INTO schedule_availability (day_of_week, start_time, end_time, location_id, is_active) VALUES
('Monday', '09:00:00', '17:00:00', 1, 1),
('Tuesday', '09:00:00', '17:00:00', 1, 1),
('Wednesday', '09:00:00', '17:00:00', 1, 1),
('Thursday', '09:00:00', '17:00:00', 1, 1),
('Friday', '09:00:00', '17:00:00', 1, 1),
('Monday', '10:00:00', '18:00:00', 2, 1),
('Wednesday', '10:00:00', '18:00:00', 2, 1),
('Friday', '10:00:00', '18:00:00', 2, 1);

-- Insert sample workers (for backward compatibility)
INSERT INTO workers (first_name, last_name) VALUES
('Alice', 'Manager'),
('Bob', 'Technician'),
('Carol', 'Supervisor');
