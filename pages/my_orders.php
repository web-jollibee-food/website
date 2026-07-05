<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- LỊCH SỬ ĐƠN HÀNG CỦA KHÁCH HÀNG -->
<?php
if (empty($_SESSION['logged_in'])) {
    header("Location: index.php?page=login");
    exit;
}
$user_id = $_SESSION['user_id'];
?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white);">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-bottom-color: #F1F5F9 !important;">
            <h4 class="text-dark fw-extrabold m-0"><i class="fa fa-history text-danger me-2"></i>Lịch Sử Đơn Hàng Của Tôi</h4>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
                <thead>
                    <tr class="table-light text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                        <th class="ps-3" style="width: 130px;">Mã Đơn Hàng</th>
                        <th>Thời Gian Đặt</th>
                        <th>Tổng Hóa Đơn</th>
                        <th>Phương Thức TT</th>
                        <th>Thanh Toán</th>
                        <th>Giao Nhận</th>
                        <th class="text-center" style="width: 250px;">Chi Tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();
                    
                    if (count($result) > 0): 
                        foreach($result as $ord):
                            // 1. Logic Badge Thanh Toán
                            $badge_class_tt = 'bg-secondary';
                            $badge_style_tt = '';
                            if ($ord['trang_thai_tt'] === 'Chưa thanh toán') {
                                $badge_class_tt = 'bg-warning text-dark';
                                $badge_style_tt = 'background-color: #FFFBEB !important; color: #D97706 !important; border: 1px solid #FDE68A !important;';
                            } elseif ($ord['trang_thai_tt'] === 'Đã thanh toán') {
                                $badge_class_tt = 'bg-success';
                                $badge_style_tt = 'background-color: #F0FDF4 !important; color: #15803D !important; border: 1px solid #BBF7D0 !important;';
                            } else {
                                $badge_class_tt = 'bg-danger';
                                $badge_style_tt = 'background-color: #FEF2F2 !important; color: #DC2626 !important; border: 1px solid #FCA5A5 !important;';
                            }

                            // 2. Logic Badge Giao Nhận
                            $badge_class_gh = 'bg-secondary';
                            $badge_style_gh = '';
                            if ($ord['trang_thai_gh'] === 'Chờ xác nhận') {
                                $badge_class_gh = 'bg-warning text-dark';
                                $badge_style_gh = 'background-color: #FFFBEB !important; color: #D97706 !important; border: 1px solid #FDE68A !important;';
                            } elseif ($ord['trang_thai_gh'] === 'Đang chuẩn bị') {
                                $badge_class_gh = 'bg-info text-dark';
                                $badge_style_gh = 'background-color: #ECFDF5 !important; color: #059669 !important; border: 1px solid #A7F3D0 !important;';
                            } elseif ($ord['trang_thai_gh'] === 'Đang giao') {
                                $badge_class_gh = 'bg-primary';
                                $badge_style_gh = 'background-color: #EFF6FF !important; color: #2563EB !important; border: 1px solid #BFDBFE !important;';
                            } elseif ($ord['trang_thai_gh'] === 'Hoàn thành') {
                                $badge_class_gh = 'bg-success';
                                $badge_style_gh = 'background-color: #F0FDF4 !important; color: #15803D !important; border: 1px solid #BBF7D0 !important;';
                            } elseif ($ord['trang_thai_gh'] === 'Đã hủy') {
                                $badge_class_gh = 'bg-danger';
                                $badge_style_gh = 'background-color: #FEF2F2 !important; color: #DC2626 !important; border: 1px solid #FCA5A5 !important;';
                            }
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold text-danger">#JB-<?= str_pad($ord['id'], 6, '0', STR_PAD_LEFT) ?></td>
                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($ord['ngay_dat'] . ' UTC')) ?></td>
                        <td class="fw-bold text-danger" style="font-family: 'Nunito', sans-serif;"><?= number_format($ord['tong_tien'], 0, ',', '.') ?>đ</td>
                        <td><span class="small fw-semibold text-secondary"><?= htmlspecialchars($ord['phuong_thuc_tt']) ?></span></td>
                        <td>
                            <span class="badge <?= $badge_class_tt ?> px-2 py-1 fs-7" style="border-radius: 6px; <?= $badge_style_tt ?>"><?= htmlspecialchars($ord['trang_thai_tt'] ?? 'Chưa thanh toán') ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $badge_class_gh ?> px-2 py-1 fs-7" style="border-radius: 6px; <?= $badge_style_gh ?>"><?= htmlspecialchars($ord['trang_thai_gh'] ?? 'Chờ xác nhận') ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="index.php?page=success&id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600; padding: 4px 10px;"><i class="fa fa-file-invoice me-1"></i> Xem Hóa Đơn</a>
                                <?php if ($ord['phuong_thuc_tt'] === 'Chuyển khoản' && ($ord['trang_thai_tt'] ?? 'Chưa thanh toán') === 'Chưa thanh toán' && $ord['trang_thai_gh'] !== 'Đã hủy'): ?>
                                    <a href="index.php?page=qrcode&id=<?= $ord['id'] ?>" class="btn btn-sm btn-success text-white" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600; padding: 4px 10px;"><i class="fa fa-qrcode me-1"></i> Thanh toán ngay</a>
                                <?php endif; ?>
                                <?php if (($ord['trang_thai_gh'] ?? 'Chờ xác nhận') === 'Chờ xác nhận'): ?>
                                    <form method="POST" action="index.php?page=my_orders" class="m-0 confirm-form" data-confirm-text="Bạn có chắc chắn muốn hủy đơn hàng này?">
                                        <input type="hidden" name="action" value="cancel_order">
                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger text-white" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600; padding: 4px 10px;"><i class="fa fa-times me-1"></i> Hủy đơn</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                        <tr><td colspan="7" class="text-center p-5 text-muted small">Bạn chưa thực hiện đơn đặt hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
