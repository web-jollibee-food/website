<?php
// process/actions.php
// Handles all POST actions in the application.
// Runs in the scope of index.php, so database connection $conn, and variables $error/$success are available.

// 1. Đăng ký tài khoản khách hàng
if (isset($_POST['btnRegister'])) {
    $fullName = trim($_POST['full_name']); 
    $email = strtolower(trim($_POST['email']));
    $phone = trim($_POST['phone']); 
    $password = $_POST['password'];
    
    if ($_POST['password'] !== $_POST['confirm_password']) { 
        $error = 'Mật khẩu xác nhận không khớp.'; 
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?"); 
        $stmt->bind_param("s", $email); 
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) { 
            $error = 'Email này đã được đăng ký.'; 
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $conn->prepare("INSERT INTO users (role_id, full_name, email, phone, password_hash) VALUES (2, ?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullName, $email, $phone, $hash); 
            $stmt->execute();
            header("Location: index.php?page=login&registered=1"); 
            exit;
        }
    }
}

// 2. Đăng nhập
if (isset($_POST['btnLogin'])) {
    $email = strtolower(trim($_POST['email'])); 
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = ?");
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc(); 
    $stmt->close();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role_name'] ?? 'user'; 
        $_SESSION['logged_in'] = true;
        
        $redirect = $_GET['redirect'] ?? 'home';
        if (!in_array($redirect, ['home', 'checkout'])) {
            $redirect = 'home';
        }
        header("Location: index.php?page=" . urlencode($redirect)); 
        exit;
    } else { 
        $error = 'Tài khoản hoặc mật khẩu không chính xác.'; 
    }
}

// 3. Thêm giỏ hàng
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $id = (int)$_POST['product_id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?"); 
    $stmt->bind_param("i", $id); 
    $stmt->execute();
    $p_name = $stmt->get_result()->fetch_assoc()['name'] ?? "Món ăn";
    header('Location: index.php?page=home&added=' . urlencode($p_name)); 
    exit;
}

// 4. Xóa món / Xóa giỏ hàng
if (isset($_POST['action']) && $_POST['action'] === 'remove_item') { 
    unset($_SESSION['cart'][(int)$_POST['product_id']]); 
    header('Location: index.php?page=home'); 
    exit; 
}
if (isset($_POST['action']) && $_POST['action'] === 'clear_cart') { 
    $_SESSION['cart'] = []; 
    header('Location: index.php?page=home'); 
    exit; 
}

if (isset($_POST['action']) && $_POST['action'] === 'update_qty') {
    $id = (int)$_POST['product_id'];
    $new_qty = (int)$_POST['quantity'];
    if ($new_qty > 0) {
        $_SESSION['cart'][$id] = $new_qty;
    } else {
        unset($_SESSION['cart'][$id]);
    }
    header('Location: index.php?page=home');
    exit;
}

// 5. Xử lý Đặt hàng (Checkout)
if (isset($_POST['btnCheckout'])) {
    if (empty($_SESSION['cart'])) { 
        header("Location: index.php"); 
        exit; 
    }
    $ho_ten = trim($_POST['ho_ten']); 
    $so_dien_thoai = trim($_POST['so_dien_thoai']); 
    $dia_chi = trim($_POST['dia_chi']);
    $ghi_chu = trim($_POST['ghi_chu']); 
    $phuong_thuc_tt = $_POST['phuong_thuc_tt'];
    
    $cart_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($cart_ids)); 
    $stmt->bind_param($types, ...$cart_ids); 
    $stmt->execute();
    $res_prods = $stmt->get_result();
    
    $subtotal = 0; 
    $order_items = [];
    while($row = $res_prods->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['id']]; 
        $actual_price = ($row['price_discount'] > 0 && $row['price_discount'] < $row['price']) ? $row['price_discount'] : $row['price'];
        $item_total = $actual_price * $qty; 
        $subtotal += $item_total;
        $row['qty'] = $qty; 
        $row['item_total'] = $item_total; 
        $row['actual_price'] = $actual_price;
        $order_items[] = $row;
    }
    $shipping_fee = ($subtotal >= 150000) ? 0 : 20000; 
    
    // Tính toán mã giảm giá nếu có
    $promo_code = null;
    $discount_amount = 0.00;
    if (!empty($_SESSION['applied_promo'])) {
        $promo_code = $_SESSION['applied_promo']['code'];
        $promo_type = $_SESSION['applied_promo']['type'];
        $promo_value = (float)$_SESSION['applied_promo']['value'];
        
        if ($promo_type === 'percentage') {
            $discount_amount = ($subtotal * $promo_value) / 100;
        } else {
            $discount_amount = $promo_value;
        }
        
        // Không cho phép giảm quá tổng tiền hàng
        if ($discount_amount > $subtotal) {
            $discount_amount = $subtotal;
        }
    }
    
    $grand_total = ($subtotal - $discount_amount) + $shipping_fee;
    
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, ho_ten, so_dien_thoai, dia_chi, tong_tien, phuong_thuc_tt, trang_thai_tt, trang_thai_gh, ghi_chu, ma_giam_gia, so_tien_giam) VALUES (?, ?, ?, ?, ?, ?, 'Chưa thanh toán', 'Chờ xác nhận', ?, ?, ?)");
        $u_id = $_SESSION['user_id'] ?? null; 
        $stmt->bind_param("isssdsssd", $u_id, $ho_ten, $so_dien_thoai, $dia_chi, $grand_total, $phuong_thuc_tt, $ghi_chu, $promo_code, $discount_amount); 
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        $stmt_detail = $conn->prepare("INSERT INTO order_items (order_id, product_id, ten_sp, gia_ban, so_luong, thanh_tien) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($order_items as $item) { 
            $stmt_detail->bind_param("iissid", $order_id, $item['id'], $item['name'], $item['actual_price'], $item['qty'], $item['item_total']); 
            $stmt_detail->execute(); 
        }
        
        // Cập nhật số lượt dùng của mã giảm giá
        if (!empty($promo_code)) {
            $stmt_promo = $conn->prepare("UPDATE promotions SET used_count = used_count + 1 WHERE code = ?");
            $stmt_promo->bind_param("s", $promo_code);
            $stmt_promo->execute();
            $stmt_promo->close();
        }
        
        $conn->commit(); 
        $_SESSION['cart'] = [];
        unset($_SESSION['applied_promo']); // Xóa mã giảm giá sau khi đặt hàng thành công
        
        if ($phuong_thuc_tt === 'Chuyển khoản') {
            header("Location: index.php?page=qrcode&id=" . $order_id);
        } else { 
            header("Location: index.php?page=success&id=" . $order_id); 
        } 
        exit;
    } catch (Exception $e) { 
        $conn->rollback(); 
        die("Lỗi đặt hàng: " . $e->getMessage()); 
    }
}

// 6. XỬ LÝ ADMIN: THÊM DANH MỤC MỚI
if (isset($_POST['btnAddCategory'])) {
    checkAdminOrDie(); 
    $cat_name = trim($_POST['category_name']);
    if (!empty($cat_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)"); 
        $stmt->bind_param("s", $cat_name); 
        $stmt->execute();
        header("Location: index.php?page=admin_dashboard"); 
        exit;
    }
}

// 7. XỬ LÝ ADMIN: CẬP NHẬT DANH MỤC
if (isset($_POST['btnUpdateCategory'])) {
    checkAdminOrDie(); 
    $cat_id = (int)$_POST['category_id']; 
    $new_name = trim($_POST['category_name']);
    if (!empty($new_name) && $cat_id > 0) {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?"); 
        $stmt->bind_param("si", $new_name, $cat_id); 
        $stmt->execute();
        header("Location: index.php?page=admin_dashboard"); 
        exit;
    }
}

// 8. XỬ LÝ ADMIN: THÊM SẢN PHẨM MỚI
if (isset($_POST['btnAddProduct'])) {
    checkAdminOrDie();
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);

    // Xử lý upload file ảnh nếu có
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = $_FILES['image_file']['name'];
        
        // Xác minh file là ảnh thực tế bằng getimagesize()
        if (getimagesize($file_tmp) !== false) {
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = 'uploads/' . $new_filename;
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_url = $upload_path;
                }
            }
        }
    }

    if (empty($image_url)) {
        $image_url = '🍔';
    }

    $price_discount = isset($_POST['price_discount']) && $_POST['price_discount'] !== '' ? (float)$_POST['price_discount'] : 0.0;

    if (!empty($name) && $category_id > 0 && $price >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, price_discount, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddds", $category_id, $name, $description, $price, $price_discount, $image_url);
        $stmt->execute();
        header("Location: index.php?page=admin_products");
        exit;
    }
}

// 9. XỬ LÝ ADMIN: CẬP NHẬT SẢN PHẨM
if (isset($_POST['btnUpdateProduct'])) {
    checkAdminOrDie();
    $id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);

    // Xử lý upload file ảnh nếu có
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = $_FILES['image_file']['name'];
        
        // Xác minh file là ảnh thực tế bằng getimagesize()
        if (getimagesize($file_tmp) !== false) {
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = 'uploads/' . $new_filename;
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_url = $upload_path;
                }
            }
        }
    }

    if ($id > 0 && !empty($name) && $category_id > 0 && $price >= 0) {
        // Lấy đường dẫn ảnh cũ để xóa nếu có thay đổi
        $stmt_old = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $old_prod = $stmt_old->get_result()->fetch_assoc();
        $stmt_old->close();

        if ($old_prod && $old_prod['image_url'] !== $image_url) {
            if (strpos($old_prod['image_url'], 'uploads/') === 0 && file_exists($old_prod['image_url'])) {
                unlink($old_prod['image_url']);
            }
        }

        $price_discount = isset($_POST['price_discount']) && $_POST['price_discount'] !== '' ? (float)$_POST['price_discount'] : 0.0;

        $stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, price_discount = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("isdddsi", $category_id, $name, $description, $price, $price_discount, $image_url, $id);
        $stmt->execute();
        header("Location: index.php?page=admin_products");
        exit;
    }
}

// 10. XỬ LÝ ADMIN: CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
if (isset($_POST['btnUpdateOrderStatus'])) {
    checkAdminOrDie();
    $order_id = (int)$_POST['order_id'];

    if ($order_id > 0) {
        if (isset($_POST['trang_thai_gh'])) {
            $trang_thai_gh = trim($_POST['trang_thai_gh']);
            $stmt = $conn->prepare("UPDATE orders SET trang_thai_gh = ? WHERE id = ?");
            $stmt->bind_param("si", $trang_thai_gh, $order_id);
            $stmt->execute();
            $stmt->close();
        }
        if (isset($_POST['trang_thai_tt'])) {
            $trang_thai_tt = trim($_POST['trang_thai_tt']);
            $stmt = $conn->prepare("UPDATE orders SET trang_thai_tt = ? WHERE id = ?");
            $stmt->bind_param("si", $trang_thai_tt, $order_id);
            $stmt->execute();
            $stmt->close();
        }
        $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?page=admin_orders';
        header("Location: " . $redirect);
        exit;
    }
}

// 11. XỬ LÝ QUÊN MẬT KHẨU
if (isset($_POST['btnForgotPassword'])) {
    $email = strtolower(trim($_POST['email']));
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($user) {
        $code = strval(rand(100000, 999999));
        $stmt_up = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt_up->bind_param("ss", $code, $email);
        $stmt_up->execute();
        $stmt_up->close();
        
        $subject = "Ma xác minh dat lai mat khau Jollibee";
        $message = "Chào bạn,\n\nMã xác minh đặt lại mật khẩu tài khoản Jollibee của bạn là: $code\nMã này có hiệu lực trong vòng 1 giờ.\n\nTrân trọng,\nHệ thống Jollibee Food.";
        
        if (SMTP_USER === 'YOUR_EMAIL@gmail.com') {
            // Chế độ demo: ghi file cục bộ rất nhanh nên chạy đồng bộ
            send_mail_smtp($email, $subject, $message);
            header("Location: index.php?page=reset_password&email=" . urlencode($email) . "&logged=1");
            exit;
        } else {
            // Chế độ gửi thật: chạy ngầm bất đồng bộ (tương thích cả Windows & Linux)
            // Mã hóa Base64 tiêu đề và nội dung để tránh mất mát ký tự có dấu (Vietnamese accents) và lỗi ngắt dòng trên Windows CLI
            $subject_encoded = base64_encode($subject);
            $message_encoded = base64_encode($message);
            
            $script_path = __DIR__ . '/send_mail_bg.php';
            $to_arg = escapeshellarg($email);
            $subject_arg = escapeshellarg($subject_encoded);
            $message_arg = escapeshellarg($message_encoded);
            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $cmd = "start /B php \"$script_path\" $to_arg $subject_arg $message_arg";
                pclose(popen($cmd, "r"));
            } else {
                $cmd = "php \"$script_path\" $to_arg $subject_arg $message_arg > /dev/null 2>&1 &";
                exec($cmd);
            }
            
            header("Location: index.php?page=reset_password&email=" . urlencode($email) . "&sent=1");
            exit;
        }
    } else {
        $error = 'Email này không tồn tại trong hệ thống.';
    }
}

// 12. XỬ LÝ ĐẶT LẠI MẬT KHẨU
if (isset($_POST['btnResetPassword'])) {
    $email = strtolower(trim($_POST['email']));
    $code = trim($_POST['code']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else if (strlen($password) < 6) {
        $error = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()");
        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($user) {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt_up = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
            $stmt_up->bind_param("ss", $hash, $email);
            $stmt_up->execute();
            $stmt_up->close();
            
            header("Location: index.php?page=login&pw_reset=1");
            exit;
        } else {
            $error = 'Mã xác minh không chính xác hoặc đã hết hạn.';
        }
    }
}

// 13. KHÁCH HÀNG TỰ HỦY ĐƠN HÀNG
if (isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    if (empty($_SESSION['logged_in'])) {
        header("Location: index.php?page=login");
        exit;
    }
    $order_id = (int)$_POST['order_id'];
    $user_id = $_SESSION['user_id'];
    
    if ($order_id > 0) {
        // Chỉ cho phép hủy nếu đơn hàng thuộc về user đăng nhập và trạng thái giao hàng là 'Chờ xác nhận'
        $stmt = $conn->prepare("UPDATE orders SET trang_thai_gh = 'Đã hủy' WHERE id = ? AND user_id = ? AND trang_thai_gh = 'Chờ xác nhận'");
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: index.php?page=my_orders");
    exit;
}

// 14. CẬP NHẬT THÔNG TIN CÁ NHÂN KHÁCH HÀNG
if (isset($_POST['btnUpdateProfile'])) {
    if (empty($_SESSION['logged_in'])) {
        header("Location: index.php?page=login");
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    if (empty($full_name) || empty($phone)) {
        $error_profile = 'Họ tên và số điện thoại không được để trống.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $full_name; // Cập nhật lại session
            $success_profile = 'Cập nhật thông tin cá nhân thành công!';
        } else {
            $error_profile = 'Lỗi cập nhật thông tin trong cơ sở dữ liệu.';
        }
        $stmt->close();
    }
}

// 15. ĐỔI MẬT KHẨU
if (isset($_POST['btnUpdatePassword'])) {
    if (empty($_SESSION['logged_in'])) {
        header("Location: index.php?page=login");
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error_password = 'Mật khẩu xác nhận không khớp.';
    } else if (strlen($new_password) < 6) {
        $error_password = 'Mật khẩu mới phải có tối thiểu 6 ký tự.';
    } else {
        // Lấy hash cũ để kiểm tra
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($user_data && password_verify($old_password, $user_data['password_hash'])) {
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt_up = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt_up->bind_param("si", $new_hash, $user_id);
            if ($stmt_up->execute()) {
                $success_password = 'Đổi mật khẩu thành công!';
            } else {
                $error_password = 'Lỗi cập nhật mật khẩu mới.';
            }
            $stmt_up->close();
        } else {
            $error_password = 'Mật khẩu hiện tại không chính xác.';
        }
    }
}

// 16. ADMIN: THÊM MÃ KHUYẾN MÃI MỚI
if (isset($_POST['action']) && $_POST['action'] === 'add_promotion') {
    checkAdminOrDie();
    $code = strtoupper(trim($_POST['code']));
    $discount_type = trim($_POST['discount_type']);
    $discount_value = (float)$_POST['discount_value'];
    $max_uses = (int)$_POST['max_uses'];
    $expiry_date = trim($_POST['expiry_date']);
    $status = (int)$_POST['status'];
    
    if (empty($code) || empty($discount_type) || $discount_value <= 0 || $max_uses <= 0 || empty($expiry_date)) {
        $error_promo = 'Vui lòng nhập đầy đủ thông tin hợp lệ.';
        $page = 'admin_promotion_add'; // Giữ lại trang để hiện lỗi
    } else {
        // Kiểm tra trùng mã
        $stmt_check = $conn->prepare("SELECT id FROM promotions WHERE code = ?");
        $stmt_check->bind_param("s", $code);
        $stmt_check->execute();
        if ($stmt_check->get_result()->fetch_assoc()) {
            $error_promo = 'Mã giảm giá này đã tồn tại.';
            $page = 'admin_promotion_add';
        } else {
            $stmt = $conn->prepare("INSERT INTO promotions (code, discount_value, discount_type, expiry_date, status, max_uses) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdssii", $code, $discount_value, $discount_type, $expiry_date, $status, $max_uses);
            $stmt->execute();
            $stmt->close();
            header("Location: index.php?page=admin_promotions");
            exit;
        }
        $stmt_check->close();
    }
}

// 17. ADMIN: CẬP NHẬT MÃ KHUYẾN MÃI
if (isset($_POST['action']) && $_POST['action'] === 'update_promotion') {
    checkAdminOrDie();
    $id = (int)$_POST['promotion_id'];
    $code = strtoupper(trim($_POST['code']));
    $discount_type = trim($_POST['discount_type']);
    $discount_value = (float)$_POST['discount_value'];
    $max_uses = (int)$_POST['max_uses'];
    $expiry_date = trim($_POST['expiry_date']);
    $status = (int)$_POST['status'];
    
    if ($id > 0 && !empty($code) && !empty($discount_type) && $discount_value > 0 && $max_uses > 0 && !empty($expiry_date)) {
        // Kiểm tra trùng mã với dòng khác
        $stmt_check = $conn->prepare("SELECT id FROM promotions WHERE code = ? AND id != ?");
        $stmt_check->bind_param("si", $code, $id);
        $stmt_check->execute();
        if ($stmt_check->get_result()->fetch_assoc()) {
            $error_promo = 'Mã giảm giá này đã tồn tại ở bản ghi khác.';
            $page = 'admin_promotion_edit';
            $_GET['id'] = $id; // Giữ lại id để load form
        } else {
            $stmt = $conn->prepare("UPDATE promotions SET code = ?, discount_value = ?, discount_type = ?, expiry_date = ?, status = ?, max_uses = ? WHERE id = ?");
            $stmt->bind_param("sdssiii", $code, $discount_value, $discount_type, $expiry_date, $status, $max_uses, $id);
            $stmt->execute();
            $stmt->close();
            header("Location: index.php?page=admin_promotions");
            exit;
        }
        $stmt_check->close();
    }
}

// 18. ADMIN: XÓA MÃ KHUYẾN MÃI
if (isset($_POST['action']) && $_POST['action'] === 'delete_promotion') {
    checkAdminOrDie();
    $id = (int)$_POST['promotion_id'];
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: index.php?page=admin_promotions");
    exit;
}

// 19. CHECKOUT: ÁP DỤNG MÃ GIẢM GIÁ
if (isset($_POST['action']) && $_POST['action'] === 'apply_promo') {
    $code = strtoupper(trim($_POST['promo_code']));
    if (empty($code)) {
        $_SESSION['promo_error'] = 'Vui lòng nhập mã giảm giá!';
        header("Location: index.php?page=checkout");
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM promotions WHERE code = ? AND status = 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $promo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($promo) {
        $expiry = strtotime($promo['expiry_date']);
        if ($expiry < time()) {
            $_SESSION['promo_error'] = 'Mã giảm giá này đã hết hạn sử dụng!';
        } elseif ($promo['used_count'] >= $promo['max_uses']) {
            $_SESSION['promo_error'] = 'Mã giảm giá này đã hết lượt sử dụng!';
        } else {
            $_SESSION['applied_promo'] = [
                'code' => $promo['code'],
                'type' => $promo['discount_type'],
                'value' => $promo['discount_value']
            ];
            unset($_SESSION['promo_error']);
            $_SESSION['promo_success'] = 'Áp dụng mã giảm giá thành công!';
        }
    } else {
        $_SESSION['promo_error'] = 'Mã giảm giá không tồn tại hoặc đã bị khóa!';
    }
    header("Location: index.php?page=checkout");
    exit;
}

// 20. CHECKOUT: GỠ BỎ MÃ GIẢM GIÁ
if (isset($_POST['action']) && $_POST['action'] === 'remove_promo') {
    unset($_SESSION['applied_promo']);
    unset($_SESSION['promo_error']);
    unset($_SESSION['promo_success']);
    header("Location: index.php?page=checkout");
    exit;
}

// 21. TIN TỨC: THÊM TIN TỨC MỚI (ADMIN)
if (isset($_POST['btnAddNews'])) {
    if (empty($_SESSION['logged_in']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
        header("Location: index.php?page=login");
        exit;
    }
    $title = trim($_POST['title']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);
    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = $_FILES['image_file']['name'];
        if (getimagesize($file_tmp) !== false) {
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = 'news_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = 'uploads/' . $new_file_name;
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_url = $upload_path;
            }
        }
    }
    if (empty($image_url)) {
        $image_url = '📰';
    }
    
    $stmt = $conn->prepare("INSERT INTO news (title, summary, content, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $summary, $content, $image_url);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Thêm tin tức mới thành công!";
    } else {
        $_SESSION['error'] = "Không thể thêm tin tức!";
    }
    $stmt->close();
    header("Location: index.php?page=admin_news");
    exit;
}

// 22. TIN TỨC: CẬP NHẬT TIN TỨC (ADMIN)
if (isset($_POST['btnUpdateNews'])) {
    if (empty($_SESSION['logged_in']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
        header("Location: index.php?page=login");
        exit;
    }
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);
    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = $_FILES['image_file']['name'];
        if (getimagesize($file_tmp) !== false) {
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = 'news_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = 'uploads/' . $new_file_name;
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_url = $upload_path;
            }
        }
    }
    
    // Xóa ảnh cũ nếu đổi sang ảnh khác
    if (!empty($image_url)) {
        $stmt_old = $conn->prepare("SELECT image_url FROM news WHERE id = ?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $old_news = $stmt_old->get_result()->fetch_assoc();
        $stmt_old->close();
        if ($old_news && $old_news['image_url'] !== $image_url) {
            if (strpos($old_news['image_url'], 'uploads/') === 0 && file_exists($old_news['image_url'])) {
                unlink($old_news['image_url']);
            }
        }
    }
    
    if (empty($image_url)) {
        // Giữ nguyên ảnh cũ nếu không đổi
        $stmt_old = $conn->prepare("SELECT image_url FROM news WHERE id = ?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $old_news = $stmt_old->get_result()->fetch_assoc();
        $stmt_old->close();
        $image_url = $old_news['image_url'] ?? '📰';
    }
    
    $stmt = $conn->prepare("UPDATE news SET title = ?, summary = ?, content = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $summary, $content, $image_url, $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Cập nhật tin tức thành công!";
    } else {
        $_SESSION['error'] = "Không thể cập nhật tin tức!";
    }
    $stmt->close();
    header("Location: index.php?page=admin_news");
    exit;
}

// 23. TIN TỨC: XÓA TIN TỨC (ADMIN)
if (isset($_POST['action']) && $_POST['action'] === 'delete_news') {
    if (empty($_SESSION['logged_in']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
        header("Location: index.php?page=login");
        exit;
    }
    $id = (int)$_POST['id'];
    $stmt_img = $conn->prepare("SELECT image_url FROM news WHERE id = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $news = $stmt_img->get_result()->fetch_assoc();
    $stmt_img->close();
    
    if ($news && !empty($news['image_url'])) {
        if (strpos($news['image_url'], 'uploads/') === 0 && file_exists($news['image_url'])) {
            unlink($news['image_url']);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Xóa bài viết thành công!";
    } else {
        $_SESSION['error'] = "Không thể xóa bài viết!";
    }
    $stmt->close();
    header("Location: index.php?page=admin_news");
    exit;
}
?>
