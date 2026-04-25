
CREATE TABLE IF NOT EXISTS helpdesk_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(30) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    customer_name VARCHAR(150),
    customer_email VARCHAR(150),
    priority VARCHAR(20) DEFAULT 'medium',
    status VARCHAR(30) DEFAULT 'new',
    category VARCHAR(50),
    assigned_to INT,
    resolution TEXT,
    resolved_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO helpdesk_tickets (ticket_number, subject, customer_name, customer_email, priority, status, category) VALUES
('TK-2024-001', 'Login Issue', 'Ahmed Hassan', 'ahmed@example.com', 'high', 'new', 'Technical'),
('TK-2024-002', 'Billing Question', 'Sara Mohamed', 'sara@example.com', 'medium', 'in_progress', 'Billing'),
('TK-2024-003', 'Feature Request', 'Omar Ali', 'omar@example.com', 'low', 'resolved', 'Feature'),
('TK-2024-004', 'System Error 500', 'Fatma Ibrahim', 'fatma@example.com', 'urgent', 'new', 'Technical'),
('TK-2024-005', 'Password Reset', 'Khaled Youssef', 'khaled@example.com', 'low', 'closed', 'Account');
