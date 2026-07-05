<?php
// pages/check_payment_status.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $st = $conn->prepare("SELECT trang_thai_tt FROM orders WHERE id = ?");
    $st->bind_param("i", $id);
    $st->execute();
    $order = $st->get_result()->fetch_assoc();
    $st->close();
    
    if ($order) {
        echo json_encode(['status' => $order['trang_thai_tt']]);
        exit;
    }
}

echo json_encode(['status' => 'error']);
exit;
?>
