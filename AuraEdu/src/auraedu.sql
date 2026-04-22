-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auraedu`
--

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','editor') COLLATE utf8mb4_unicode_ci DEFAULT 'editor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admins` (`admin_id`, `full_name`, `email`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
-- ── Original record ──────────────────────────────────────────────────────────
(1, 'AuraEdu Admin',      'admin@auraedu.edu',       '$2y$10$examplehash',   'super_admin', '2026-04-14 02:45:32', '2026-04-14 02:45:32'),
-- ── Additional records ───────────────────────────────────────────────────────
(2, 'Norah Al-Otaibi',    'norah.a@auraedu.edu',     '$2y$10$AdminHash0002', 'editor',      '2026-04-15 08:10:00', '2026-04-15 08:10:00'),
(3, 'Lujain Al-Shamrani', 'lujain.s@auraedu.edu',    '$2y$10$AdminHash0003', 'editor',      '2026-04-15 09:00:00', '2026-04-15 09:00:00'),
(4, 'Reema Al-Zahrani',   'reema.z@auraedu.edu',     '$2y$10$AdminHash0004', 'editor',      '2026-04-15 09:30:00', '2026-04-15 09:30:00'),
(5, 'Ghada Al-Ghamdi',    'ghada.g@auraedu.edu',     '$2y$10$AdminHash0005', 'super_admin', '2026-04-15 10:00:00', '2026-04-15 10:00:00');

-- --------------------------------------------------------
-- Table structure for table `customers`
-- --------------------------------------------------------

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customers` (`customer_id`, `full_name`, `email`, `password_hash`, `phone`, `city`, `address`, `created_at`, `updated_at`) VALUES
-- ── Original records ─────────────────────────────────────────────────────────
(1, 'Aisha Alqahtani',  'aisha.q@auraedu.edu',   '$2y$10$CustHash0001', '0551111001', 'Dammam',  NULL, '2026-04-14 02:45:32', '2026-04-14 02:45:32'),
(2, 'Omar Alharbi',     'omar.h@auraedu.edu',     '$2y$10$CustHash0002', '0551111002', 'Khobar',  NULL, '2026-04-14 02:45:32', '2026-04-14 02:45:32'),
-- ── Additional records ───────────────────────────────────────────────────────
(3, 'Sara Al-Malki',    'sara.m@gmail.com',        '$2y$10$CustHash0003', '0561234567', 'Riyadh',  'Al-Malaz District, Building 12',        '2026-04-15 09:00:00', '2026-04-15 09:00:00'),
(4, 'Hessa Al-Dosari',  'hessa.d@hotmail.com',     '$2y$10$CustHash0004', '0572345678', 'Jeddah',  'Al-Hamraa District, Villa 5',           '2026-04-15 10:15:00', '2026-04-15 10:15:00'),
(5, 'Maha Al-Rashidi',  'maha.r@outlook.com',      '$2y$10$CustHash0005', '0583456789', 'Makkah',  'Al-Aziziyah, Block 3, Apt 8',           '2026-04-15 11:00:00', '2026-04-15 11:00:00'),
(6, 'Dina Al-Harbi',    'dina.h@gmail.com',        '$2y$10$CustHash0006', '0594567890', 'Madinah', 'Al-Anbariyah St, Building 7',           '2026-04-15 12:00:00', '2026-04-15 12:00:00'),
(7, 'Rawan Al-Enezi',   'rawan.e@gmail.com',       '$2y$10$CustHash0007', '0505678901', 'Riyadh',  'Olaya District, Tower B Apt 14',        '2026-04-15 13:00:00', '2026-04-15 13:00:00'),
(8, 'Fatimah Al-Osaimi','fatimah.o@yahoo.com',     '$2y$10$CustHash0008', '0516789012', 'Taif',    'Al-Shafa Road, Villa 22',               '2026-04-15 14:00:00', '2026-04-15 14:00:00');

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_type` enum('book','course') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_sar` decimal(10,2) NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no-image.jpg',
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `download_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`product_id`, `product_type`, `title`, `price_sar`, `short_description`, `product_image`, `stock_qty`, `discount_percentage`, `is_featured`, `is_active`, `download_link`, `created_at`, `updated_at`) VALUES
-- ── Original records ─────────────────────────────────────────────────────────
(1, 'book',   'Web Coding & Development (2nd Edition)',        '209.00', 'Comprehensive practical guide for modern web coding.',        'prod_69ddabb5156cc6.05674825.png',  25,  '0.00', 1, 1, 'https://auraedu.edu/downloads/web-coding-development.pdf',  '2026-04-14 02:45:32', '2026-04-14 02:51:33'),
(2, 'book',   'HTML, CSS, and JavaScript All in One',          '281.00', 'Step-by-step all-in-one guide.',                             'prod_69ddaba806dd68.36728291.png',  20,  '0.00', 1, 1, 'https://auraedu.edu/downloads/html-css-js.pdf',             '2026-04-14 02:45:32', '2026-04-14 02:51:20'),
(3, 'book',   'Modern Web Design - Soft Copy Edition',         '149.00', 'Digital PDF version - instant download.',                    'prod_69ddab9b9a0f42.88683277.png',  999, '0.00', 0, 1, 'https://auraedu.edu/downloads/modern-web-design.pdf',       '2026-04-14 02:45:32', '2026-04-14 02:51:07'),
(4, 'course', 'Full-Stack Web Bootcamp (HTML/CSS/PHP/MySQL)',  '699.00', 'Project-based full stack track - Instant access.',           'prod_69ddab792b90f2.71553151.webp', 150, '0.00', 1, 1, 'https://auraedu.edu/courses/fullstack-bootcamp/access',     '2026-04-14 02:45:32', '2026-04-14 02:50:33'),
(5, 'course', 'PHP for Beginners: From Zero to CRUD',          '249.00', 'Beginner-friendly PHP course with lifetime access.',         'prod_69ddab6fd1e303.50209836.jpg',  300, '0.00', 0, 1, 'https://auraedu.edu/courses/php-beginners/access',          '2026-04-14 02:45:32', '2026-04-14 02:50:23'),
-- ── Additional records ───────────────────────────────────────────────────────
(6, 'book',   'JavaScript: The Good Parts',                    '189.00', 'Concise guide to JavaScript best practices.',                'no-image.jpg',                     40,  '5.00', 0, 1, 'https://auraedu.edu/downloads/js-good-parts.pdf',           '2026-04-15 08:00:00', '2026-04-15 08:00:00'),
(7, 'course', 'UI/UX Design Fundamentals',                     '399.00', 'Learn user-centred design from scratch.',                   'no-image.jpg',                     200, '10.00',1, 1, 'https://auraedu.edu/courses/uiux-fundamentals/access',      '2026-04-15 09:00:00', '2026-04-15 09:00:00'),
(8, 'book',   'Clean Code: A Handbook of Agile Software',      '229.00', 'Best practices for writing readable, maintainable code.',   'no-image.jpg',                     35,  '0.00', 0, 1, 'https://auraedu.edu/downloads/clean-code.pdf',              '2026-04-15 10:00:00', '2026-04-15 10:00:00'),
(9, 'course', 'Python for Data Science',                       '549.00', 'Hands-on Python course covering pandas, numpy & ML basics.','no-image.jpg',                     120, '15.00',1, 1, 'https://auraedu.edu/courses/python-data-science/access',    '2026-04-15 11:00:00', '2026-04-15 11:00:00'),
(10,'book',   'Database Design and Relational Theory',         '199.00', 'Deep dive into relational DB design principles.',           'no-image.jpg',                     50,  '0.00', 0, 1, 'https://auraedu.edu/downloads/db-design-theory.pdf',        '2026-04-15 12:00:00', '2026-04-15 12:00:00');

-- --------------------------------------------------------
-- Table structure for table `books`
-- --------------------------------------------------------

CREATE TABLE `books` (
  `product_id` int(11) NOT NULL,
  `author` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher_provider` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publication_year` year(4) DEFAULT NULL,
  `pages_count` int(11) DEFAULT NULL,
  `language` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'English',
  `book_format` enum('hard_copy','soft_copy') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hard_copy',
  `delivery_format` enum('physical','digital') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'physical'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `books` (`product_id`, `author`, `publisher_provider`, `publication_year`, `pages_count`, `language`, `book_format`, `delivery_format`) VALUES
-- ── Original records ─────────────────────────────────────────────────────────
(1,  'Paul McFedries',                    'John Wiley',       2024, 822,  'English', 'hard_copy', 'physical'),
(2,  'Julie C. Meloni, Jennifer Kyrnin',  'Pearson / Sams',   NULL, NULL, 'English', 'hard_copy', 'physical'),
(3,  'AuraEdu Team',                      'AuraEdu Press',    2026, 180,  'English', 'soft_copy', 'digital'),
-- ── Additional records ───────────────────────────────────────────────────────
(6,  'Douglas Crockford',                 'O\'Reilly Media',  2008, 172,  'English', 'soft_copy', 'digital'),
(8,  'Robert C. Martin',                  'Prentice Hall',    2008, 431,  'English', 'hard_copy', 'physical'),
(10, 'C.J. Date',                         'O\'Reilly Media',  2012, 278,  'English', 'hard_copy', 'physical');

-- --------------------------------------------------------
-- Table structure for table `courses`
-- --------------------------------------------------------

CREATE TABLE `courses` (
  `product_id` int(11) NOT NULL,
  `instructor` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci DEFAULT 'beginner',
  `access_duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Lifetime',
  `delivery_format` enum('digital') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'digital'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `courses` (`product_id`, `instructor`, `duration_hours`, `level`, `access_duration`, `delivery_format`) VALUES
-- ── Original records ─────────────────────────────────────────────────────────
(4, 'Eng. Sami Al-Rasheed',    120, 'intermediate', 'Lifetime', 'digital'),
(5, 'Dr. Huda Al-Qahtani',     45,  'beginner',     'Lifetime', 'digital'),
-- ── Additional records ───────────────────────────────────────────────────────
(7, 'Dr. Mona Al-Ghamdi',      60,  'beginner',     'Lifetime', 'digital'),
(9, 'Eng. Khalid Al-Mutairi',  95,  'intermediate', 'Lifetime', 'digital');

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reviews` (`review_id`, `product_id`, `customer_id`, `rating`, `review_text`, `created_at`) VALUES
-- ── Original records ─────────────────────────────────────────────────────────
(1, 1, 1, 5, 'كتاب ممتاز جداً، شرح واضح وأمثلة عملية',             '2026-04-14 02:45:32'),
(2, 1, 2, 4, 'مفيد للمبتدئين والمحترفين',                             '2026-04-14 02:45:32'),
(3, 4, 1, 5, 'الكورس عملي جداً، مشاريع حقيقية',                      '2026-04-14 02:45:32'),
-- ── Additional records ───────────────────────────────────────────────────────
(4, 2, 3, 4, 'Great all-in-one reference, very comprehensive.',        '2026-04-15 10:20:00'),
(5, 3, 4, 5, 'Excellent digital version, instant download worked.',    '2026-04-15 11:00:00'),
(6, 5, 5, 5, 'Best PHP course for beginners, very clear examples.',   '2026-04-15 12:30:00'),
(7, 7, 6, 5, 'محتوى احترافي جداً، الشرح مرتب ومنظم بشكل رائع.',     '2026-04-15 13:00:00'),
(8, 4, 7, 4, 'الكورس شامل ومفيد، أنصح به بشدة للمبتدئين.',           '2026-04-15 14:00:00'),
(9, 9, 8, 5, 'Python course is excellent, datasets are real-world.',  '2026-04-15 15:00:00');

-- --------------------------------------------------------
-- Table structure for table `cart`
-- --------------------------------------------------------

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cart` (`cart_id`, `customer_id`, `product_id`, `quantity`, `added_at`) VALUES
(1, 1, 2, 1, '2026-04-15 08:30:00'),
(2, 2, 4, 1, '2026-04-15 09:00:00'),
(3, 3, 1, 2, '2026-04-15 09:45:00'),
(4, 4, 5, 1, '2026-04-15 10:10:00'),
(5, 5, 3, 1, '2026-04-15 11:20:00'),
(6, 6, 7, 1, '2026-04-15 12:00:00'),
(7, 7, 9, 1, '2026-04-15 13:15:00'),
(8, 8, 6, 1, '2026-04-15 14:00:00');

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `order_status` enum('pending','paid','processing','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` enum('cash_on_delivery','card','bank_transfer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'card',
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `order_status`, `payment_method`, `shipping_address`, `total_amount`) VALUES
(1, 1, '2026-04-15 09:00:00', 'paid',        'card',              'Dammam, Al-Noor District',            '209.00'),
(2, 2, '2026-04-15 09:30:00', 'completed',   'bank_transfer',     'Khobar, King Fahd Road',              '699.00'),
(3, 3, '2026-04-15 10:00:00', 'processing',  'card',              'Riyadh, Al-Malaz District, Bldg 12',  '430.00'),
(4, 4, '2026-04-15 10:45:00', 'pending',     'cash_on_delivery',  'Jeddah, Al-Hamraa, Villa 5',          '249.00'),
(5, 5, '2026-04-15 11:15:00', 'paid',        'card',              'Makkah, Al-Aziziyah, Block 3',        '149.00'),
(6, 6, '2026-04-15 12:00:00', 'cancelled',   'card',              'Madinah, Al-Anbariyah St, Bldg 7',    '699.00'),
(7, 7, '2026-04-15 13:00:00', 'paid',        'bank_transfer',     'Riyadh, Olaya District, Tower B',     '548.00'),
(8, 8, '2026-04-15 14:00:00', 'processing',  'card',              'Taif, Al-Shafa Road, Villa 22',        '399.00');

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `line_total`) VALUES
(1,  1, 1,  1, '209.00', '209.00'),
(2,  2, 4,  1, '699.00', '699.00'),
(3,  3, 2,  1, '281.00', '281.00'),
(4,  3, 5,  1, '149.00', '149.00'),
(5,  4, 5,  1, '249.00', '249.00'),
(6,  5, 3,  1, '149.00', '149.00'),
(7,  6, 4,  1, '699.00', '699.00'),
(8,  7, 9,  1, '549.00', '549.00'),
(9,  7, 6,  1, '189.00', '189.00'),
(10, 8, 7,  1, '399.00', '399.00');

-- --------------------------------------------------------
-- Table structure for table `contact_messages`
-- --------------------------------------------------------

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `sender_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `contact_messages` (`message_id`, `customer_id`, `sender_name`, `sender_email`, `subject`, `message_body`, `created_at`) VALUES
(1, 1,    'Aisha Alqahtani',   'aisha.q@auraedu.edu',  'Request for invoice',          'Hello, I need a VAT invoice for my recent order. Please send it to my email.',                           '2026-04-15 09:00:00'),
(2, 2,    'Omar Alharbi',      'omar.h@auraedu.edu',   'Course access issue',          'I purchased the Full-Stack Bootcamp but cannot access the materials. Please assist.',                    '2026-04-15 09:30:00'),
(3, 3,    'Sara Al-Malki',     'sara.m@gmail.com',     'Book delivery inquiry',        'When will my physical book order be shipped? I have not received a tracking number yet.',                '2026-04-15 10:15:00'),
(4, NULL, 'Rawan Al-Enezi',    'rawan.e@gmail.com',    'Question about PHP course',    'Is the PHP for Beginners course suitable for someone with no programming background at all?',           '2026-04-15 11:00:00'),
(5, 4,    'Hessa Al-Dosari',   'hessa.d@hotmail.com',  'Refund request',               'I would like to request a refund for my order. The content did not meet my expectations.',              '2026-04-15 11:45:00'),
(6, 5,    'Maha Al-Rashidi',   'maha.r@outlook.com',   'Discount code not working',    'I received a discount code via email but it shows as invalid at checkout. Please help.',                '2026-04-15 12:30:00'),
(7, 6,    'Dina Al-Harbi',     'dina.h@gmail.com',     'Certificate of completion',    'Do you issue certificates after completing a course? I finished the UI/UX course yesterday.',          '2026-04-15 13:00:00'),
(8, NULL, 'Nouf Al-Shehri',    'nouf.s@gmail.com',     'Partnership inquiry',          'We are an educational institution and would like to discuss a bulk purchase deal for your courses.',    '2026-04-15 14:00:00');

-- --------------------------------------------------------
--  Indexes
-- --------------------------------------------------------

ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `books`
  ADD PRIMARY KEY (`product_id`);

ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_customer` (`customer_id`),
  ADD KEY `fk_cart_product` (`product_id`);

ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_contact_customer` (`customer_id`);

ALTER TABLE `courses`
  ADD PRIMARY KEY (`product_id`);

ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_customer` (`customer_id`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_product` (`product_id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_type` (`product_type`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_featured` (`is_featured`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_reviews_product` (`product_id`),
  ADD KEY `fk_reviews_customer` (`customer_id`);

-- --------------------------------------------------------
--  AUTO_INCREMENT
-- --------------------------------------------------------

ALTER TABLE `admins`          MODIFY `admin_id`       int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `cart`            MODIFY `cart_id`         int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `contact_messages`MODIFY `message_id`      int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `customers`       MODIFY `customer_id`     int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `orders`          MODIFY `order_id`        int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `order_items`     MODIFY `order_item_id`   int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `products`        MODIFY `product_id`      int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `reviews`         MODIFY `review_id`       int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

-- --------------------------------------------------------
--  Foreign Key Constraints
-- --------------------------------------------------------

ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_product`  FOREIGN KEY (`product_id`)  REFERENCES `products`  (`product_id`) ON DELETE CASCADE;

ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_contact_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

ALTER TABLE `courses`
  ADD CONSTRAINT `fk_courses_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order`   FOREIGN KEY (`order_id`)   REFERENCES `orders`   (`order_id`),
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_product`  FOREIGN KEY (`product_id`)  REFERENCES `products`  (`product_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
