<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- QUẢN LÝ ĐƠN HÀNG (ADMIN) -->
<?php checkAdminOrDie(); ?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white);">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-bottom-color: #F1F5F9 !important;">
            <h4 class="text-dark fw-extrabold m-0"><i class="fa fa-shopping-bag text-danger me-2"></i>Quản Lý Đơn Đặt Hàng</h4>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
                <thead>
                    <tr class="table-light text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                        <th class="ps-3" style="width: 110px;">Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Điện Thoại</th>
                        <th>Địa Chỉ Giao</th>
                        <th>Tổng Tiền</th>
                        <th>Phương Thức TT</th>
                        <th>Ngày Đặt</th>
                        <th>Thanh Toán</th>
                        <th>Giao Nhận</th>
                        <th class="text-center" style="width: 380px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $orders = $conn->query("SELECT * FROM orders ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
                    if (count($orders) > 0): 
                        foreach($orders as $ord):
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
                        <td class="fw-bold text-dark"><?= htmlspecialchars($ord['ho_ten']) ?></td>
                        <td><?= htmlspecialchars($ord['so_dien_thoai']) ?></td>
                        <td class="text-muted small" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($ord['dia_chi']) ?>"><?= htmlspecialchars($ord['dia_chi']) ?></td>
                        <td class="fw-bold text-danger" style="font-family: 'Nunito', sans-serif;"><?= number_format($ord['tong_tien'], 0, ',', '.') ?>đ</td>
                        <td><span class="small fw-semibold text-secondary"><?= htmlspecialchars($ord['phuong_thuc_tt']) ?></span></td>
                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($ord['ngay_dat'] . ' UTC')) ?></td>
                        <td>
                            <span class="badge <?= $badge_class_tt ?> px-2 py-1 fs-7" style="border-radius: 6px; <?= $badge_style_tt ?>"><?= htmlspecialchars($ord['trang_thai_tt'] ?? 'Chưa thanh toán') ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $badge_class_gh ?> px-2 py-1 fs-7" style="border-radius: 6px; <?= $badge_style_gh ?>"><?= htmlspecialchars($ord['trang_thai_gh'] ?? 'Chờ xác nhận') ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="index.php?page=admin_order_detail&id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-info text-dark" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600; padding: 4px 10px;"><i class="fa fa-eye me-1"></i> Xem</a>
                                
                                <form method="POST" class="m-0">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                    <select name="trang_thai_gh" class="form-select form-select-sm" style="width: 125px; font-size: 0.8rem; border-radius: 6px;" onchange="this.form.submit()">
                                        <option value="">Giao nhận...</option>
                                        <option value="Chờ xác nhận" <?= ($ord['trang_thai_gh'] ?? 'Chờ xác nhận') === 'Chờ xác nhận' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                        <option value="Đang chuẩn bị" <?= ($ord['trang_thai_gh'] ?? '') === 'Đang chuẩn bị' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                        <option value="Đang giao" <?= ($ord['trang_thai_gh'] ?? '') === 'Đang giao' ? 'selected' : '' ?>>Đang giao</option>
                                        <option value="Hoàn thành" <?= ($ord['trang_thai_gh'] ?? '') === 'Hoàn thành' ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="Đã hủy" <?= ($ord['trang_thai_gh'] ?? '') === 'Đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                    <input type="hidden" name="btnUpdateOrderStatus" value="1">
                                </form>

                                <form method="POST" class="m-0">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                    <select name="trang_thai_tt" class="form-select form-select-sm" style="width: 115px; font-size: 0.8rem; border-radius: 6px;" onchange="this.form.submit()">
                                        <option value="">Thanh toán...</option>
                                        <option value="Chưa thanh toán" <?= ($ord['trang_thai_tt'] ?? 'Chưa thanh toán') === 'Chưa thanh toán' ? 'selected' : '' ?>>Chưa thanh toán</option>
                                        <option value="Đã thanh toán" <?= ($ord['trang_thai_tt'] ?? '') === 'Đã thanh toán' ? 'selected' : '' ?>>Đã thanh toán</option>
                                    </select>
                                    <input type="hidden" name="btnUpdateOrderStatus" value="1">
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                        <tr><td colspan="9" class="text-center p-4 text-muted small">Chưa có đơn đặt hàng nào trong hệ thống.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
