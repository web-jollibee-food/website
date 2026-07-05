<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

// Kiểm tra quyền Admin
checkAdminOrDie();

$default_date = date('Y-m-d', strtotime('+1 month'));
?>
<div class="container py-5" style="max-width: 600px;">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white); border-top: 5px solid var(--red) !important;">
        <h3 class="fw-bold mb-4" style="font-family: 'Fredoka One', cursive; color: var(--red);"><i class="fa fa-tag me-2"></i>Thêm mã giảm giá mới</h3>
        
        <?php if (isset($error_promo)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error_promo ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=admin_promotions">
            <input type="hidden" name="action" value="add_promotion">
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Mã giảm giá (Code) <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" required style="border-radius: 8px; text-transform: uppercase;" placeholder="Ví dụ: JOLLIBEE50">
                <div class="form-text text-muted">Nhập viết liền, không dấu. Trình duyệt tự động chuyển sang chữ IN HOA.</div>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Loại giảm giá <span class="text-danger">*</span></label>
                <select name="discount_type" class="form-select" required style="border-radius: 8px;">
                    <option value="percentage">Phần trăm (%)</option>
                    <option value="flat">Số tiền cố định (VND)</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Mức giảm giá <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="discount_value" class="form-control" required style="border-radius: 8px;" placeholder="Ví dụ: 10 cho 10% hoặc 20000 cho 20.000đ" min="0">
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Số lượng mã (Giới hạn sử dụng) <span class="text-danger">*</span></label>
                <input type="number" name="max_uses" class="form-control" value="100" required style="border-radius: 8px;" min="1" placeholder="Ví dụ: 100">
                <div class="form-text text-muted">Số lượt sử dụng tối đa của mã giảm giá này.</div>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Ngày hết hạn <span class="text-danger">*</span></label>
                <input type="date" name="expiry_date" class="form-control" value="<?= $default_date ?>" required style="border-radius: 8px;">
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold">Trạng thái</label>
                <select name="status" class="form-select" style="border-radius: 8px;">
                    <option value="1">Kích hoạt</option>
                    <option value="0">Tạm khóa</option>
                </select>
            </div>
            
            <div class="d-flex gap-2">
                <a href="index.php?page=admin_promotions" class="btn btn-outline-secondary w-50 py-2.5" style="border-radius: 8px;">Hủy bỏ</a>
                <button type="submit" name="btnAddPromotion" class="btn btn-jollibee w-50 py-2.5" style="border-radius: 8px;">Tạo mã giảm giá</button>
            </div>
        </form>
    </div>
</div>
