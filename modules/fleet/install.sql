
CREATE TABLE IF NOT EXISTS fleet_vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    license_plate VARCHAR(20),
    make VARCHAR(50),
    model VARCHAR(50),
    year INT,
    color VARCHAR(30),
    fuel_type VARCHAR(20) DEFAULT 'petrol',
    mileage INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'available',
    driver_id INT,
    insurance_expiry DATE,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fleet_maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    type VARCHAR(50),
    description TEXT,
    cost DECIMAL(15,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'EGP',
    date DATE,
    next_service_date DATE,
    status VARCHAR(20) DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES fleet_vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO fleet_vehicles (name, license_plate, make, model, year, fuel_type, mileage, status) VALUES
('Delivery Van 1', 'ABC-1234', 'Toyota', 'HiAce', 2022, 'diesel', 45000, 'available'),
('Pickup Truck', 'XYZ-5678', 'Ford', 'Ranger', 2023, 'diesel', 12000, 'in_use'),
('Sedan - Sales', 'DEF-9012', 'Hyundai', 'Elantra', 2024, 'petrol', 5000, 'available');
