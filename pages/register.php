<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- ĐĂNG KÝ -->
<div class="container py-5">
    <div class="auth-card mx-auto shadow-sm" style="max-width: 520px;">
        <div class="text-center mb-4">
            <span style="font-size: 3rem;">🐝</span>
            <h3 class="text-danger fw-extrabold mt-2 mb-1" style="font-family: 'Fredoka One', cursive;">Đăng Ký Thành Viên</h3>
            <p class="text-muted small">Tạo tài khoản để nhận nhiều ưu đãi từ Jollibee!</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger py-2 px-3 small border-0 text-danger" style="background-color: #FEF2F2; font-weight: 500; border-radius: 8px;">
                <i class="fa fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Họ và Tên *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-user"></i></span>
                    <input type="text" name="full_name" class="form-control border-start-0" placeholder="Nguyễn Văn A" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Email liên hệ *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="vi_du@gmail.com" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Số điện thoại *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-phone"></i></span>
                    <input type="tel" name="phone" pattern="^0\d{9}$" class="form-control border-start-0" placeholder="Ví dụ: 0987654321" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Mật khẩu *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="Ít nhất 6 ký tự" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold small text-secondary">Xác nhận mật khẩu *</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-shield-alt"></i></span>
                    <input type="password" name="confirm_password" class="form-control border-start-0" placeholder="Nhập lại mật khẩu" style="border-radius: 0 8px 8px 0;" required>
                </div>
            </div>
            
            <button type="submit" name="btnRegister" class="btn btn-jollibee w-100 py-2.5 mb-3" style="font-size: 0.95rem;">Đăng Ký Tài Khoản</button>
        </form>
        
        <div class="text-center mt-3 pt-3 border-top" style="border-top-color: #F1F5F9 !important;">
            <span class="text-muted small">Đã có tài khoản?</span>
            <a href="index.php?page=login" class="text-danger small fw-bold text-decoration-none ms-1">Đăng nhập ngay</a>
        </div>
    </div>
</div>
