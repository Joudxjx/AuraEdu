DROP TABLE IF EXISTS `Product`;
DROP TABLE IF EXISTS `Admin`;

-- Create the admin table.
CREATE TABLE `Admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the product table.
CREATE TABLE `Product` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admin_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT 0,
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_admin_idx` (`admin_id`),
  CONSTRAINT `product_admin_fk` FOREIGN KEY (`admin_id`) REFERENCES `Admin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add the default admin account.
INSERT INTO `Admin` (`name`, `email`, `password`) VALUES
('AuraEdu Admin', 'admin@auraedu.edu', '$2y$10$CEZLrIw9XghFEbeb/Vjdv.CfGXhoMswxTRW4ZWGCEGlTABobUiMrW');

-- Add the first products shown in the shop.
INSERT INTO `Product` (`admin_id`, `name`, `price`, `stock`, `image`, `description`) VALUES
(1, 'Web Coding & Development', 209.00, 25, 'prod_69ddabb5156cc6.05674825.png', 'Practical web development book.'),
(1, 'HTML CSS and JavaScript', 281.00, 20, 'prod_69ddaba806dd68.36728291.png', 'Complete front-end learning guide.'),
(1, 'Modern Web Design', 149.00, 40, 'prod_69ddab9b9a0f42.88683277.png', 'Modern design principles and examples.'),
(1, 'Full Stack Web Bootcamp', 699.00, 15, 'prod_69ddab792b90f2.71553151.webp', 'Hands-on PHP and MySQL bootcamp.'),
(1, 'PHP for Beginners', 249.00, 30, 'prod_69ddab6fd1e303.50209836.jpg', 'Simple beginner PHP course.');
