<?php
// pages/admin_news_edit.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

if (empty($_SESSION['logged_in']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

$id = (int)$_GET['id'];
$st = $conn->prepare("SELECT * FROM news WHERE id = ?");
$st->bind_param("i", $id);
$st->execute();
$news = $st->get_result()->fetch_assoc();
$st->close();

if (!$news) {
    echo "<div class='container py-5 text-center'><div class='alert alert-danger'>Bài viết không tồn tại!</div></div>";
    exit;
}
?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 680px; border-radius: 12px; background: var(--white);">
        <div class="border-bottom pb-3 mb-4">
            <h4 class="fw-extrabold text-danger m-0" style="font-family: 'Fredoka One', cursive;">
                <i class="fa fa-newspaper me-2"></i>Chỉnh Sửa Bài Viết Tin Tức
            </h4>
        </div>

        <form method="POST" action="index.php?page=admin_news" enctype="multipart/form-data" class="text-start">
            <input type="hidden" name="btnUpdateNews" value="1">
            <input type="hidden" name="id" value="<?= $news['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Tiêu đề bài viết <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($news['title']) ?>" placeholder="Nhập tiêu đề tin tức..." required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Tóm tắt ngắn bài viết <span class="text-danger">*</span></label>
                <textarea name="summary" class="form-control" rows="2" placeholder="Nhập tóm tắt ngắn..." required><?= htmlspecialchars($news['summary']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Nội dung chi tiết <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="8" placeholder="Nhập nội dung chi tiết..." required><?= htmlspecialchars($news['content']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary d-block">Hình ảnh hiện tại</label>
                <div class="p-2 border rounded bg-light d-inline-block mb-2">
                    <?php if (strpos($news['image_url'] ?? '', 'uploads/') === 0 || strpos($news['image_url'] ?? '', 'http://') === 0 || strpos($news['image_url'] ?? '', 'https://') === 0): ?>
                        <img src="<?= htmlspecialchars($news['image_url']) ?>" style="height: 100px; object-fit: cover; border-radius: 6px;">
                    <?php else: ?>
                        <span style="font-size: 3rem;"><?= htmlspecialchars($news['image_url'] ?: '📰') ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary">Thay ảnh mới (File)</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-secondary">Hoặc thay URL ảnh / Emoji</label>
                    <input type="text" name="image_url" class="form-control" value="<?= (strpos($news['image_url'] ?? '', 'uploads/') !== 0) ? htmlspecialchars($news['image_url'] ?? '') : '' ?>" placeholder="Ví dụ: 🍟 hoặc link https://...">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-jollibee py-2 px-4" style="border-radius: 8px; font-weight: 700; font-size: 0.95rem;">
                    <i class="fa fa-save me-1"></i> Lưu Thay Đổi
                </button>
                <a href="index.php?page=admin_news" class="btn btn-outline-secondary py-2 px-4" style="border-radius: 8px; font-weight: 600; font-size: 0.95rem;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
