<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
checkAdminOrDie();
$id = (int)$_GET['id'];
$st = $conn->prepare("SELECT * FROM orders WHERE id = ?"); 
$st->bind_param("i", $id); 
$st->execute(); 
$order = $st->get_result()->fetch_assoc();

if (!$order) {
    header("Location: index.php?page=admin_orders");
    exit;
}

$st_det = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?"); 
$st_det->bind_param("i", $id); 
$st_det->execute(); 
$items = $st_det->get_result()->fetch_all(MYSQLI_ASSOC);

// Xác định màu sắc badge Thanh Toán
$badge_style_tt = 'background-color: #F1F5F9; color: #475569;';
if ($order['trang_thai_tt'] === 'Chưa thanh toán') {
    $badge_style_tt = 'background-color: #FFFBEB; color: #D97706; border: 1px solid #FDE68A;';
} elseif ($order['trang_thai_tt'] === 'Đã thanh toán') {
    $badge_style_tt = 'background-color: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0;';
} else {
    $badge_style_tt = 'background-color: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5;';
}

// Xác định màu sắc badge Giao Nhận
$badge_style_gh = 'background-color: #F1F5F9; color: #475569;';
if ($order['trang_thai_gh'] === 'Chờ xác nhận') {
    $badge_style_gh = 'background-color: #FFFBEB; color: #D97706; border: 1px solid #FDE68A;';
} elseif ($order['trang_thai_gh'] === 'Đang chuẩn bị') {
    $badge_style_gh = 'background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0;';
} elseif ($order['trang_thai_gh'] === 'Đang giao') {
    $badge_style_gh = 'background-color: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;';
} elseif ($order['trang_thai_gh'] === 'Hoàn thành') {
    $badge_style_gh = 'background-color: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0;';
} elseif ($order['trang_thai_gh'] === 'Đã hủy') {
    $badge_style_gh = 'background-color: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5;';
}
?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width:740px; border-radius: 12px; background: var(--white);">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h4 class="text-dark fw-extrabold m-0"><i class="fa fa-receipt text-danger me-2"></i>Đơn Hàng #JB-<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></h4>
            <div class="d-flex gap-2">
                <span class="badge px-2.5 py-1.5 fs-7 fw-bold" style="border-radius: 6px; <?= $badge_style_tt ?>">TT: <?= htmlspecialchars($order['trang_thai_tt'] ?? 'Chưa thanh toán') ?></span>
                <span class="badge px-2.5 py-1.5 fs-7 fw-bold" style="border-radius: 6px; <?= $badge_style_gh ?>">Đơn: <?= htmlspecialchars($order['trang_thai_gh'] ?? 'Chờ xác nhận') ?></span>
            </div>
        </div>

        <div class="bg-light p-4 rounded border mb-4" style="font-size: 0.95rem;">
            <h5 class="fw-bold text-secondary mb-3" style="font-size: 1.05rem;"><i class="fa fa-info-circle me-2 text-danger"></i>Thông Tin Giao Hàng</h5>
            <div class="row g-2 text-dark">
                <div class="col-sm-6"><strong>Họ tên người nhận:</strong> <?= htmlspecialchars($order['ho_ten']) ?></div>
                <div class="col-sm-6"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['so_dien_thoai']) ?></div>
                <div class="col-12"><strong>Địa chỉ nhận gà:</strong> <?= htmlspecialchars($order['dia_chi']) ?></div>
                <div class="col-sm-6"><strong>Phương thức TT:</strong> <?= htmlspecialchars($order['phuong_thuc_tt']) ?></div>
                <div class="col-sm-6"><strong>Thời gian đặt:</strong> <?= date('d/m/Y H:i:s', strtotime($order['ngay_dat'] . ' UTC')) ?></div>
                <div class="col-sm-6">
                    <strong>Trạng thái thanh toán:</strong> 
                    <span class="badge px-2 py-1 fs-7 fw-bold" style="border-radius: 6px; <?= $badge_style_tt ?>"><?= htmlspecialchars($order['trang_thai_tt'] ?? 'Chưa thanh toán') ?></span>
                </div>
                <div class="col-sm-6">
                    <strong>Trạng thái đơn hàng:</strong> 
                    <span class="badge px-2 py-1 fs-7 fw-bold" style="border-radius: 6px; <?= $badge_style_gh ?>"><?= htmlspecialchars($order['trang_thai_gh'] ?? 'Chờ xác nhận') ?></span>
                </div>
                <div class="col-12"><strong>Ghi chú:</strong> <?= htmlspecialchars($order['ghi_chu'] ?: 'Không có') ?></div>
            </div>
        </div>

        <h5 class="fw-bold text-secondary mb-3" style="font-size: 1.05rem;"><i class="fa fa-utensils me-2 text-danger"></i>Món Ăn Đã Đặt</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle border text-start">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                        <th class="ps-3 py-2.5">Món Ăn</th>
                        <th class="text-center py-2.5" style="width: 120px;">Giá Bán</th>
                        <th class="text-center py-2.5" style="width: 80px;">SL</th>
                        <th class="text-end pe-3 py-2.5" style="width: 150px;">Thành Tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $it): ?>
                    <tr>
                        <td class="ps-3 fw-bold text-dark"><?= htmlspecialchars($it['ten_sp']) ?></td>
                        <td class="text-center"><?= number_format($it['gia_ban'], 0, ',', '.') ?>đ</td>
                        <td class="text-center fw-bold"><?= $it['so_luong'] ?></td>
                        <td class="text-end pe-3 text-danger fw-bold" style="font-family: 'Nunito', sans-serif;"><?= number_format($it['thanh_tien'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php
        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += $it['thanh_tien'];
        }
        $discount = (float)($order['so_tien_giam'] ?? 0);
        $shipping_fee = ($subtotal >= 150000) ? 0 : 20000;
        ?>
        <div class="mt-4 border-top pt-3" style="border-top-style: dashed !important; font-size: 0.95rem;">
            <div class="d-flex justify-content-between mb-2 text-muted">
                <span>Tạm tính tiền hàng:</span>
                <span class="text-dark fw-bold"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
            </div>
            <?php if ($discount > 0): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Mã giảm giá (<?= htmlspecialchars($order['ma_giam_gia']) ?>):</span>
                    <span class="fw-bold">-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between mb-2 text-muted">
                <span>Phí giao hàng:</span>
                <span class="text-dark fw-bold"><?= $shipping_fee == 0 ? 'Miễn phí' : number_format($shipping_fee, 0, ',', '.') . 'đ' ?></span>
            </div>
            <div class="d-flex justify-content-between fs-4 fw-extrabold text-danger border-top pt-3 mt-3" style="font-family: 'Nunito', sans-serif;">
                <span>Tổng cộng:</span>
                <span><?= number_format($order['tong_tien'], 0, ',', '.') ?>đ</span>
            </div>
        </div>

        <div class="no-print d-flex gap-2 mt-4 pt-3 border-top" style="border-top-color: #F1F5F9 !important;">
            <a href="index.php?page=admin_orders" class="btn btn-secondary flex-grow-1 py-2.5" style="border-radius: 8px; font-weight: 600;">Quay về Danh Sách</a>
            <button onclick="window.print();" class="btn btn-outline-dark flex-grow-1 py-2.5" style="border-radius: 8px; font-weight: 600;"><i class="fa fa-print me-2"></i>In Hóa Đơn</button>
        </div>
    </div>
</div>
