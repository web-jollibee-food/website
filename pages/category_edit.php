<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!'); 
checkAdminOrDie(); 
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?"); 
$stmt->bind_param("i", $id); 
$stmt->execute(); 
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { 
    header("Location: index.php?page=admin_dashboard"); 
    exit; 
}
?>
<div class="container py-5">
    <div class="card shadow p-4 mx-auto" style="max-width:500px;">
        <h4 class="text-dark fw-bold border-bottom pb-2 mb-3">Cập Nhật Danh Mục ID: <?= $id ?></h4>
        <form method="POST">
            <input type="hidden" name="category_id" value="<?= $id ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Tên Danh Mục Hiện Tại:</label>
                <input type="text" name="category_name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="index.php?page=admin_dashboard" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" name="btnUpdateCategory" class="btn btn-warning fw-bold px-4">Cập Nhật</button>
            </div>
        </form>
    </div>
</div>
