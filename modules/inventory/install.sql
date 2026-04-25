
CREATE TABLE IF NOT EXISTS inventory_warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10),
    address TEXT,
    manager_id INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS inventory_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    sku VARCHAR(50) UNIQUE,
    description TEXT,
    category VARCHAR(100),
    unit_price DECIMAL(15,2) DEFAULT 0,
    cost_price DECIMAL(15,2) DEFAULT 0,
    quantity INT DEFAULT 0,
    reorder_level INT DEFAULT 10,
    warehouse_id INT,
    currency VARCHAR(3) DEFAULT 'EGP',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (warehouse_id) REFERENCES inventory_warehouses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS inventory_stock_moves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(30),
    product_id INT NOT NULL,
    warehouse_id INT,
    move_type VARCHAR(20) NOT NULL,
    quantity INT NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_by INT,
    FOREIGN KEY (product_id) REFERENCES inventory_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO inventory_warehouses (name, code) VALUES ('Main Warehouse', 'WH-01'), ('Secondary Warehouse', 'WH-02');

INSERT INTO inventory_products (name, sku, category, unit_price, cost_price, quantity, reorder_level, warehouse_id) VALUES
('Server Rack Unit', 'HW-SRV-001', 'Hardware', 15000, 10000, 25, 5, 1),
('Network Switch 24-Port', 'HW-NET-001', 'Hardware', 3500, 2000, 50, 10, 1),
('UPS 3000VA', 'HW-UPS-001', 'Hardware', 8000, 5000, 15, 3, 1),
('CAT6 Cable (305m)', 'HW-CAB-001', 'Cables', 1200, 800, 100, 20, 2),
('SSD 1TB', 'HW-SSD-001', 'Storage', 2500, 1800, 75, 15, 1);
