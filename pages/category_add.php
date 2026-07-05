<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!'); checkAdminOrDie(); ?>
<div class="container py-5">
    <div class="card shadow p-4 mx-auto" style="max-width:500px;">
        <h4 class="text-danger fw-bold border-bottom pb-2 mb-3">Thêm Danh Mục Jollibee</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Tên Danh Mục Mới:</label>
                <input type="text" name="category_name" class="form-control" placeholder="Ví dụ: Mỳ Ý, Combo Gia Đình..." required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="index.php?page=admin_dashboard" class="btn btn-secondary">Quay lại</a>
                <button type="submit" name="btnAddCategory" class="btn btn-jollibee px-4">Thêm Mới</button>
            </div>
        </form>
    </div>
</div>
