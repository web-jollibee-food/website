<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!'); 
checkAdminOrDie(); 
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    header("Location: index.php?page=admin_products");
    exit;
}
$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
?>
<div class="container py-5">
    <div class="card shadow p-4 mx-auto" style="max-width:600px;">
        <h4 class="text-dark fw-bold border-bottom pb-2 mb-3"><i class="fa fa-edit me-2"></i>Cập Nhật Món Ăn #<?= $id ?></h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= $id ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Tên Món Ăn *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Danh Mục *</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-bold">Giá Gốc (VNĐ) *</label>
                    <input type="number" id="price" name="price" min="0" step="1" class="form-control" value="<?= (int)$product['price'] ?>" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-bold">Giá Khuyến Mãi (VNĐ)</label>
                    <input type="number" id="price_discount" name="price_discount" min="0" step="1" class="form-control" value="<?= $product['price_discount'] > 0 ? (int)$product['price_discount'] : '' ?>" placeholder="Không giảm">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-bold">Phần Trăm Giảm (%)</label>
                    <input type="number" id="discount_percent" min="0" max="100" class="form-control" placeholder="Tự động tính">
                </div>
                <div class="col-sm-6 d-flex align-items-end">
                    <div id="discount-summary" class="text-success small fw-bold pb-2" style="display:none;">
                        <i class="fa fa-check-circle me-1"></i> Giảm <span id="discount-diff-val">0đ</span>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Thay Đổi Ảnh Món Ăn</label>
                <input type="file" name="image_file" class="form-control" accept="image/*">
                <div class="form-text small text-muted">Để trống nếu không muốn thay đổi ảnh hiện tại. Hỗ trợ: JPG, PNG, WebP, GIF.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hoặc Biểu Tượng (Emoji) / URL Ảnh</label>
                <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($product['image_url'] ?? '') ?>">
                <div class="form-text small text-muted">Có thể điền biểu tượng Emoji hoặc dán link ảnh trực tiếp nếu không tải file lên.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mô Tả Chi Tiết</label>
                <textarea name="description" class="form-control" style="min-height: 100px;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-between pt-2">
                <a href="index.php?page=admin_products" class="btn btn-secondary">Quay lại</a>
                <button type="submit" name="btnUpdateProduct" class="btn btn-warning fw-bold px-4">Cập Nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price');
    const discountInput = document.getElementById('price_discount');
    const percentInput = document.getElementById('discount_percent');
    const summaryBlock = document.getElementById('discount-summary');
    const diffVal = document.getElementById('discount-diff-val');

    function calculateFromDiscountPrice() {
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;

        if (price > 0 && discount > 0 && discount < price) {
            const diff = price - discount;
            const pct = Math.round((diff / price) * 100);
            percentInput.value = pct;
            diffVal.textContent = diff.toLocaleString('vi-VN') + 'đ';
            summaryBlock.style.display = 'block';
        } else {
            percentInput.value = '';
            summaryBlock.style.display = 'none';
        }
    }

    function calculateFromPercent() {
        const price = parseFloat(priceInput.value) || 0;
        const pct = parseFloat(percentInput.value) || 0;

        if (price > 0 && pct > 0 && pct <= 100) {
            const diff = Math.round(price * (pct / 100));
            const discount = price - diff;
            discountInput.value = discount;
            diffVal.textContent = diff.toLocaleString('vi-VN') + 'đ';
            summaryBlock.style.display = 'block';
        } else {
            discountInput.value = '';
            summaryBlock.style.display = 'none';
        }
    }

    priceInput.addEventListener('input', function() {
        if (discountInput.value) {
            calculateFromDiscountPrice();
        } else if (percentInput.value) {
            calculateFromPercent();
        }
    });

    discountInput.addEventListener('input', calculateFromDiscountPrice);
    percentInput.addEventListener('input', calculateFromPercent);

    // Tính toán ban đầu dựa trên dữ liệu hiện có
    if (discountInput.value) {
        calculateFromDiscountPrice();
    }
});
</script>
