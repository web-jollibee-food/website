<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- TRANG HÓA ĐƠN ĐIỆN TỬ -->
<?php
$id = (int)$_GET['id'];
$st = $conn->prepare("SELECT * FROM orders WHERE id = ?"); 
$st->bind_param("i", $id); 
$st->execute(); 
$order = $st->get_result()->fetch_assoc();

$st_det = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?"); 
$st_det->bind_param("i", $id); 
$st_det->execute(); 
$items = $st_det->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<?php
$is_unpaid_bank = ($order['phuong_thuc_tt'] === 'Chuyển khoản' && ($order['trang_thai_tt'] ?? 'Chưa thanh toán') === 'Chưa thanh toán');

// Xác định màu sắc badge Thanh Toán
$badge_class_tt = 'bg-secondary';
$badge_style_tt = '';
if ($order['trang_thai_tt'] === 'Chưa thanh toán') {
    $badge_class_tt = 'bg-warning text-dark';
    $badge_style_tt = 'background-color: #FFFBEB !important; color: #D97706 !important; border: 1px solid #FDE68A !important;';
} elseif ($order['trang_thai_tt'] === 'Đã thanh toán') {
    $badge_class_tt = 'bg-success';
    $badge_style_tt = 'background-color: #F0FDF4 !important; color: #15803D !important; border: 1px solid #BBF7D0 !important;';
} else {
    $badge_class_tt = 'bg-danger';
    $badge_style_tt = 'background-color: #FEF2F2 !important; color: #DC2626 !important; border: 1px solid #FCA5A5 !important;';
}

// Xác định màu sắc badge Giao Nhận
$badge_class_gh = 'bg-secondary';
$badge_style_gh = '';
if ($order['trang_thai_gh'] === 'Chờ xác nhận') {
    $badge_class_gh = 'bg-warning text-dark';
    $badge_style_gh = 'background-color: #FFFBEB !important; color: #D97706 !important; border: 1px solid #FDE68A !important;';
} elseif ($order['trang_thai_gh'] === 'Đang chuẩn bị') {
    $badge_class_gh = 'bg-info text-dark';
    $badge_style_gh = 'background-color: #ECFDF5 !important; color: #059669 !important; border: 1px solid #A7F3D0 !important;';
} elseif ($order['trang_thai_gh'] === 'Đang giao') {
    $badge_class_gh = 'bg-primary';
    $badge_style_gh = 'background-color: #EFF6FF !important; color: #2563EB !important; border: 1px solid #BFDBFE !important;';
} elseif ($order['trang_thai_gh'] === 'Hoàn thành') {
    $badge_class_gh = 'bg-success';
    $badge_style_gh = 'background-color: #F0FDF4 !important; color: #15803D !important; border: 1px solid #BBF7D0 !important;';
} elseif ($order['trang_thai_gh'] === 'Đã hủy') {
    $badge_class_gh = 'bg-danger';
    $badge_style_gh = 'background-color: #FEF2F2 !important; color: #DC2626 !important; border: 1px solid #FCA5A5 !important;';
}
?>
<div class="container py-5">
    <div class="card border-0 p-4 shadow-sm mx-auto text-center" style="max-width:640px; border-radius: 12px; background: var(--white); border-top: 5px solid <?= $is_unpaid_bank ? '#F59E0B' : '#10B981' ?> !important;">
        
        <?php if ($is_unpaid_bank): ?>
            <div class="text-warning mb-2" style="font-size:4.5rem; line-height: 1;">⚠️</div>
            <h3 class="text-warning fw-extrabold mb-3" style="font-family: 'Fredoka One', cursive;">CHỜ THANH TOÁN!</h3>
            <p class="text-muted small mb-4">Đơn hàng của bạn đã được ghi nhận. Vui lòng hoàn tất thanh toán chuyển khoản bên dưới:</p>
            
            <div class="alert alert-warning text-start mb-4 border-0 p-3 shadow-sm d-flex flex-column gap-2" style="background-color: #FFFBEB; color: #D97706; border-radius: 8px; font-weight: 600;">
                <div class="d-flex align-items-center gap-2 fs-6">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>Đơn hàng chưa được thanh toán!</span>
                </div>
                <p class="mb-0 small text-secondary fw-normal">Hệ thống đang chờ nhận tiền chuyển khoản của đơn hàng này. Sau khi bạn chuyển khoản thành công, hệ thống tự động cập nhật.</p>
                <div class="mt-2 text-center">
                    <a href="index.php?page=qrcode&id=<?= $id ?>" class="btn btn-warning w-100 fw-bold" style="border-radius: 8px; color: #D97706; background-color: #FDE68A; border: 1px solid #FCD34D;">
                        <i class="fa fa-qrcode me-2"></i>TIẾN HÀNH THANH TOÁN (QUÉT VIETQR)
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="text-success mb-2" style="font-size:4.5rem; line-height: 1;">✓</div>
            <h3 class="text-success fw-extrabold mb-3" style="font-family: 'Fredoka One', cursive;">ĐẶT HÀNG THÀNH CÔNG!</h3>
            <p class="text-muted small mb-4">Cảm ơn bạn đã lựa chọn Jollibee. Dưới đây là thông tin hóa đơn điện tử của bạn:</p>
        <?php endif; ?>
        
        <div class="bg-light p-4 rounded text-start border mb-4" style="font-size: 0.95rem;">
            <h5 class="border-bottom pb-2 mb-3 d-flex justify-content-between text-secondary fw-bold" style="font-size: 1.1rem;">
                <span>HÓA ĐƠN ĐƠN HÀNG</span>
                <span class="text-danger">#JB-<?= str_pad($id,6,'0',STR_PAD_LEFT) ?></span>
            </h5>
            
            <div class="row g-2 mb-3">
                <div class="col-sm-6">
                    <p class="mb-1 text-muted">Khách hàng:</p>
                    <p class="mb-2 fw-bold text-dark"><?= htmlspecialchars($order['ho_ten']) ?></p>
                </div>
                <div class="col-sm-6">
                    <p class="mb-1 text-muted">Điện thoại:</p>
                    <p class="mb-2 fw-bold text-dark"><?= htmlspecialchars($order['so_dien_thoai']) ?></p>
                </div>
                <div class="col-12">
                    <p class="mb-1 text-muted">Địa chỉ nhận hàng:</p>
                    <p class="mb-2 fw-bold text-dark"><?= htmlspecialchars($order['dia_chi']) ?></p>
                </div>
                <div class="col-sm-6">
                    <p class="mb-1 text-muted">Phương thức thanh toán:</p>
                    <p class="mb-2 fw-bold text-dark"><?= htmlspecialchars($order['phuong_thuc_tt']) ?></p>
                </div>
                <div class="col-sm-6">
                    <p class="mb-1 text-muted">Trạng thái thanh toán:</p>
                    <p class="mb-2"><span class="badge <?= $badge_class_tt ?> px-2 py-1 fs-7" style="border-radius: 6px; <?= $badge_style_tt ?>"><?= htmlspecialchars($order['trang_thai_tt'] ?? 'Chưa thanh toán') ?></span></p>
                </div>
                <div class="col-sm-6">
                    <p class="mb-1 text-muted">Trạng thái đơn hàng:</p>
                    <p class="mb-0"><span class="badge <?= $badge_class_gh ?> px-2 py-1 fs-7" style="border-radius: 6px; <?= $badge_style_gh ?>"><?= htmlspecialchars($order['trang_thai_gh'] ?? 'Chờ xác nhận') ?></span></p>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-sm text-start align-middle mb-0 mt-3 border-top" style="border-top-style: dashed !important;">
                    <thead>
                        <tr class="text-muted"><th class="py-2">Món ăn</th><th class="text-center py-2">SL</th><th class="text-end py-2">Thành tiền</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $it): ?>
                        <tr>
                            <td class="py-2 fw-bold text-dark"><?= htmlspecialchars($it['ten_sp']) ?></td>
                            <td class="text-center py-2 fw-bold"><?= $it['so_luong'] ?></td>
                            <td class="text-end py-2 fw-bold text-danger"><?= number_format($it['thanh_tien'],0,',','.') ?>đ</td>
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
            <div class="mt-3 border-top pt-2" style="border-top-style: dashed !important; font-size: 0.95rem;">
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
                <div class="d-flex justify-content-between fs-4 fw-extrabold text-danger border-top pt-2 mt-2" style="font-family: 'Nunito', sans-serif;">
                    <span>Tổng thanh toán:</span>
                    <span><?= number_format($order['tong_tien'], 0, ',', '.') ?>đ</span>
                </div>
            </div>
        </div>
        
        <div class="no-print d-flex gap-2">
            <a href="index.php?page=home" class="btn btn-jollibee flex-grow-1 py-2.5" style="border-radius: 8px;">Quay về Trang Chủ</a>
            <button onclick="window.print();" class="btn btn-outline-dark flex-grow-1 py-2.5" style="border-radius: 8px; font-weight: 600;"><i class="fa fa-print me-2"></i>In hóa đơn điện tử</button>
        </div>
    </div>
</div>
