
CREATE TABLE IF NOT EXISTS prepress_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_number VARCHAR(30) NOT NULL UNIQUE,
    job_name VARCHAR(200) NOT NULL,
    client_name VARCHAR(150),
    client_email VARCHAR(150),
    status VARCHAR(30) DEFAULT 'new',
    priority VARCHAR(20) DEFAULT 'medium',
    color_mode VARCHAR(20) DEFAULT 'CMYK',
    dimensions VARCHAR(50),
    bleed VARCHAR(20) DEFAULT '3mm',
    resolution_dpi INT DEFAULT 300,
    specifications TEXT,
    assigned_to INT,
    due_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS prepress_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_type VARCHAR(20),
    file_size INT DEFAULT 0,
    version INT DEFAULT 1,
    uploaded_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES prepress_jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS prepress_proofs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    file_id INT,
    proof_number INT DEFAULT 1,
    status VARCHAR(30) DEFAULT 'pending',
    reviewer_name VARCHAR(150),
    reviewer_email VARCHAR(150),
    comments TEXT,
    approved_at DATETIME,
    rejected_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES prepress_jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (file_id) REFERENCES prepress_files(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO prepress_jobs (job_number, job_name, client_name, status, priority, color_mode, dimensions, resolution_dpi, due_date) VALUES
('PP-2024-001', 'Business Cards - Premium', 'Ahmed Corp', 'in_progress', 'high', 'CMYK', '90x50mm', 300, DATE_ADD(CURDATE(), INTERVAL 3 DAY)),
('PP-2024-002', 'Product Catalog 2024', 'Global Trade Co', 'proofing', 'medium', 'CMYK', 'A4', 300, DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
('PP-2024-003', 'Banner - Trade Show', 'StartupXYZ', 'new', 'low', 'CMYK', '3000x1500mm', 150, DATE_ADD(CURDATE(), INTERVAL 14 DAY)),
('PP-2024-004', 'Packaging Design', 'FoodCo', 'approved', 'high', 'CMYK+Spot', '200x150x50mm', 300, DATE_ADD(CURDATE(), INTERVAL 2 DAY));

INSERT INTO prepress_proofs (job_id, proof_number, status, reviewer_name) VALUES
(2, 1, 'rejected', 'Sara Mohamed'),
(2, 2, 'pending', 'Sara Mohamed'),
(4, 1, 'approved', 'Ahmed Hassan');
