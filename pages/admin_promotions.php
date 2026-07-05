<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

// Kiểm tra quyền Admin
checkAdminOrDie();

$st = $conn->query("SELECT * FROM promotions ORDER BY id DESC");
$promotions = $st->fetch_all(MYSQLI_ASSOC);
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0" style="font-family: 'Fredoka One', cursive; color: var(--red);"><i class="fa fa-tag me-2"></i>Quản lý khuyến mãi</h3>
        <a href="index.php?page=admin_promotion_add" class="btn btn-jollibee px-4 py-2.5" style="border-radius: 8px;">
            <i class="fa fa-plus me-2"></i>Thêm mã giảm giá
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white);">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Mã giảm giá</th>
                        <th>Mức giảm</th>
                        <th>Loại giảm</th>
                        <th>Lượt dùng (Đã dùng / Tối đa)</th>
                        <th>Ngày hết hạn</th>
                        <th>Trạng thái</th>
                        <th class="text-end" style="width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($promotions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có chương trình khuyến mãi nào được tạo.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($promotions as $promo): ?>
                            <tr>
                                <td class="fw-bold"><?= $promo['id'] ?></td>
                                <td>
                                    <span class="badge bg-danger px-3 py-2 fs-6" style="border-radius: 6px; letter-spacing: 0.5px;">
                                        <?= htmlspecialchars($promo['code']) ?>
                                    </span>
                                </td>
                                <td class="fw-bold">
                                    <?= $promo['discount_type'] === 'percentage' ? (int)$promo['discount_value'] . '%' : number_format($promo['discount_value'], 0, ',', '.') . 'đ' ?>
                                </td>
                                <td>
                                    <?= $promo['discount_type'] === 'percentage' ? 'Phần trăm (%)' : 'Số tiền cố định (đ)' ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold" style="border-radius: 6px; font-size: 0.85rem;">
                                        <strong><?= (int)$promo['used_count'] ?></strong> / <?= (int)$promo['max_uses'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $expiry = strtotime($promo['expiry_date']);
                                    $is_expired = $expiry < time();
                                    if ($is_expired): 
                                    ?>
                                        <span class="text-danger small fw-bold"><i class="fa fa-clock me-1"></i>Hết hạn (<?= htmlspecialchars($promo['expiry_date']) ?>)</span>
                                    <?php else: ?>
                                        <span class="text-success small fw-bold"><i class="fa fa-calendar me-1"></i>Còn hạn (<?= htmlspecialchars($promo['expiry_date']) ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $is_fully_used = $promo['used_count'] >= $promo['max_uses'];
                                    if ($promo['status'] == 1 && !$is_expired && !$is_fully_used): 
                                    ?>
                                        <span class="badge bg-success" style="border-radius: 4px;">Đang hoạt động</span>
                                    <?php elseif ($is_fully_used): ?>
                                        <span class="badge bg-warning text-dark" style="border-radius: 4px;">Hết lượt dùng</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary" style="border-radius: 4px;">Tạm ngưng / Hết hạn</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="index.php?page=admin_promotion_edit&id=<?= $promo['id'] ?>" class="btn btn-outline-secondary btn-sm" style="border-radius: 6px;"><i class="fa fa-edit"></i></a>
                                        <form method="POST" action="index.php?page=admin_promotions" class="m-0 confirm-form" data-confirm-text="Bạn có chắc chắn muốn xóa mã giảm giá này không?">
                                            <input type="hidden" name="action" value="delete_promotion">
                                            <input type="hidden" name="promotion_id" value="<?= $promo['id'] ?>">
                                            <button type="submit" name="btnDeletePromotion" class="btn btn-outline-danger btn-sm" style="border-radius: 6px;"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
