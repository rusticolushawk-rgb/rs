
CREATE TABLE IF NOT EXISTS ecommerce_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    customer_name VARCHAR(150),
    customer_email VARCHAR(150),
    customer_phone VARCHAR(30),
    shipping_address TEXT,
    status VARCHAR(30) DEFAULT 'pending',
    payment_method VARCHAR(30),
    payment_status VARCHAR(30) DEFAULT 'pending',
    subtotal DECIMAL(15,2) DEFAULT 0,
    shipping_cost DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'EGP',
    tracking_number VARCHAR(100),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ecommerce_orders (order_number, customer_name, customer_email, status, payment_status, total_amount) VALUES
('EC-2024-001', 'John Smith', 'john@example.com', 'processing', 'paid', 5500),
('EC-2024-002', 'Maria Garcia', 'maria@example.com', 'shipped', 'paid', 12000),
('EC-2024-003', 'Li Wei', 'li@example.com', 'pending', 'pending', 3200);
