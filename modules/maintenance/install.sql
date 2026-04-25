
CREATE TABLE IF NOT EXISTS maintenance_equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    serial_number VARCHAR(50),
    category VARCHAR(50),
    location VARCHAR(100),
    status VARCHAR(20) DEFAULT 'operational',
    purchase_date DATE,
    warranty_end DATE,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS maintenance_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT,
    request_type VARCHAR(30) DEFAULT 'corrective',
    priority VARCHAR(20) DEFAULT 'medium',
    description TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    requested_by INT,
    assigned_to INT,
    scheduled_date DATE,
    completed_date DATE,
    cost DECIMAL(15,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'EGP',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES maintenance_equipment(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO maintenance_equipment (name, serial_number, category, location, status) VALUES
('CNC Machine', 'CNC-001', 'Machinery', 'Production Floor', 'operational'),
('Forklift', 'FLT-001', 'Vehicles', 'Warehouse', 'operational'),
('HVAC System', 'HVAC-001', 'Facilities', 'Office Building', 'maintenance');

INSERT INTO maintenance_requests (equipment_id, request_type, priority, description, status, scheduled_date) VALUES
(1, 'preventive', 'medium', 'Monthly preventive maintenance', 'scheduled', DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(3, 'corrective', 'high', 'HVAC not cooling properly', 'in_progress', CURDATE()),
(2, 'preventive', 'low', 'Oil change and inspection', 'pending', DATE_ADD(CURDATE(), INTERVAL 14 DAY));
