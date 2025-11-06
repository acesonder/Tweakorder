-- =============================================
-- Tweakorder Database Schema
-- Main database structure with all tables
-- =============================================
-- This file creates the core database structure
-- Import this first before harm_reduction_products.sql and demo_data.sql

CREATE DATABASE IF NOT EXISTS tweakorder;
USE tweakorder;

-- =============================================
-- CORE TABLES
-- =============================================

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    inventory INT DEFAULT 100,
    background_color VARCHAR(100),
    category VARCHAR(100),
    sku VARCHAR(100),
    is_favorite BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Workers Table
CREATE TABLE IF NOT EXISTS workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Clients Table (Enhanced with authentication fields)
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    emergency_contact VARCHAR(255),
    username VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255),
    security_question VARCHAR(255),
    security_answer_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table (for staff authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Manager', 'Worker', 'Viewer') NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255),
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Sessions Table
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT,
    client_id INT,
    user_type ENUM('staff', 'client') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Orders Table (Enhanced status options)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    status ENUM('processing', 'accepted', 'waiting_for_supplies', 'waiting_for_customer', 
                'further_info_needed', 'problems_unknown', 'ready_for_pickup', 
                'ready_for_dropoff', 'awaiting_scheduled_time', 'fulfilled', 'cancelled') DEFAULT 'processing',
    pickup_or_dropoff ENUM('pickup', 'dropoff') DEFAULT 'pickup',
    location_id INT,
    scheduled_time DATETIME,
    other_instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Pickup/Dropoff Locations Table
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    type ENUM('pickup', 'dropoff', 'both') DEFAULT 'both',
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Schedule Availability Table
CREATE TABLE IF NOT EXISTS schedule_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location_id INT,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    client_id INT,
    message TEXT NOT NULL,
    type ENUM('order_confirmation', 'status_change', 'system', 'low_stock') DEFAULT 'system',
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Product Categories Table
CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- AUDIT AND LOGGING TABLES
-- =============================================

-- Audit Log Table
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_value TEXT,
    new_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Error Logs Table
CREATE TABLE IF NOT EXISTS error_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    error_type VARCHAR(100) NOT NULL,
    error_message TEXT NOT NULL,
    error_details TEXT,
    user_id INT,
    client_id INT,
    page_url VARCHAR(255),
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    is_resolved BOOLEAN DEFAULT 0,
    resolved_at TIMESTAMP NULL,
    resolved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (resolved_by) REFERENCES users(id)
);

-- Activity Log Table (for audit trail)
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- CASE MANAGEMENT TABLES
-- =============================================

-- Case Templates Table
CREATE TABLE IF NOT EXISTS case_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('addiction', 'homelessness', 'mental_health', 'general', 'referral') NOT NULL,
    description TEXT,
    template_content TEXT NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Case Notes Table
CREATE TABLE IF NOT EXISTS case_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    template_id INT,
    note_content TEXT NOT NULL,
    category ENUM('addiction', 'homelessness', 'mental_health', 'general', 'referral') NOT NULL,
    created_by INT,
    follow_up_date DATE,
    is_confidential BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES case_templates(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- =============================================
-- CUSTOMIZATION TABLES
-- =============================================

-- Worker Preferences Table (for UI style preferences)
CREATE TABLE IF NOT EXISTS worker_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    ui_style ENUM('default', 'card-based', 'list-view', 'grid-categories', 'compact', 'tabbed') DEFAULT 'default',
    theme VARCHAR(50) DEFAULT 'light',
    preferences_json TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- CHAT SYSTEM TABLES (if not already created by chat_messaging_schema.sql)
-- =============================================

CREATE TABLE IF NOT EXISTS chat_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    type ENUM('direct', 'group', 'support') DEFAULT 'direct',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS chat_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT,
    client_id INT,
    participant_type ENUM('staff', 'client') NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    sender_id INT,
    sender_type ENUM('staff', 'client') NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE
);

-- =============================================
-- DEFAULT ADMIN USER
-- =============================================

-- Insert demo staff users (password: "password")
INSERT IGNORE INTO users (id, username, password_hash, role, first_name, last_name, email, is_active) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Admin', 'User', 'admin@tweakorder.com', 1),
(2, 'manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manager', 'Manager', 'User', 'manager@tweakorder.com', 1),
(3, 'worker', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Worker', 'Worker', 'User', 'worker@tweakorder.com', 1);

-- Note: All demo passwords are "password" - MUST be changed in production!

-- =============================================
-- DATABASE SCHEMA COMPLETE
-- =============================================
-- Next Steps:
-- 1. Import harm_reduction_products.sql for product catalog
-- 2. Import demo_data.sql for sample data
-- =============================================
