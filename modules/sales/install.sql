
CREATE TABLE IF NOT EXISTS sales_customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(30),
    company VARCHAR(200),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    tax_id VARCHAR(50),
    currency VARCHAR(3) DEFAULT 'EGP',
    credit_limit DECIMAL(15,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    sku VARCHAR(50) UNIQUE,
    description TEXT,
    category VARCHAR(100),
    unit_price DECIMAL(15,2) DEFAULT 0,
    cost_price DECIMAL(15,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'EGP',
    stock_quantity INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    customer_id INT,
    order_date DATE NOT NULL,
    delivery_date DATE,
    status VARCHAR(30) DEFAULT 'draft',
    payment_status VARCHAR(30) DEFAULT 'unpaid',
    subtotal DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'EGP',
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES sales_customers(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_order_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    description VARCHAR(255),
    quantity DECIMAL(10,2) DEFAULT 1,
    unit_price DECIMAL(15,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    discount DECIMAL(5,2) DEFAULT 0,
    line_total DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES sales_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES sales_products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(30) NOT NULL UNIQUE,
    order_id INT,
    customer_id INT,
    invoice_date DATE NOT NULL,
    due_date DATE,
    status VARCHAR(30) DEFAULT 'draft',
    total_amount DECIMAL(15,2) DEFAULT 0,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'EGP',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES sales_orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sales_customers (name, email, phone, company, currency) VALUES
('Ahmed Corp', 'info@ahmedcorp.com', '+20 111 222 3333', 'Ahmed Corp', 'EGP'),
('Global Systems', 'sales@globalsys.com', '+1 555 123 4567', 'Global Systems Inc', 'USD'),
('EuroTech GmbH', 'contact@eurotech.eu', '+49 30 12345', 'EuroTech GmbH', 'EUR');

INSERT INTO sales_products (name, sku, unit_price, cost_price, tax_rate, stock_quantity) VALUES
('ERP License - Standard', 'ERP-STD-001', 5000, 2000, 14, 100),
('ERP License - Enterprise', 'ERP-ENT-001', 15000, 6000, 14, 50),
('Implementation Service', 'SVC-IMP-001', 25000, 10000, 14, 999),
('Annual Support', 'SVC-SUP-001', 3000, 1000, 14, 999),
('Training Package', 'SVC-TRN-001', 2000, 800, 14, 999);

INSERT INTO sales_orders (order_number, customer_id, order_date, status, payment_status, subtotal, tax_amount, total_amount) VALUES
('SO-2024-001', 1, CURDATE(), 'confirmed', 'paid', 20000, 2800, 22800),
('SO-2024-002', 2, CURDATE(), 'draft', 'unpaid', 40000, 5600, 45600),
('SO-2024-003', 3, CURDATE(), 'confirmed', 'partial', 15000, 2100, 17100);
