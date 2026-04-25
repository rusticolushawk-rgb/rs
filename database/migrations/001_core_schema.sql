-- Gyr Falcon ERP - Core Database Schema
-- =============================================

-- Roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    permissions JSON,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    role_id INT DEFAULT 1,
    company_id INT DEFAULT 1,
    phone VARCHAR(30),
    language VARCHAR(5) DEFAULT 'en',
    timezone VARCHAR(50) DEFAULT 'UTC',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Companies table
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    logo VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    phone VARCHAR(30),
    email VARCHAR(150),
    website VARCHAR(200),
    tax_id VARCHAR(50),
    registration_number VARCHAR(50),
    default_currency VARCHAR(3) DEFAULT 'EGP',
    language VARCHAR(5) DEFAULT 'en',
    is_default TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Currencies table
CREATE TABLE IF NOT EXISTS currencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(3) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    symbol_position ENUM('before', 'after') DEFAULT 'before',
    decimal_places INT DEFAULT 2,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exchange rates table
CREATE TABLE IF NOT EXISTS exchange_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_currency VARCHAR(3) NOT NULL,
    to_currency VARCHAR(3) NOT NULL,
    rate DECIMAL(15,6) NOT NULL,
    effective_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_currencies (from_currency, to_currency),
    INDEX idx_date (effective_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modules table
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    display_name VARCHAR(150) NOT NULL,
    version VARCHAR(20) DEFAULT '1.0',
    category VARCHAR(100) DEFAULT 'General',
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-cube',
    color VARCHAR(20) DEFAULT '#875A7B',
    dependencies JSON,
    is_active TINYINT(1) DEFAULT 0,
    installed_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit logs table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    type VARCHAR(30) DEFAULT 'info',
    icon VARCHAR(50) DEFAULT 'fas fa-bell',
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_read (user_id, is_read),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT,
    icon VARCHAR(50),
    color VARCHAR(20),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Seed Data
-- =============================================

-- Default Roles
INSERT INTO roles (name, display_name, permissions, description) VALUES
('admin', 'Administrator', '["*"]', 'Full system access'),
('manager', 'Manager', '["view","create","edit","delete","reports"]', 'Management access'),
('user', 'User', '["view","create","edit"]', 'Standard user access'),
('viewer', 'Viewer', '["view"]', 'Read-only access')
ON DUPLICATE KEY UPDATE display_name=VALUES(display_name);

-- Default Admin User (password: admin123)
INSERT INTO users (name, email, password, role_id, is_active) VALUES
('Administrator', 'admin@gyrfalcon.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Default Company
INSERT INTO companies (name, email, phone, default_currency, language, is_default, is_active) VALUES
('Gyr Falcon Corp', 'info@gyrfalcon.com', '+20 123 456 7890', 'EGP', 'en', 1, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Currencies
INSERT INTO currencies (code, name, symbol, symbol_position, decimal_places, is_active) VALUES
('EGP', 'Egyptian Pound', 'E£', 'before', 2, 1),
('USD', 'US Dollar', '$', 'before', 2, 1),
('EUR', 'Euro', '€', 'before', 2, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Default Exchange Rates
INSERT INTO exchange_rates (from_currency, to_currency, rate, effective_date) VALUES
('USD', 'EGP', 30.9000, CURDATE()),
('EUR', 'EGP', 33.5000, CURDATE()),
('USD', 'EUR', 0.9200, CURDATE()),
('EGP', 'USD', 0.0324, CURDATE()),
('EGP', 'EUR', 0.0299, CURDATE()),
('EUR', 'USD', 1.0870, CURDATE());

-- Module Categories
INSERT INTO categories (name, slug, description, icon, color, sort_order) VALUES
('Technology & Services', 'technology-services', 'Tech & IT service businesses', 'fas fa-laptop-code', '#00A09D', 1),
('Retail & Trade', 'retail-trade', 'Retail and trading businesses', 'fas fa-store', '#E6007E', 2),
('Food & Hospitality', 'food-hospitality', 'Food service & hospitality', 'fas fa-utensils', '#F39C12', 3),
('Health & Wellness', 'health-wellness', 'Healthcare & wellness', 'fas fa-heartbeat', '#E74C3C', 4),
('Construction & Manufacturing', 'construction-manufacturing', 'Construction & manufacturing', 'fas fa-industry', '#95A5A6', 5),
('Real Estate & Logistics', 'real-estate-logistics', 'Real estate & logistics', 'fas fa-building', '#3498DB', 6),
('Leisure & Lifestyle', 'leisure-lifestyle', 'Leisure & lifestyle businesses', 'fas fa-palette', '#9B59B6', 7),
('Business Operations', 'business-operations', 'Core business operations', 'fas fa-briefcase', '#2C3E50', 8)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Default Settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'Gyr Falcon ERP', 'general'),
('default_language', 'en', 'general'),
('default_currency', 'EGP', 'general'),
('items_per_page', '25', 'general'),
('date_format', 'Y-m-d', 'general'),
('time_format', 'H:i:s', 'general'),
('fiscal_year_start', '01-01', 'accounting'),
('tax_enabled', '1', 'accounting')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
