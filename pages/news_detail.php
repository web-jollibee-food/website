<?php
// pages/news_detail.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

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
    <div class="mx-auto" style="max-width: 800px;">
        <!-- Nút Back -->
        <a href="index.php?page=news" class="btn btn-sm btn-outline-secondary mb-4" style="border-radius: 8px; font-weight: 600;">
            <i class="fa fa-arrow-left me-1"></i> Quay lại Tin tức
        </a>

        <!-- Tiêu đề & Thông tin -->
        <h1 class="fw-extrabold text-dark mb-3" style="font-family: 'Nunito', sans-serif; font-size: 2.2rem; line-height: 1.3;">
            <?= htmlspecialchars($news['title']) ?>
        </h1>
        <p class="text-muted small mb-4">
            <i class="fa fa-calendar-alt me-1"></i> Đăng lúc: <?= date('d/m/Y H:i', strtotime($news['created_at'] . ' UTC')) ?> 
            <span class="mx-2">|</span> 
            <span class="badge bg-danger">Tin Tức Jollibee</span>
        </p>

        <!-- Banner ảnh -->
        <div class="mb-5 rounded shadow-sm overflow-hidden bg-light" style="max-height: 450px;">
            <?php if (strpos($news['image_url'] ?? '', 'uploads/') === 0 || strpos($news['image_url'] ?? '', 'http://') === 0 || strpos($news['image_url'] ?? '', 'https://') === 0): ?>
                <img src="<?= htmlspecialchars($news['image_url']) ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?= htmlspecialchars($news['title']) ?>">
            <?php else: ?>
                <div class="w-100 d-flex align-items-center justify-content-center bg-danger-subtle text-danger py-5" style="font-size: 10rem;">
                    <?= htmlspecialchars($news['image_url'] ?: '📰') ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tóm tắt (In đậm) -->
        <?php if (!empty($news['summary'])): ?>
            <p class="fs-5 fw-semibold text-secondary mb-4 lh-base" style="border-left: 4px solid var(--red); padding-left: 15px;">
                <?= htmlspecialchars($news['summary']) ?>
            </p>
        <?php endif; ?>

        <!-- Nội dung bài viết -->
        <div class="text-dark lh-lg fs-6" style="text-align: justify; font-family: 'Nunito', sans-serif; white-space: pre-line;">
            <?= htmlspecialchars($news['content']) ?>
        </div>
    </div>
</div>
