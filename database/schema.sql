CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    phone_number VARCHAR(40) NULL,
    country VARCHAR(120) NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    balance DECIMAL(12,2) DEFAULT 0,
    balance_topup DECIMAL(12,2) DEFAULT 0,
    balance_earnings DECIMAL(12,2) DEFAULT 0,
    store_slug VARCHAR(120) NULL UNIQUE,
    store_name VARCHAR(160) NULL,
    store_tagline VARCHAR(220) NULL,
    store_description TEXT NULL,
    profile_image VARCHAR(255) NULL,
    referral_code VARCHAR(24) NULL UNIQUE,
    referred_by INT NULL,
    first_deposit_at DATETIME NULL,
    referral_rewarded TINYINT(1) NOT NULL DEFAULT 0,
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

CREATE TABLE IF NOT EXISTS social_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    peakerr_service_id INT NOT NULL,
    name VARCHAR(180) NOT NULL,
    type VARCHAR(120) NULL,
    category VARCHAR(180) NULL,
    rate DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
    min_qty INT NOT NULL DEFAULT 1,
    max_qty INT NOT NULL DEFAULT 1,
    refill TINYINT(1) NOT NULL DEFAULT 0,
    cancel TINYINT(1) NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_peakerr_service (peakerr_service_id)
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

CREATE TABLE IF NOT EXISTS social_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    peakerr_order_id INT NOT NULL,
    link TEXT NOT NULL,
    quantity INT NOT NULL,
    runs INT NULL,
    interval_minutes INT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'pending',
    charge DECIMAL(12,4) NULL,
    remains INT NULL,
    currency VARCHAR(10) DEFAULT 'XAF',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES social_services(id)
);

CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(160) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS account_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    category VARCHAR(120) NOT NULL,
    platform VARCHAR(120) NOT NULL,
    year INT NULL,
    price DECIMAL(12,2) NOT NULL,
    description TEXT NULL,
    account_details TEXT NOT NULL,
    status ENUM('pending','approved','rejected','sold') DEFAULT 'pending',
    buyer_id INT NULL,
    approved_at DATETIME NULL,
    sold_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS account_purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    platform_fee DECIMAL(12,2) NOT NULL,
    net_amount DECIMAL(12,2) NOT NULL,
    details_snapshot TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES account_listings(id),
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS seller_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    ref VARCHAR(120) NOT NULL,
    paid_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS withdrawal_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    fee DECIMAL(12,2) NOT NULL,
    net_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (name, email, password_hash, role, balance, referral_code) VALUES
('Admin', 'admin@getsms.local', '$2y$10$pT.y6aoiBJE0/EApKk5RLe206TpwIAPAv.bjjhMbjCxTfLm4zenfa', 'admin', 0.00, 'admin');

INSERT INTO services (smsman_application_id, name, code, price) VALUES
(1, 'Vkontakte', 'vk', 1.50),
(2, 'WeChat', 'wb', 2.00),
(3, 'Telegram', 'tg', 1.20);
