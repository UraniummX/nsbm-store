-- NSBM Campus Market Database Schema

CREATE DATABASE IF NOT EXISTS nsbm_market;
USE nsbm_market;

-- Users table for Admin authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Insert sample categories
INSERT IGNORE INTO categories (name) VALUES ('Food'), ('Stationery'), ('Fashion'), ('Tech'), ('Services');

-- Insert a default admin (password is 'admin123')
INSERT IGNORE INTO users (username, password) VALUES ('admin', 'admin123');

-- Add some sample products for the demonstration
INSERT INTO products (name, description, price, category_id) VALUES 
('NSBM Hoodie - Green', 'Official NSBM university hoodie, limited edition.', 3500.00, 3),
('Customized Notebook', 'Spiral bound A5 notebook with custom student designs.', 450.00, 2),
('Laptop Sticker Pack', 'High-quality waterproof stickers for tech lovers.', 150.00, 4),
('Homemade Brownies', 'Freshly baked chocolate brownies (Set of 4).', 600.00, 1),
('Wireless Mouse', 'Ergonomic wireless mouse for long study sessions.', 1200.00, 4);
