<?php
// pages/sepay_webhook.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

header('Content-Type: application/json');

// Đọc dữ liệu JSON từ SePay gửi sang
$raw_body = file_get_contents('php://input');
$data = json_decode($raw_body, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
    exit;
}

// Kiểm tra mã API Key bảo mật (nếu có cấu hình trong .env)
$sepay_api_key = get_env_val('SEPAY_API_KEY', '');
if ($sepay_api_key !== '') {
    $headers = apache_request_headers();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (strpos($auth_header, 'Apikey ' . $sepay_api_key) === false) {
        echo json_encode(['success' => false, 'message' => 'Lỗi xác thực API Key!']);
        exit;
    }
}

$transactionContent = $data['transactionContent'] ?? '';
$transferAmount = (float)($data['transferAmount'] ?? 0);

// Tìm mã đơn hàng dạng JBxxxxx trong nội dung chuyển khoản
if (preg_match('/JB(\d+)/i', $transactionContent, $matches)) {
    $order_id = (int)$matches[1];
    
    // Kiểm tra đơn hàng trong CSDL
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($order) {
        // Kiểm tra xem số tiền chuyển khoản có khớp hoặc lớn hơn tiền đơn hàng không
        if ($transferAmount >= (float)$order['tong_tien']) {
            // Cập nhật trạng thái thành Đã thanh toán
            $ghi_chu_moi = trim(($order['ghi_chu'] ? $order['ghi_chu'] . "\n" : "") . "Thanh toán thành công qua SePay Bank Transfer. Ref: " . ($data['referenceNumber'] ?? ''));
            
            $stmt_up = $conn->prepare("UPDATE orders SET trang_thai_tt = 'Đã thanh toán', ghi_chu = ? WHERE id = ?");
            $stmt_up->bind_param("si", $ghi_chu_moi, $order_id);
            $stmt_up->execute();
            $stmt_up->close();
            
            echo json_encode(['success' => true, 'message' => 'Đã xác nhận thanh toán đơn hàng thành công!']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Số tiền chuyển khoản thiếu so với đơn hàng!']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng #' . $order_id]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Không tìm thấy cú pháp mã đơn hàng JB trong nội dung chuyển khoản!']);
exit;
?>
