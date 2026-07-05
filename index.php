<?php
// index.php
define('SECURE_ACCESS', true);
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

$page = $_GET['page'] ?? 'home';
$error = ''; 
$success = '';

// Check if registered parameter is passed to show success message on login page
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
}

if (isset($_GET['pw_reset']) && $_GET['pw_reset'] == 1) {
    $success = 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.';
}

if (isset($_GET['redirect']) && $_GET['redirect'] === 'checkout') {
    $error = 'Bạn cần đăng nhập tài khoản trước khi thực hiện đặt hàng!';
}

// Helper function to check admin status
function checkAdminOrDie() {
    if (empty($_SESSION['logged_in']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
        die("<div style='padding:50px; text-align:center; font-family:sans-serif;'><h2>Mục này chỉ dành cho Quản trị viên!</h2><a href='index.php?page=login'>Đăng nhập tài khoản Admin</a></div>");
    }
}

// ──────────────────────────────────────────────────────────────────────────
// PROCESS LOGIC (POST / GET action controllers)
// ──────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'process/actions.php';
}

// GET action handlers (Logout, admin delete category)
if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=home");
    exit;
}

if ($page === 'admin_delete') {
    checkAdminOrDie();
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: index.php?page=admin_dashboard");
    exit;
}

if ($page === 'admin_product_delete') {
    checkAdminOrDie();
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt_img = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
        $stmt_img->bind_param("i", $id);
        $stmt_img->execute();
        $prod = $stmt_img->get_result()->fetch_assoc();
        $stmt_img->close();

        if ($prod && !empty($prod['image_url'])) {
            if (strpos($prod['image_url'], 'uploads/') === 0 && file_exists($prod['image_url'])) {
                unlink($prod['image_url']);
            }
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: index.php?page=admin_products");
    exit;
}

if ($page === 'checkout' || $page === 'qrcode' || $page === 'success' || $page === 'my_orders') {
    if (empty($_SESSION['logged_in'])) {
        header("Location: index.php?page=login&redirect=" . urlencode($page));
        exit;
    }
}

// ──────────────────────────────────────────────────────────────────────────
// VIEW ROUTING
// ──────────────────────────────────────────────────────────────────────────
$allowed_pages = [
    'home', 'checkout', 'qrcode', 'success', 'login', 'register', 'my_orders',
    'forgot_password', 'reset_password',
    'admin_dashboard', 'category_add', 'category_edit',
    'admin_products', 'admin_product_add', 'admin_product_edit',
    'admin_orders', 'admin_order_detail',
    'profile', 'admin_promotions', 'admin_promotion_add', 'admin_promotion_edit',
    'check_payment_status', 'sepay_webhook',
    'news', 'news_detail', 'admin_news', 'admin_news_add', 'admin_news_edit'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Include views
require_once 'includes/header.php';
require_once "pages/{$page}.php";
require_once 'includes/footer.php';
?>