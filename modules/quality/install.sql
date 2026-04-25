
CREATE TABLE IF NOT EXISTS quality_checks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    check_type VARCHAR(50),
    product_name VARCHAR(200),
    check_point VARCHAR(200),
    result VARCHAR(20) DEFAULT 'pending',
    inspector_id INT,
    inspection_date DATE,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quality_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    severity VARCHAR(20) DEFAULT 'medium',
    status VARCHAR(20) DEFAULT 'new',
    product_name VARCHAR(200),
    assigned_to INT,
    resolved_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO quality_checks (name, check_type, product_name, check_point, result, inspection_date) VALUES
('Incoming Material Check', 'incoming', 'Steel Plates', 'Dimension Accuracy', 'pass', CURDATE()),
('Final Product QC', 'final', 'Server Rack Unit', 'Assembly Quality', 'pass', CURDATE()),
('Process Check - Welding', 'process', 'Metal Frame', 'Weld Strength', 'fail', CURDATE());

INSERT INTO quality_alerts (title, severity, status, product_name) VALUES
('Dimensional Deviation Detected', 'high', 'new', 'Metal Frame'),
('Surface Finish Issue', 'medium', 'in_progress', 'Cabinet Door');
