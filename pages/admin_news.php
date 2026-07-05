<?php
// pages/admin_news.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

if (empty($_SESSION['logged_in']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}
?>
<div class="container py-5">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white);">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h4 class="fw-extrabold text-danger m-0" style="font-family: 'Fredoka One', cursive;">
                <i class="fa fa-newspaper me-2"></i>Quản Lý Tin Tức & Khuyến Mãi
            </h4>
            <a href="index.php?page=admin_news_add" class="btn btn-jollibee py-2 px-4" style="border-radius: 8px; font-weight: 700; font-size: 0.9rem;">
                <i class="fa fa-plus me-1"></i> Thêm Bài Viết Mới
            </a>
        </div>

        <!-- Thông báo Toast -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success py-2.5 px-3 mb-4 border-0" style="border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                <i class="fa fa-check-circle me-1"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2.5 px-3 mb-4 border-0" style="border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                <i class="fa fa-times-circle me-1"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle border text-start">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 80px;">Mã bài</th>
                        <th style="width: 100px; text-align: center;">Hình ảnh</th>
                        <th>Tiêu đề tin tức</th>
                        <th>Tóm tắt ngắn</th>
                        <th style="width: 180px;">Ngày đăng bài</th>
                        <th class="text-center" style="width: 180px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT * FROM news ORDER BY id DESC";
                    $result = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
                    if (count($result) > 0): 
                        foreach($result as $row):
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#<?= $row['id'] ?></td>
                        <td style="text-align: center;">
                            <?php if (strpos($row['image_url'] ?? '', 'uploads/') === 0 || strpos($row['image_url'] ?? '', 'http://') === 0 || strpos($row['image_url'] ?? '', 'https://') === 0): ?>
                                <img src="<?= htmlspecialchars($row['image_url']) ?>" style="width: 60px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #E2E8F0;">
                            <?php else: ?>
                                <span style="font-size: 1.8rem;"><?= htmlspecialchars($row['image_url'] ?: '📰') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold text-dark" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['title']) ?>"><?= htmlspecialchars($row['title']) ?></td>
                        <td class="text-secondary small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['summary'] ?? '') ?>"><?= htmlspecialchars($row['summary'] ?: 'Không có') ?></td>
                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'] . ' UTC')) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="index.php?page=admin_news_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning text-dark" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600; padding: 4px 10px;">
                                    <i class="fa fa-edit me-1"></i> Sửa
                                </a>
                                <form method="POST" action="index.php?page=admin_news" class="m-0 d-inline confirm-form" data-confirm-text="Bạn có chắc chắn muốn xóa bài viết này? Hành động này sẽ xóa vĩnh viễn!">
                                    <input type="hidden" name="action" value="delete_news">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger text-white" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600; padding: 4px 10px;">
                                        <i class="fa fa-trash me-1"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                        <tr><td colspan="6" class="text-center p-4 text-muted small">Chưa có tin tức nào trong hệ thống.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
