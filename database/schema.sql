CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    balance DECIMAL(12,2) DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    value TEXT NULL
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    smsman_application_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    code VARCHAR(20) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uniq_smsman_app (smsman_application_id)
);

CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_id INT NOT NULL,
    country_id INT NOT NULL,
    application_id INT NOT NULL,
    number VARCHAR(32) NOT NULL,
    status VARCHAR(20) NOT NULL,
    purchase_type ENUM('buy','rent') DEFAULT 'buy',
    rental_hours INT NULL,
    rental_end_at DATETIME NULL,
    cost DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('refill','purchase','adjustment') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    ref VARCHAR(120) NOT NULL,
    provider VARCHAR(60) NOT NULL,
    status VARCHAR(40) DEFAULT 'pending',
    meta JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (name, email, password_hash, role, balance) VALUES
('Admin', 'admin@getsms.local', '$2y$10$pT.y6aoiBJE0/EApKk5RLe206TpwIAPAv.bjjhMbjCxTfLm4zenfa', 'admin', 0.00);

INSERT INTO services (smsman_application_id, name, code, price) VALUES
(1, 'Vkontakte', 'vk', 1.50),
(2, 'WeChat', 'wb', 2.00),
(3, 'Telegram', 'tg', 1.20);
