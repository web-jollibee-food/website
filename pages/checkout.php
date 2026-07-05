<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

// Kiểm tra giỏ hàng trống
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Lấy thông tin người dùng hiện tại
$default_name = $_SESSION['user_name'] ?? '';
$default_phone = '';
$default_address = '';

if (!empty($_SESSION['user_id'])) {
    $st_user = $conn->prepare("SELECT phone, address FROM users WHERE id = ?");
    $st_user->bind_param("i", $_SESSION['user_id']);
    $st_user->execute();
    $user_data = $st_user->get_result()->fetch_assoc();
    $st_user->close();
    
    $st_last = $conn->prepare("SELECT ho_ten, so_dien_thoai, dia_chi FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $st_last->bind_param("i", $_SESSION['user_id']);
    $st_last->execute();
    $last_order = $st_last->get_result()->fetch_assoc();
    $st_last->close();
    
    if ($last_order) {
        $default_name = $last_order['ho_ten'];
        $default_phone = $last_order['so_dien_thoai'];
        $default_address = $last_order['dia_chi'];
    } elseif ($user_data) {
        $default_phone = $user_data['phone'] ?? '';
        $default_address = $user_data['address'] ?? '';
    }
}

// Tính toán giỏ hàng và tiền thanh toán
$subtotal = 0;
$checkout_items = [];
foreach ($_SESSION['cart'] as $p_id => $qty) {
    $st = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $st->bind_param("i", $p_id);
    $st->execute();
    $p = $st->get_result()->fetch_assoc();
    $st->close();
    if ($p) {
        $actual_price = ($p['price_discount'] > 0 && $p['price_discount'] < $p['price']) ? $p['price_discount'] : $p['price'];
        $item_total = $actual_price * $qty;
        $subtotal += $item_total;
        $p['qty'] = $qty;
        $p['total'] = $item_total;
        $p['actual_price'] = $actual_price;
        $checkout_items[] = $p;
    }
}

$shipping_fee = ($subtotal >= 150000) ? 0 : 20000;

// Tính toán mã giảm giá
$discount = 0.00;
$promo_code = '';
if (!empty($_SESSION['applied_promo'])) {
    $promo_code = $_SESSION['applied_promo']['code'];
    $promo_type = $_SESSION['applied_promo']['type'];
    $promo_value = (float)$_SESSION['applied_promo']['value'];
    
    if ($promo_type === 'percentage') {
        $discount = ($subtotal * $promo_value) / 100;
    } else {
        $discount = $promo_value;
    }
    
    if ($discount > $subtotal) {
        $discount = $subtotal;
    }
}

$grand_total = ($subtotal - $discount) + $shipping_fee;
?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Cột bên trái: Điền thông tin giao hàng -->
        <div class="col-md-7">
            <div class="card border-0 p-4 shadow-sm" style="border-radius: 12px; background: var(--white); border-top: 5px solid var(--red) !important; height: 100%;">
                <div class="mb-4 pb-2 border-bottom" style="border-bottom-color: #F1F5F9 !important;">
                    <h3 class="text-danger fw-extrabold" style="font-family: 'Fredoka One', cursive;"><i class="fa fa-shopping-basket me-2"></i>Thông Tin Giao Hàng</h3>
                    <p class="text-muted small">Vui lòng điền thông tin chính xác để Jollibee giao gà nóng hổi đến cho bạn!</p>
                </div>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Họ và Tên người nhận *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-user"></i></span>
                            <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($default_name) ?>" style="border-radius: 0 8px 8px 0;" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Số điện thoại liên hệ *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-phone"></i></span>
                            <input type="tel" name="so_dien_thoai" pattern="^0\d{9}$" class="form-control" placeholder="Ví dụ: 0912345678" value="<?= htmlspecialchars($default_phone) ?>" style="border-radius: 0 8px 8px 0;" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Địa chỉ nhận hàng *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-map-marker-alt"></i></span>
                            <textarea name="dia_chi" class="form-control" style="min-height:90px; border-radius: 0 8px 8px 0;" placeholder="Địa chỉ chi tiết (Số nhà, Tên đường, Phường/Xã, Quận/Huyện...)" required><?= htmlspecialchars($default_address) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Ghi chú cho tài xế</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-comment-dots"></i></span>
                            <textarea name="ghi_chu" class="form-control" style="min-height:60px; border-radius: 0 8px 8px 0;" placeholder="Ví dụ: Giao ở cổng sau, gọi trước khi đến 5 phút..."></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">Phương thức thanh toán</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-credit-card"></i></span>
                            <select name="phuong_thuc_tt" class="form-select" style="border-radius: 0 8px 8px 0;">
                                <option value="COD">💵 Tiền mặt khi nhận hàng (COD)</option>
                                <option value="Chuyển khoản">🏦 Chuyển khoản ngân hàng (VietQR quét tự động)</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" name="btnCheckout" class="btn btn-jollibee w-100 py-3" style="font-size: 1.05rem; border-radius: 8px;"><i class="fa fa-paper-plane me-2"></i>ĐẶT HÀNG NGAY</button>
                </form>
            </div>
        </div>

        <!-- Cột bên phải: Tóm tắt đơn hàng & Mã giảm giá -->
        <div class="col-md-5">
            <div class="card border-0 p-4 shadow-sm" style="border-radius: 12px; background: var(--white); border-top: 5px solid var(--gold) !important; height: 100%;">
                <h4 class="mb-4 fw-bold text-dark" style="font-family: 'Fredoka One', cursive; color: var(--gold-dark);"><i class="fa fa-file-invoice-dollar me-2"></i>Tóm tắt đơn hàng</h4>
                
                <!-- Danh sách món đặt -->
                <div class="mb-4 border-bottom pb-3" style="max-height: 250px; overflow-y: auto;">
                    <?php foreach ($checkout_items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div style="max-width: 75%;">
                                <span class="fw-bold text-dark small d-block"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="text-muted small"><?= number_format($item['actual_price'], 0, ',', '.') ?>đ × <?= $item['qty'] ?></span>
                            </div>
                            <span class="fw-bold text-dark small"><?= number_format($item['total'], 0, ',', '.') ?>đ</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Form áp dụng mã giảm giá -->
                <div class="mb-4 border-bottom pb-4">
                    <label class="form-label small fw-bold text-secondary">Bạn có mã giảm giá?</label>
                    
                    <?php if (!empty($_SESSION['promo_error'])): ?>
                        <div class="alert alert-danger py-2 px-3 small mb-2" style="border-radius: 8px;">
                            <?= $_SESSION['promo_error'] ?>
                            <?php unset($_SESSION['promo_error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($_SESSION['promo_success'])): ?>
                        <div class="alert alert-success py-2 px-3 small mb-2" style="border-radius: 8px;">
                            <?= $_SESSION['promo_success'] ?>
                            <?php unset($_SESSION['promo_success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($promo_code)): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="apply_promo">
                            <div class="input-group">
                                <input type="text" name="promo_code" class="form-control" placeholder="Ví dụ: JOLLIBEE10" style="border-radius: 8px 0 0 8px; text-transform: uppercase;" required>
                                <button type="submit" class="btn btn-outline-danger" style="border-radius: 0 8px 8px 0; font-weight: 700;">Áp dụng</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background-color: #FEF2F2; border: 1px dashed var(--red);">
                            <div>
                                <span class="small text-secondary d-block">Đang áp dụng mã:</span>
                                <strong class="text-danger fs-5"><?= htmlspecialchars($promo_code) ?></strong>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="remove_promo">
                                <button type="submit" class="btn btn-link text-danger text-decoration-none p-0 fw-bold small"><i class="fa fa-trash-alt me-1"></i>Gỡ bỏ</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tổng thanh toán -->
                <div class="mt-auto">
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Tạm tính hàng:</span>
                        <strong class="text-dark"><?= number_format($subtotal, 0, ',', '.') ?>đ</strong>
                    </div>
                    
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-3 small text-success">
                            <span>Giảm giá:</span>
                            <strong class="fw-bold">-<?= number_format($discount, 0, ',', '.') ?>đ</strong>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Phí giao hàng:</span>
                        <strong class="text-dark"><?= $shipping_fee === 0 ? 'Miễn phí' : number_format($shipping_fee, 0, ',', '.') . 'đ' ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2 fs-4 fw-bold border-top pt-3 text-danger" style="border-top-style: dashed !important; font-family: 'Nunito', sans-serif;">
                        <span>Tổng thanh toán:</span>
                        <span><?= number_format($grand_total, 0, ',', '.') ?>đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
