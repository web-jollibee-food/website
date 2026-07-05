<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- TRANG CHỦ DASHBOARD ADMIN: QUẢN LÝ DANH MỤC -->
<?php checkAdminOrDie(); ?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white);">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-bottom-color: #F1F5F9 !important;">
            <h4 class="text-dark fw-extrabold m-0"><i class="fa fa-list text-danger me-2"></i>Quản Lý Danh Mục Món Ăn</h4>
            <a href="index.php?page=category_add" class="btn btn-jollibee btn-sm px-3 py-2" style="border-radius: 6px; font-size: 0.85rem;"><i class="fa fa-plus me-2"></i>Thêm Danh Mục</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
                <thead>
                    <tr class="table-light text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                        <th class="ps-3" style="width: 100px;">STT</th>
                        <th>Tên Danh Mục Món Ăn</th>
                        <th class="text-center" style="width: 250px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $result = $conn->query("SELECT * FROM categories ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
                    if (count($result) > 0): 
                        $stt = 1; 
                        foreach($result as $row):
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold text-secondary"><?= $stt++ ?></td>
                        <td><span class="badge bg-danger px-3 py-2 fs-6 fw-bold" style="border-radius: 6px; background-color: #FFF1F2 !important; color: var(--red) !important; border: 1px solid rgba(215,27,42,0.1) !important;"><?= htmlspecialchars($row['name']) ?></span></td>
                        <td class="text-center">
                            <a href="index.php?page=category_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning text-dark me-1" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><i class="fa fa-edit me-1"></i> Sửa</a>
                            <a href="index.php?page=admin_delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger confirm-link" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600;" data-confirm-text="Bạn có chắc chắn muốn xóa danh mục này?"><i class="fa fa-trash me-1"></i> Xóa</a>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                        <tr><td colspan="3" class="text-center p-4 text-muted small">Chưa có dữ liệu danh mục nào được tạo.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
