-- Create database
CREATE DATABASE IF NOT EXISTS it_assets_db;
USE it_assets_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Asset categories table
CREATE TABLE IF NOT EXISTS asset_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Assets table
CREATE TABLE IF NOT EXISTS assets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asset_tag VARCHAR(50) UNIQUE NOT NULL,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    purchase_date DATE,
    purchase_cost DECIMAL(10, 2),
    status ENUM('available', 'in_use', 'maintenance', 'decommissioned') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES asset_categories(id)
);

-- Asset allocations table
CREATE TABLE IF NOT EXISTS asset_allocations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asset_id INT,
    user_id INT,
    allocated_date DATE NOT NULL,
    return_date DATE,
    status ENUM('active', 'returned') DEFAULT 'active',
    notes TEXT,
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Maintenance records table
CREATE TABLE IF NOT EXISTS maintenance_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asset_id INT,
    maintenance_date DATE NOT NULL,
    description TEXT,
    cost DECIMAL(10, 2),
    performed_by VARCHAR(100),
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- Insert default admin user
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@itams.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert default employee user
INSERT INTO users (name, email, password, role) VALUES 
('Employee User', 'employee@itams.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee');

-- Insert sample asset categories
INSERT INTO asset_categories (name, description) VALUES 
('Laptops', 'Portable computers for staff'),
('Servers', 'Network and application servers'),
('Software', 'Software licenses and applications'),
('Network Equipment', 'Routers, switches, and network devices');

ALTER TABLE asset_allocations 
DROP FOREIGN KEY asset_allocations_ibfk_1;

ALTER TABLE asset_allocations 
ADD CONSTRAINT asset_allocations_ibfk_1 
FOREIGN KEY (asset_id) REFERENCES assets(id) 
ON DELETE CASCADE;




