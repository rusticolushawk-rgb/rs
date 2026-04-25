
CREATE TABLE IF NOT EXISTS project_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    status VARCHAR(30) DEFAULT 'planning',
    start_date DATE,
    end_date DATE,
    budget DECIMAL(15,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'EGP',
    manager_id INT,
    progress INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    status VARCHAR(30) DEFAULT 'todo',
    priority VARCHAR(20) DEFAULT 'medium',
    assigned_to INT,
    due_date DATE,
    estimated_hours DECIMAL(5,1) DEFAULT 0,
    actual_hours DECIMAL(5,1) DEFAULT 0,
    progress INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES project_projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_timesheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    project_id INT,
    user_id INT,
    date DATE NOT NULL,
    hours DECIMAL(5,1) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES project_tasks(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES project_projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO project_projects (name, description, status, start_date, end_date, budget, progress) VALUES
('ERP Implementation', 'Full ERP system implementation for client', 'in_progress', '2024-01-15', '2024-06-30', 500000, 65),
('Website Redesign', 'Modern website redesign project', 'planning', '2024-03-01', '2024-05-15', 80000, 10),
('Mobile App v2', 'Mobile application version 2 development', 'in_progress', '2024-02-01', '2024-07-01', 200000, 40);

INSERT INTO project_tasks (project_id, name, status, priority, due_date, estimated_hours) VALUES
(1, 'Requirements Gathering', 'done', 'high', '2024-02-01', 40),
(1, 'Database Design', 'done', 'high', '2024-02-15', 24),
(1, 'Backend Development', 'in_progress', 'high', '2024-04-30', 160),
(1, 'Frontend Development', 'todo', 'medium', '2024-05-30', 120),
(2, 'UI/UX Design', 'in_progress', 'high', '2024-03-15', 60),
(3, 'API Development', 'in_progress', 'high', '2024-04-15', 80);
