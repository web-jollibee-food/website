<?php
// pages/qrcode.php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');

$id = (int)$_GET['id']; 
$st = $conn->prepare("SELECT tong_tien, trang_thai_tt, ngay_dat FROM orders WHERE id = ?"); 
$st->bind_param("i", $id); 
$st->execute(); 
$order = $st->get_result()->fetch_assoc();
$st->close();

if (!$order) {
    echo "<div class='container py-5 text-center'><div class='alert alert-danger'>Không tìm thấy đơn hàng!</div></div>";
    exit;
}

$order_amt = $order['tong_tien'];

// Chuyển đổi chuẩn hóa múi giờ UTC của SQLite sang Timestamp
$order_time = strtotime($order['ngay_dat'] . ' UTC');
$expire_time = $order_time + 5 * 60; // Hết hạn sau 5 phút
$time_left = $expire_time - time();

if ($time_left < 0) {
    $time_left = 0;
}

// Lấy thông tin cấu hình ngân hàng từ .env (hoặc mặc định MB Bank demo)
$bank_id = get_env_val('BANK_ID', 'MB'); 
$bank_account = get_env_val('BANK_ACCOUNT', '0868067476'); 
$bank_acc_name = get_env_val('BANK_ACC_NAME', 'NGUYEN VAN A');

$memo = "JB" . $id;
$qr_url = "https://img.vietqr.io/image/" . $bank_id . "-" . $bank_account . "-compact.png?amount=" . $order_amt . "&addInfo=" . urlencode($memo) . "&accountName=" . urlencode($bank_acc_name);
?>
<div class="container py-5 text-center">
    <div class="card border-0 p-4 shadow-sm mx-auto" style="max-width:480px; border-radius: 12px; background: var(--white); border-top: 5px solid var(--red) !important;">
        <h4 class="text-danger fw-extrabold mb-3" style="font-family: 'Fredoka One', cursive;">
            <i class="fa fa-university me-2"></i>Chuyển Khoản Ngân Hàng
        </h4>
        <p class="my-1 fw-bold text-secondary">Đơn hàng: <span class="text-danger">#JB-<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></span></p>
        <p class="fs-4 fw-extrabold text-danger mb-3" style="font-family: 'Nunito', sans-serif;">
            <?= number_format($order_amt,0,',','.') ?>đ
        </p>

        <!-- Đồng hồ đếm ngược 5 phút -->
        <div class="mb-3 text-center">
            <span class="badge bg-danger p-2 fs-6" style="border-radius: 6px; font-family: 'Fredoka One', cursive;">
                ⏳ Thời gian còn lại: <span id="countdown-timer">05:00</span>
            </span>
        </div>
        
        <div id="qr-container" style="<?= $time_left <= 0 ? 'opacity: 0.3; pointer-events: none;' : '' ?>">
            <div class="my-3 p-3 bg-light rounded d-inline-block border" style="border-style: dashed !important;">
                <img src="<?= $qr_url ?>" class="img-fluid bg-white rounded shadow-sm p-2 zoomable-img" style="width: 240px; height: 240px; cursor: zoom-in;" alt="VietQR">
            </div>
            
            <!-- Bảng thông tin chuyển khoản chi tiết -->
            <div class="text-start mx-auto p-3 mb-4 rounded bg-light border" style="max-width: 380px; font-size: 0.9rem;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Ngân hàng:</span>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($bank_id) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Số tài khoản:</span>
                    <span class="fw-bold text-dark text-copy" style="cursor: pointer;" onclick="copyText('<?= htmlspecialchars($bank_account) ?>', 'Đã copy số tài khoản!')">
                        <?= htmlspecialchars($bank_account) ?> <i class="fa fa-copy ms-1 text-muted"></i>
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Chủ tài khoản:</span>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($bank_acc_name) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Nội dung chuyển khoản:</span>
                    <span class="fw-bold text-danger text-copy" style="cursor: pointer;" onclick="copyText('<?= htmlspecialchars($memo) ?>', 'Đã copy nội dung chuyển khoản!')">
                        <?= htmlspecialchars($memo) ?> <i class="fa fa-copy ms-1 text-muted"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Trạng thái chờ quét mã -->
        <?php if ($time_left <= 0): ?>
            <div id="status-alert" class="alert alert-danger py-2.5 px-3 mb-4 border-0 d-flex align-items-center justify-content-center gap-2" style="background-color: #FEF2F2; color: #EF4444; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                <i class="fa fa-times-circle me-1"></i>
                <span>Giao dịch đã hết hạn thanh toán (5 phút)! Vui lòng đặt đơn hàng mới.</span>
            </div>
        <?php else: ?>
            <div id="status-alert" class="alert alert-warning py-2.5 px-3 mb-4 border-0 d-flex align-items-center justify-content-center gap-2" style="background-color: #FFFBEB; color: #D97706; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span>Đang chờ bạn chuyển khoản... Tự động chuyển trang khi nhận tiền.</span>
            </div>
        <?php endif; ?>

        <div class="d-grid gap-2">
            <a href="index.php?page=home" class="btn btn-outline-secondary py-2" style="border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                Hủy giao dịch & Quay lại
            </a>
        </div>
    </div>
</div>

<script>
// Hàm copy nhanh thông tin
function copyText(text, msg) {
    navigator.clipboard.writeText(text).then(() => {
        alert(msg);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let timeLeft = <?= $time_left ?>;
    const countdownTimer = document.getElementById('countdown-timer');
    const qrContainer = document.getElementById('qr-container');
    const statusAlert = document.getElementById('status-alert');

    function updateCountdown() {
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            clearInterval(checkStatusInterval);
            
            if (qrContainer) {
                qrContainer.style.opacity = '0.3';
                qrContainer.style.pointerEvents = 'none';
            }
            
            if (statusAlert) {
                statusAlert.className = 'alert alert-danger py-2.5 px-3 mb-4 border-0 d-flex align-items-center justify-content-center gap-2';
                statusAlert.style.backgroundColor = '#FEF2F2';
                statusAlert.style.color = '#EF4444';
                statusAlert.innerHTML = '<i class="fa fa-times-circle me-1"></i><span>Giao dịch đã hết hạn thanh toán (5 phút)! Vui lòng đặt đơn hàng mới.</span>';
            }
            
            if (countdownTimer) {
                countdownTimer.innerHTML = 'ĐÃ HẾT HẠN';
            }
            return;
        }
        
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        if (countdownTimer) {
            countdownTimer.innerHTML = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        timeLeft--;
    }

    const countdownInterval = setInterval(updateCountdown, 1000);
    updateCountdown(); // Chạy ngay lập tức

    // Lắng nghe trạng thái đơn hàng mỗi 3 giây (AJAX Polling)
    const orderId = <?= $id ?>;
    const checkStatusInterval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(checkStatusInterval);
            return;
        }
        
        fetch(`index.php?page=check_payment_status&id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'Đã thanh toán') {
                    clearInterval(checkStatusInterval);
                    clearInterval(countdownInterval);
                    window.location.href = `index.php?page=success&id=${orderId}`;
                }
            })
            .catch(err => console.error("Lỗi kiểm tra trạng thái thanh toán:", err));
    }, 3000);
});
</script>
