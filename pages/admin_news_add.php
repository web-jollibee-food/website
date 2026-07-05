<?php
// pages/admin_news_add.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

if (empty($_SESSION['logged_in']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}
?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 680px; border-radius: 12px; background: var(--white);">
        <div class="border-bottom pb-3 mb-4">
            <h4 class="fw-extrabold text-danger m-0" style="font-family: 'Fredoka One', cursive;">
                <i class="fa fa-newspaper me-2"></i>Thêm Tin Tức Mới
            </h4>
        </div>

        <form method="POST" action="index.php?page=admin_news" enctype="multipart/form-data" class="text-start">
            <input type="hidden" name="btnAddNews" value="1">
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Tiêu đề bài viết <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề tin tức..." required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Tóm tắt ngắn bài viết <span class="text-danger">*</span></label>
                <textarea name="summary" class="form-control" rows="2" placeholder="Nhập tóm tắt ngắn hiển thị ở trang chủ danh sách..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Nội dung chi tiết <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="8" placeholder="Nhập nội dung chi tiết bài viết tin tức tại đây..." required></textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary">Tải ảnh lên (File)</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary">Hoặc dán URL ảnh ngoài / Emoji</label>
                    <input type="text" name="image_url" class="form-control" placeholder="Ví dụ: 🍟 hoặc link https://...">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-jollibee py-2 px-4" style="border-radius: 8px; font-weight: 700; font-size: 0.95rem;">
                    <i class="fa fa-save me-1"></i> Đăng Bài Viết
                </button>
                <a href="index.php?page=admin_news" class="btn btn-outline-secondary py-2 px-4" style="border-radius: 8px; font-weight: 600; font-size: 0.95rem;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
