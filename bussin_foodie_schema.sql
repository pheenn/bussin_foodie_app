.

-- General Setup for Smooth Import
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0; -- Temporarily disable checks for table drop/create order

-- --------------------------------------------------------
-- DATABASE SETUP
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `bussin_foodie` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bussin_foodie`;

-- --------------------------------------------------------
-- TABLE STRUCTURES
-- --------------------------------------------------------

-- Table: users (admin users only)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'staff') DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: categories
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: products
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `category_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `cost_price` DECIMAL(10,2) DEFAULT 0,
  `image_url` VARCHAR(500) NOT NULL,
  `stock_quantity` INT DEFAULT 100,
  `min_stock` INT DEFAULT 10,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: customers
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: orders (Updated to include customer_id and seat_number)
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `customer_id` INT NULL, -- Links to customers, NULLable in case customer is deleted (ON DELETE SET NULL)
  `seat_number` VARCHAR(10) NULL, -- New field for table/seat number
  `order_number` VARCHAR(20) UNIQUE NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100),
  `customer_phone` VARCHAR(20),
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: payments
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `payment_method` ENUM('cash', 'gcash', 'card', 'paymaya') DEFAULT 'cash',
  `amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  `transaction_id` VARCHAR(100) NULL,
  `paid_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: system_settings
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `setting_key` VARCHAR(50) UNIQUE NOT NULL,
  `setting_value` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- FOREIGN KEY CONSTRAINTS
-- --------------------------------------------------------

-- Products
ALTER TABLE `products`
ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE;

-- Orders
ALTER TABLE `orders`
ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL;

-- Order Items
ALTER TABLE `order_items`
ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE;

-- Payments
ALTER TABLE `payments`
ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- Insert Categories
INSERT INTO `categories` (`name`, `description`, `display_order`) VALUES
('Meals', 'Hearty & Satisfying', 1),
('Silogs', 'Pinoy Breakfast Favorites', 2),
('Snacks', 'Perfect Bite-Sized Treats', 3),
('Drinks', 'Refreshing & Pair-Worthy', 4);

-- Insert Products
-- MEALS
INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `cost_price`, `image_url`, `stock_quantity`, `min_stock`) VALUES
(1, 'Creamy Garlic Butter Shrimp Pasta', 'Al dente pasta with plump shrimp in a rich, garlicky sauce', 350.00, 150.00, 'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=400&h=300&fit=crop', 45, 10),
(1, 'Sweet & Sour Pork Belly', 'Crispy belly chunks tossed in tangy-sweet glaze with veggies', 320.00, 120.00, 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w-400&h=300&fit=crop', 38, 10),
(1, 'Beef Kaldereta', 'Tender beef stew with potatoes, carrots, and cheese in tomato sauce', 280.00, 100.00, 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=400&h=300&fit=crop', 52, 10),
(1, 'Grilled Chicken Inasal', 'Marinated chicken with lemongrass and annatto, served with rice', 250.00, 80.00, 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=400&h=300&fit=crop', 65, 10),
(1, 'Veggie Pad Thai', 'Stir-fried rice noodles with tofu, peanuts, and lime-cilantro dressing', 220.00, 70.00, 'https://images.unsplash.com/photo-1559314809-2b99056a8c4a?w=400&h=300&fit=crop', 42, 10),
(1, 'Classic Hamburger Steak', 'Juicy patty with mushroom gravy, served with mashed potatoes', 270.00, 90.00, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=300&fit=crop', 58, 10);

-- SILOGS
INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `cost_price`, `image_url`, `stock_quantity`, `min_stock`) VALUES
(2, 'Tapsilog', 'Marinated beef tapa, garlic fried rice, sunny-side up egg', 180.00, 60.00, 'https://images.unsplash.com/photo-1585937421612-70ca003675ed?w=400&h=300&fit=crop', 72, 10),
(2, 'Longsilog', 'Sweet Filipino longganisa, garlic rice, sunny-side up egg', 160.00, 50.00, 'https://images.unsplash.com/photo-1585937421612-70ca003675ed?w=400&h=300&fit=crop', 68, 10),
(2, 'Tocilog', 'Crispy pork tocino, garlic rice, sunny-side up egg', 170.00, 55.00, 'https://images.unsplash.com/photo-1585937421612-70ca003675ed?w=400&h=300&fit=crop', 75, 10),
(2, 'Bangsilog', 'Fried milkfish (bangus), garlic rice, sunny-side up egg', 190.00, 65.00, 'https://images.unsplash.com/photo-1585937421612-70ca003675ed?w=400&h=300&fit=crop', 62, 10),
(2, 'Adosilog', 'Pork adobo, garlic rice, sunny-side up egg', 175.00, 58.00, 'https://images.unsplash.com/photo-1585937421612-70ca003675ed?w=400&h=300&fit=crop', 70, 10),
(2, 'Chicksilog', 'Grilled chicken strips, garlic rice, sunny-side up egg', 165.00, 52.00, 'https://images.unsplash.com/photo-1585937421612-70ca003675ed?w=400&h=300&fit=crop', 65, 10);

-- SNACKS
INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `cost_price`, `image_url`, `stock_quantity`, `min_stock`) VALUES
(3, 'Cheese Stuffed Pandesal Bites', 'Warm bread rolls with melted cheese filling', 120.00, 30.00, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=300&fit=crop', 85, 10),
(3, 'Spicy Tuna Empanadas', 'Crispy pastry with savory tuna and chili mix', 95.00, 25.00, 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=400&h=300&fit=crop', 78, 10),
(3, 'Ube Cheese Mochi', 'Chewy rice cake with ube and cream cheese center', 110.00, 35.00, 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=400&h=300&fit=crop', 92, 10),
(3, 'Chicken Skin Chicharon', 'Extra crispy chicken skin, salt & vinegar seasoning', 85.00, 20.00, 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=400&h=300&fit=crop', 105, 10),
(3, 'Mango Float Cups', 'Layered graham, cream, fresh mangoes in individual cups', 135.00, 40.00, 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=400&h=300&fit=crop', 88, 10),
(3, 'Beef Tapa Sliders', 'Mini burgers with tender tapa and garlic mayo', 150.00, 45.00, 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=400&h=300&fit=crop', 72, 10);

-- DRINKS
INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `cost_price`, `image_url`, `stock_quantity`, `min_stock`) VALUES
(4, 'Calamansi Juice with Honey', 'Tangy local citrus drink with natural sweetness', 80.00, 15.00, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=400&h=300&fit=crop', 120, 10),
(4, 'Ube Milkshake', 'Creamy shake with purple yam flavor and whipped cream', 140.00, 35.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&h=300&fit=crop', 95, 10),
(4, 'Iced Barako Coffee', 'Strong Filipino coffee with milk and sugar', 90.00, 20.00, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400&h=300&fit=crop', 110, 10),
(4, 'Mango Green Tea', 'Refreshing iced tea with fresh mango puree', 100.00, 25.00, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=300&fit=crop', 98, 10),
(4, 'Coconut Water with Lychee', 'Hydrating coconut water mixed with lychee bits', 85.00, 18.00, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=300&fit=crop', 115, 10),
(4, 'Strawberry Calamansi Smoothie', 'Sweet-tart blend of strawberries and calamansi', 130.00, 32.00, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=400&h=300&fit=crop', 88, 10);

-- Insert Admin User (password: admin123)
-- Hash generated from: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `role`) VALUES
('admin', 'admin@bussinfoodie.com',
'$2y$12$Xv2gEy1aZooGHketQ.JO3e4SrEoqiEOd0Pl8NRSuZnANRXia2EeX', 'System Administrator', 'admin'),
('manager', 'manager@bussinfoodie.com',
'$2y$12$Xv2gEy1aZooGHketQ.JO3e4SrEoqiEOd0Pl8NRSuZnANRXia2EeX', 'Store Manager', 'admin');

-- Insert System Settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('store_name', 'Bussin'' Foodie'),
('store_phone', '+63 917 123 4567'),
('store_email', 'orders@bussinfoodie.com'),
('currency', 'PHP'),
('tax_rate', '0.12');

-- --------------------------------------------------------
-- INDEXES & VIEWS
-- --------------------------------------------------------

-- Create indexes for better performance
CREATE INDEX `idx_orders_status` ON `orders`(`status`);
CREATE INDEX `idx_orders_created` ON `orders`(`created_at`);
CREATE INDEX `idx_products_category` ON `products`(`category_id`);
CREATE INDEX `idx_products_active` ON `products`(`is_active`);
CREATE INDEX `idx_order_items_order` ON `order_items`(`order_id`);
CREATE INDEX `idx_payments_order` ON `payments`(`order_id`);
CREATE INDEX `idx_orders_customer` ON `orders`(`customer_id`);

-- Create view for dashboard statistics
DROP VIEW IF EXISTS `dashboard_stats`;
CREATE VIEW `dashboard_stats` AS
SELECT
  (SELECT COUNT(*) FROM `orders` WHERE DATE(created_at) = CURDATE()) AS `today_orders`,
  (SELECT SUM(total_amount) FROM `orders` WHERE DATE(created_at) = CURDATE() AND status != 'cancelled') AS `today_revenue`,
  (SELECT COUNT(*) FROM `orders` WHERE status = 'pending') AS `pending_orders`,
  (SELECT COUNT(*) FROM `products` WHERE stock_quantity <= min_stock) AS `low_stock_items`;

-- Finalize Setup
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
```