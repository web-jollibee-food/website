<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- QUẢN LÝ MÓN ĂN (ADMIN) -->
<?php checkAdminOrDie(); ?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white);">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-bottom-color: #F1F5F9 !important;">
            <h4 class="text-dark fw-extrabold m-0"><i class="fa fa-hamburger text-danger me-2"></i>Quản Lý Danh Sách Món Ăn</h4>
            <a href="index.php?page=admin_product_add" class="btn btn-jollibee btn-sm px-3 py-2" style="border-radius: 6px; font-size: 0.85rem;"><i class="fa fa-plus me-2"></i>Thêm Món Ăn</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
                <thead>
                    <tr class="table-light text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                        <th class="ps-3" style="width: 80px;">ID</th>
                        <th style="width: 100px; text-align: center;">Hình Ảnh</th>
                        <th>Tên Món Ăn</th>
                        <th>Danh Mục</th>
                        <th>Giá Bán</th>
                        <th>Mô Tả</th>
                        <th class="text-center" style="width: 200px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
                    $result = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
                    if (count($result) > 0): 
                        foreach($result as $row):
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#<?= $row['id'] ?></td>
                        <td style="text-align: center;">
                            <?php if (strpos($row['image_url'] ?? '', 'uploads/') === 0 || strpos($row['image_url'] ?? '', 'http://') === 0 || strpos($row['image_url'] ?? '', 'https://') === 0): ?>
                                <img src="<?= htmlspecialchars($row['image_url']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #E2E8F0; cursor: zoom-in;" class="zoomable-img">
                            <?php else: ?>
                                <span style="font-size: 2.2rem;"><?= htmlspecialchars($row['image_url'] ?? '🍔') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge bg-secondary px-2.5 py-1.5 fs-7" style="border-radius: 6px; background-color: #F1F5F9 !important; color: #475569 !important; border: 1px solid #E2E8F0 !important;"><?= htmlspecialchars($row['category_name'] ?? 'Không có') ?></span></td>
                        <td style="font-family: 'Nunito', sans-serif;">
                            <?php if ($row['price_discount'] > 0 && $row['price_discount'] < $row['price']): ?>
                                <div class="text-danger fw-bold" style="font-size: 0.95rem;"><?= number_format($row['price_discount'], 0, ',', '.') ?>đ</div>
                                <div class="text-muted text-decoration-line-through" style="font-size: 0.8rem;"><?= number_format($row['price'], 0, ',', '.') ?>đ</div>
                                <span class="badge bg-danger text-white py-0.5 px-1.5 mt-1" style="border-radius: 4px; font-size: 0.7rem; font-weight: bold;">-<?= round((($row['price'] - $row['price_discount']) / $row['price']) * 100) ?>%</span>
                            <?php else: ?>
                                <div class="text-danger fw-bold" style="font-size: 0.95rem;"><?= number_format($row['price'], 0, ',', '.') ?>đ</div>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['description'] ?? '') ?>"><?= htmlspecialchars($row['description'] ?? '') ?></td>
                        <td class="text-center">
                            <a href="index.php?page=admin_product_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning text-dark me-1" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><i class="fa fa-edit me-1"></i> Sửa</a>
                            <a href="index.php?page=admin_product_delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger confirm-link" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600;" data-confirm-text="Bạn có chắc chắn muốn xóa món ăn này?"><i class="fa fa-trash me-1"></i> Xóa</a>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                        <tr><td colspan="7" class="text-center p-4 text-muted small">Chưa có sản phẩm món ăn nào trong hệ thống.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
