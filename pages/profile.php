<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

// Kiểm tra đăng nhập
if (empty($_SESSION['logged_in'])) {
    header("Location: index.php?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Không tìm thấy thông tin người dùng.");
}
?>
<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <!-- Cột bên trái: Cập nhật thông tin -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white); border-top: 5px solid var(--red) !important;">
                <h3 class="fw-bold mb-4" style="font-family: 'Fredoka One', cursive; color: var(--red);">👤 Thông tin cá nhân</h3>
                
                <?php if (isset($success_profile)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success_profile ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_profile)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error_profile ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ Email</label>
                        <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly style="border-radius: 8px;">
                        <div class="form-text text-muted">Email đăng ký tài khoản không thể thay đổi.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Địa chỉ nhận hàng mặc định</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ giao hàng thường dùng của bạn..." style="border-radius: 8px;"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" name="btnUpdateProfile" class="btn btn-jollibee w-100 py-2.5" style="border-radius: 8px;">Cập nhật thông tin</button>
                </form>
            </div>
        </div>

        <!-- Cột bên phải: Đổi mật khẩu -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background: var(--white); border-top: 5px solid var(--gold) !important;">
                <h3 class="fw-bold mb-4" style="font-family: 'Fredoka One', cursive; color: var(--gold-dark);">🔑 Đổi mật khẩu</h3>
                
                <?php if (isset($success_password)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success_password ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_password)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error_password ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="update_password">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" name="old_password" class="form-control" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" required style="border-radius: 8px;">
                        <div class="form-text text-muted">Mật khẩu mới phải có tối thiểu 6 ký tự.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" required style="border-radius: 8px;">
                    </div>
                    
                    <button type="submit" name="btnUpdatePassword" class="btn btn-jollibee w-100 py-2.5" style="background-color: var(--gold); border-color: var(--gold); color: #000; border-radius: 8px;">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
