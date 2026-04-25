
CREATE TABLE IF NOT EXISTS crm_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    contact_name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(30),
    company_name VARCHAR(200),
    source VARCHAR(50),
    stage VARCHAR(50) DEFAULT 'new',
    expected_revenue DECIMAL(15,2) DEFAULT 0,
    probability INT DEFAULT 10,
    assigned_to INT,
    notes TEXT,
    currency VARCHAR(3) DEFAULT 'EGP',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_stage (stage),
    INDEX idx_assigned (assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS crm_opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    lead_id INT,
    customer_id INT,
    stage VARCHAR(50) DEFAULT 'qualification',
    expected_revenue DECIMAL(15,2) DEFAULT 0,
    probability INT DEFAULT 50,
    expected_close DATE,
    assigned_to INT,
    notes TEXT,
    currency VARCHAR(3) DEFAULT 'EGP',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES crm_leads(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS crm_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(30),
    mobile VARCHAR(30),
    company_name VARCHAR(200),
    job_title VARCHAR(100),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    notes TEXT,
    is_customer TINYINT(1) DEFAULT 0,
    is_vendor TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO crm_leads (name, contact_name, email, company_name, source, stage, expected_revenue, probability) VALUES
('Website Redesign Project', 'Ahmed Hassan', 'ahmed@example.com', 'TechCorp Egypt', 'website', 'new', 50000, 20),
('ERP Implementation', 'Sara Mohamed', 'sara@example.com', 'Global Trade Co', 'referral', 'qualified', 150000, 50),
('Mobile App Development', 'Omar Ali', 'omar@example.com', 'StartupXYZ', 'social', 'proposition', 80000, 70),
('Cloud Migration', 'Fatma Ibrahim', 'fatma@example.com', 'DataFlow Inc', 'email', 'won', 200000, 100),
('Security Audit', 'Khaled Youssef', 'khaled@example.com', 'SecureNet', 'partner', 'new', 35000, 10);
