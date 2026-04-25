
CREATE TABLE IF NOT EXISTS iot_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    device_type VARCHAR(50),
    serial_number VARCHAR(50),
    location VARCHAR(100),
    status VARCHAR(20) DEFAULT 'offline',
    last_reading DECIMAL(15,4),
    last_reading_unit VARCHAR(20),
    last_seen DATETIME,
    ip_address VARCHAR(45),
    firmware_version VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS iot_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_id INT NOT NULL,
    value DECIMAL(15,4),
    unit VARCHAR(20),
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES iot_devices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO iot_devices (name, device_type, serial_number, location, status, last_reading, last_reading_unit) VALUES
('Temp Sensor - Server Room', 'temperature', 'IOT-TMP-001', 'Server Room A', 'online', 22.5, '°C'),
('Humidity Sensor - Warehouse', 'humidity', 'IOT-HUM-001', 'Main Warehouse', 'online', 45.2, '%'),
('Power Meter - Office', 'power', 'IOT-PWR-001', 'Office Building', 'offline', 15.8, 'kWh'),
('Motion Detector - Entrance', 'motion', 'IOT-MOT-001', 'Main Entrance', 'online', 1, 'bool');
