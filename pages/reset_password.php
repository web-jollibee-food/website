<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- ĐẶT LẠI MẬT KHẨU -->
<?php
$email_param = $_GET['email'] ?? '';
?>
<div class="container py-5">
    <div class="auth-card mx-auto shadow-sm" style="max-width: 500px;">
        <div class="text-center mb-4">
            <span style="font-size: 3rem;">🛡️</span>
            <h3 class="text-danger fw-extrabold mt-2 mb-1" style="font-family: 'Fredoka One', cursive;">Đặt Lại Mật Khẩu</h3>
            <p class="text-muted small">Nhập mã xác minh 6 số và mật khẩu mới của bạn.</p>
        </div>
        
        <?php if(isset($_GET['sent'])): ?>
            <div class="alert alert-success py-2.5 px-3 small border-0 text-success mb-3" style="background-color: #F0FDF4; font-weight: 500; border-radius: 8px;">
                <i class="fa fa-envelope me-2"></i>Mã xác minh đã được gửi về email <strong><?= htmlspecialchars($email_param) ?></strong>. Vui lòng kiểm tra hộp thư!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['logged'])): ?>
            <div class="alert alert-warning py-2.5 px-3 small border-0 text-warning mb-3" style="background-color: #FFFBEB; font-weight: 500; border-radius: 8px; line-height: 1.4;">
                <i class="fa fa-info-circle me-2"></i><strong>[Chế độ Demo Local]:</strong> Mã xác minh đã được lưu trữ bảo mật trong thư mục server: <strong>`jollibee/logs_emails/`</strong> trên ổ đĩa của bạn (thay vì hiện trực tiếp trên màn hình để bảo mật thông tin). Hãy mở thư mục này để xem mã!
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger py-2 px-3 small border-0 text-danger" style="background-color: #FEF2F2; font-weight: 500; border-radius: 8px;">
                <i class="fa fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success py-2 px-3 small border-0 text-success" style="background-color: #F0FDF4; font-weight: 500; border-radius: 8px;">
                <i class="fa fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email_param) ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Mã Xác Minh (6 chữ số) *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-key"></i></span>
                    <input type="text" name="code" class="form-control border-start-0 text-center fw-bold" placeholder="Nhập 6 số" style="border-radius: 0 8px 8px 0; letter-spacing: 2px; font-size: 1.1rem;" maxlength="6" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Mật Khẩu Mới *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="Mật khẩu mới (ít nhất 6 ký tự)" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold small text-secondary">Xác Nhận Mật Khẩu Mới *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-shield-alt"></i></span>
                    <input type="password" name="confirm_password" class="form-control border-start-0" placeholder="Nhập lại mật khẩu mới" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <button type="submit" name="btnResetPassword" class="btn btn-jollibee w-100 py-2.5 mb-3" style="font-size: 0.95rem;">Cập Nhật Mật Khẩu</button>
        </form>
        
        <div class="text-center mt-3 pt-3 border-top" style="border-top-color: #F1F5F9 !important;">
            <a href="index.php?page=login" class="text-secondary small fw-bold text-decoration-none"><i class="fa fa-arrow-left me-1"></i> Quay lại Đăng Nhập</a>
        </div>
    </div>
</div>
