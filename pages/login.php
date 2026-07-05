<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- ĐĂNG NHẬP -->
<div class="container py-5">
    <div class="auth-card mx-auto shadow-sm">
        <div class="text-center mb-4">
            <span style="font-size: 3rem;">🐝</span>
            <h3 class="text-danger fw-extrabold mt-2 mb-1" style="font-family: 'Fredoka One', cursive;">Đăng Nhập Jollibee</h3>
            <p class="text-muted small">Chào mừng bạn quay lại với Gà Giòn Vui Vẻ!</p>
        </div>
        
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
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Email Đăng Ký</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="admin@jollibee.vn" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-bold small text-secondary m-0">Mật Khẩu</label>
                    <a href="index.php?page=forgot_password" class="text-danger small text-decoration-none fw-semibold">Quên mật khẩu?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="Mật khẩu của bạn" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <button type="submit" name="btnLogin" class="btn btn-jollibee w-100 py-2.5 mb-3" style="font-size: 0.95rem;">Đăng Nhập</button>
        </form>
        
        <div class="text-center mt-3 pt-3 border-top" style="border-top-color: #F1F5F9 !important;">
            <span class="text-muted small">Chưa có tài khoản?</span>
            <a href="index.php?page=register" class="text-danger small fw-bold text-decoration-none ms-1">Đăng ký ngay</a>
        </div>
    </div>
</div>
