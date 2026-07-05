<?php
// pages/news.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

// Lấy danh sách tin tức
$query = "SELECT * FROM news ORDER BY id DESC";
$news_list = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-extrabold text-danger mb-2" style="font-family: 'Fredoka One', cursive; font-size: 2.5rem;">
            📰 Tin Tức & Khuyến Mãi
        </h2>
        <p class="text-secondary mx-auto" style="max-width: 600px;">Cập nhật những thông tin mới nhất, các chương trình ưu đãi đặc biệt và sự kiện nổi bật của Jollibee Vietnam!</p>
    </div>

    <?php if (count($news_list) > 0): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach($news_list as $news): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm transition-hover" style="border-radius: 12px; overflow: hidden; background: var(--white);">
                        <!-- Thumbnail ảnh -->
                        <div style="height: 200px; overflow: hidden; position: relative; background: #F8FAFC;">
                            <?php if (strpos($news['image_url'] ?? '', 'uploads/') === 0 || strpos($news['image_url'] ?? '', 'http://') === 0 || strpos($news['image_url'] ?? '', 'https://') === 0): ?>
                                <img src="<?= htmlspecialchars($news['image_url']) ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?= htmlspecialchars($news['title']) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="font-size: 5rem;">
                                    <?= htmlspecialchars($news['image_url'] ?: '📰') ?>
                                </div>
                            <?php endif; ?>
                            <span class="badge bg-danger position-absolute top-0 end-0 m-3 px-2.5 py-1.5 fs-7" style="border-radius: 6px;">Hot News</span>
                        </div>

                        <!-- Nội dung card -->
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-2"><i class="fa fa-calendar-alt me-1"></i> <?= date('d/m/Y H:i', strtotime($news['created_at'] . ' UTC')) ?></p>
                                <h5 class="card-title fw-bold text-dark mb-2" style="font-size: 1.15rem; line-height: 1.4;"><?= htmlspecialchars($news['title']) ?></h5>
                                <p class="card-text text-secondary small line-clamp-3 mb-4"><?= htmlspecialchars($news['summary']) ?></p>
                            </div>
                            <div class="d-grid">
                                <a href="index.php?page=news_detail&id=<?= $news['id'] ?>" class="btn btn-outline-jollibee py-2" style="border-radius: 8px; font-weight: 700; font-size: 0.9rem;">
                                    Đọc thêm <i class="fa fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <span class="fs-1 text-muted">📭</span>
            <h5 class="text-secondary mt-3">Chưa có bài đăng tin tức nào.</h5>
        </div>
    <?php endif; ?>
</div>

<style>
.transition-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.transition-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
