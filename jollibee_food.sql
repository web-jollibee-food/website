-- Tạo cơ sở dữ liệu nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS `jollibee_food` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jollibee_food`;

-- 1. Bảng Vai trò (Roles)
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng Người dùng (Users)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng Danh mục (Categories)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng Sản phẩm (Products)
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng Đơn hàng (Orders)
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `ho_ten` VARCHAR(100) NOT NULL,
  `so_dien_thoai` VARCHAR(20) NOT NULL,
  `dia_chi` TEXT NOT NULL,
  `tong_tien` DECIMAL(10, 2) NOT NULL,
  `phuong_thuc_tt` VARCHAR(50) NOT NULL,
  `trang_thai` VARCHAR(50) DEFAULT 'Mới',
  `ghi_chu` TEXT DEFAULT NULL,
  `ngay_dat` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng Chi tiết Đơn hàng (Order Items)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `ten_sp` VARCHAR(255) NOT NULL,
  `gia_ban` DECIMAL(10, 2) NOT NULL,
  `so_luong` INT NOT NULL,
  `thanh_tien` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chèn dữ liệu mẫu cho Roles
INSERT INTO `roles` (`id`, `name`) VALUES 
(1, 'admin'), 
(2, 'user')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Chèn dữ liệu mẫu cho Categories
INSERT INTO `categories` (`id`, `name`) VALUES 
(1, 'Gà Giòn Vui Vẻ'),
(2, 'Mì Ý Jolly'),
(3, 'Burger & Sandwich'),
(4, 'Món Đi Kèm'),
(5, 'Nước Uống & Tráng Miệng')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Chèn dữ liệu mẫu cho Products
INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `image_url`) VALUES
(1, '1 Miếng Gà Giòn Vui Vẻ', '1 miếng gà giòn vui vẻ thơm ngon, da giòn rụm, thịt mềm mọng nước.', 37000.00, '🍗'),
(1, '2 Miếng Gà Giòn Vui Vẻ', '2 miếng gà giòn vui vẻ kèm 1 phần nước ngọt mát lạnh.', 72000.00, '🍗'),
(1, 'Combo Gà Giòn & Khoai Tây', '1 miếng gà giòn vui vẻ + 1 khoai tây chiên vừa + 1 ly Pepsi.', 65000.00, '🍗'),
(2, 'Mì Ý Jolly Sốt Bò Bằm', 'Mì Ý thơm ngon hòa quyện cùng sốt bò bằm cà chua đậm đà.', 37000.00, '🍝'),
(2, 'Combo Mì Ý & Pepsi', '1 phần mì ý sốt bò bằm Jolly + 1 ly Pepsi sảng khoái.', 50000.00, '🍝'),
(3, 'Burger Gà Giòn', 'Bánh mì burger mềm kẹp thịt gà chiên giòn, rau xà lách tươi.', 35000.00, '🍔'),
(3, 'Burger Bò Sốt Jolly', 'Thịt bò nướng thơm phức kết hợp với sốt Jolly đặc chế.', 30000.00, '🍔'),
(4, 'Khoai Tây Chiên (Vừa)', 'Khoai tây chiên vàng ruộm, giòn tan bên ngoài, bùi thơm bên trong.', 22000.00, '🍟'),
(4, 'Gà Không Xương Jolly (3 Miếng)', '3 miếng ức gà phi-lê lăn bột chiên giòn, nước sốt chấm tuyệt hảo.', 35000.00, '🍗'),
(5, 'Nước Ngọt Pepsi (Ly Lớn)', 'Nước ngọt Pepsi mát lạnh giúp bữa ăn thêm trọn vẹn.', 15000.00, '🥤'),
(5, 'Kem Sundae Sô-cô-la', 'Kem sữa tươi mềm mịn kết hợp với sốt sô-cô-la ngọt ngào.', 17000.00, '🍦'),
(5, 'Bánh Khoai Môn Jolly', 'Vỏ bánh chiên giòn rụm kẹp nhân khoai môn thơm lừng.', 17000.00, '🥧');

-- Chèn tài khoản admin mặc định an toàn (Mật khẩu: Admin@123)
INSERT INTO `users` (`id`, `role_id`, `full_name`, `email`, `phone`, `password_hash`) VALUES
(1, 1, 'Quản Trị Viên Jollibee', 'admin@jollibee.vn', '0123456789', '$2y$12$7gIJUSLMU3qwqNevbRJNoOCp7QesKnyFEWmBZMZQ2Ej/OmWfv0Xpe')
ON DUPLICATE KEY UPDATE `full_name`=VALUES(`full_name`);
