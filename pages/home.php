<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!-- GIAO DIỆN TRANG CHỦ -->
<?php if (isset($_GET['added'])): ?>
    <div class="toast-jollibee">✓ Đã thêm <strong><?= htmlspecialchars($_GET['added']) ?></strong> vào giỏ hàng!</div>
<?php endif; ?>

<!-- Hero Section split-screen style -->
<div style="background: linear-gradient(135deg, var(--red-dark) 0%, var(--red) 100%); color: white; padding: 70px 0; border-bottom: 5px solid var(--gold);" class="no-print">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7 text-start text-white">
                <h1 style="font-family:'Fredoka One', cursive; font-size: 3.5rem; line-height: 1.2; text-shadow: 0 2px 4px rgba(0,0,0,0.15);" class="mb-3 text-white">
                    Thưởng Thức <br><span style="color: var(--gold);">Gà Giòn Vui Vẻ!</span>
                </h1>
                <p class="fs-5 mb-4 text-white-50" style="font-weight: 500; letter-spacing: 0.3px;">
                    Đặt hàng trực tuyến dễ dàng và nhanh chóng. Gà rán nóng hổi, giòn rụm sẽ giao đến tận tay bạn chỉ trong tích tắc!
                </p>
                <a href="#menu" class="btn btn-jollibee px-4 py-2.5 fs-6" style="background-color: var(--gold); color: #000; font-weight: 800; border-radius: 8px;">
                    <i class="fa fa-utensils me-2"></i>Đặt Món Ngay
                </a>
            </div>
            <div class="col-md-5 d-none d-md-flex justify-content-center">
                <div style="width: 250px; height: 250px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10rem; animation: bounce 3s infinite alternate;">
                    🍗
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes bounce {
    0% { transform: translateY(0) rotate(-5deg); }
    100% { transform: translateY(-15px) rotate(5deg); }
}
.cat-pill {
    background-color: var(--white);
    color: var(--dark);
    border: 1px solid var(--border);
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 700;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.cat-pill:hover {
    border-color: var(--red);
    color: var(--red);
    background-color: #FFF1F2;
}
.cat-pill.active {
    background-color: var(--red) !important;
    color: var(--white) !important;
    border-color: var(--red) !important;
    box-shadow: 0 4px 10px rgba(215, 27, 42, 0.2);
}
.category-nav-wrapper {
    position: sticky;
    top: 70px; /* height of navbar */
    z-index: 90;
    background-color: var(--cream);
    padding: 15px 0;
    border-bottom: 1px solid var(--border);
    margin-bottom: 30px;
}
.category-section {
    scroll-margin-top: 150px; /* offset for sticky navbar + category nav */
}
</style>

<!-- Sticky Category Navigation Bar -->
<div class="category-nav-wrapper no-print">
    <div class="container d-flex gap-2 overflow-auto py-1">
        <?php 
        $cats = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
        foreach($cats as $index => $c) { 
            $active_class = ($index === 0) ? 'active' : '';
            echo '<button class="btn cat-pill ' . $active_class . '" data-cat-id="'.$c['id'].'" onclick="scrollToCategory(\''.$c['id'].'\')">'.$c['name'].'</button>'; 
        }
        ?>
    </div>
</div>

<!-- Product list organized by Category Sections (Scrollspy) -->
<div class="container py-2" id="menu">
    <?php foreach($cats as $c): 
        $cat_id = $c['id'];
        $prods = $conn->query("SELECT * FROM products WHERE category_id = $cat_id")->fetch_all(MYSQLI_ASSOC);
    ?>
    <div id="category-sec-<?= $c['id'] ?>" class="category-section mb-5" data-cat-id="<?= $c['id'] ?>">
        <h3 class="mb-4 fw-bold" style="letter-spacing: -0.5px; border-left: 5px solid var(--red); padding-left: 15px;">
            <?= htmlspecialchars($c['name']) ?>
        </h3>
        
        <?php if (empty($prods)): ?>
            <p class="text-muted small ps-3">Chưa có món ăn nào trong danh mục này.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach($prods as $p): ?>
                <div class="col">
                    <div class="product-card">
                        <div>
                            <div class="product-image-container">
                                <?php if ($p['price_discount'] > 0 && $p['price_discount'] < $p['price']): ?>
                                    <div class="position-absolute bg-danger text-white px-2 py-1 fw-bold" style="top: 8px; right: 8px; border-radius: 6px; font-size: 0.75rem; z-index: 2; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3); font-family: 'Nunito', sans-serif;">
                                        -<?= round((($p['price'] - $p['price_discount']) / $p['price']) * 100) ?>%
                                    </div>
                                <?php endif; ?>
                                <?php if (strpos($p['image_url'] ?? '', 'uploads/') === 0 || strpos($p['image_url'] ?? '', 'http://') === 0 || strpos($p['image_url'] ?? '', 'https://') === 0): ?>
                                    <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name'] ?? 'Món ăn') ?>" class="zoomable-img" style="cursor: zoom-in;">
                                <?php else: ?>
                                    <div class="product-emoji-container">
                                        <?= htmlspecialchars($p['image_url'] ?? '🍔') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold mt-2" style="font-size: 1.05rem; line-height: 1.4; color: var(--dark);"><?= htmlspecialchars($p['name'] ?? 'Món ăn') ?></h5>
                            <p class="text-muted small mb-3" style="font-size: 0.85rem; line-height: 1.5;"><?= htmlspecialchars($p['description'] ?? '') ?></p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="border-top-style: dashed !important;">
                            <div class="d-flex flex-column align-items-start">
                                <?php if ($p['price_discount'] > 0 && $p['price_discount'] < $p['price']): ?>
                                    <div class="d-flex align-items-baseline gap-1 flex-wrap">
                                        <span class="text-danger fw-bold fs-5" style="font-family: 'Nunito', sans-serif;"><?= number_format($p['price_discount'], 0, ',', '.') ?>đ</span>
                                        <span class="text-muted text-decoration-line-through fw-semibold" style="font-size: 0.75rem; font-family: 'Nunito', sans-serif; opacity: 0.7;"><?= number_format($p['price'], 0, ',', '.') ?>đ</span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-danger fw-bold fs-5" style="font-family: 'Nunito', sans-serif;"><?= number_format($p['price'] ?? 0, 0, ',', '.') ?>đ</span>
                                <?php endif; ?>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn-jollibee btn-sm px-3 py-2" style="border-radius: 6px; font-size: 0.85rem;">+ Thêm</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- GIỎ HÀNG -->
<div class="container pb-5" id="cart">
    <h3 class="fw-bold mb-4" style="border-left: 5px solid var(--red); padding-left: 15px;">🛒 Giỏ hàng của bạn</h3>
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="card border-0 p-5 text-center shadow-sm" style="border-radius: 12px; background: var(--white);">
            <div style="font-size: 4rem;" class="mb-3">🛒</div>
            <p class="text-muted mb-0 fw-medium">Giỏ hàng trống. Hãy chọn những món ăn ngon phía trên!</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border-0 p-4 shadow-sm" style="border-radius: 12px; background: var(--white);">
                    <?php
                    $cart_total = 0;
                    foreach($_SESSION['cart'] as $p_id => $qty):
                        $st = $conn->prepare("SELECT * FROM products WHERE id = ?"); 
                        $st->bind_param("i", $p_id); 
                        $st->execute(); 
                        $p = $st->get_result()->fetch_assoc(); 
                        if(!$p) continue;
                        $actual_price = ($p['price_discount'] > 0 && $p['price_discount'] < $p['price']) ? $p['price_discount'] : $p['price'];
                        $item_total = $actual_price * $qty; 
                        $cart_total += $item_total;
                    ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3" style="border-bottom-color: #F1F5F9 !important;">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Ảnh nhỏ sản phẩm trong giỏ hàng -->
                            <div style="width: 55px; height: 55px; border-radius: 8px; overflow: hidden; background-color: #F8FAFC; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <?php if (strpos($p['image_url'] ?? '', 'uploads/') === 0 || strpos($p['image_url'] ?? '', 'http://') === 0 || strpos($p['image_url'] ?? '', 'https://') === 0): ?>
                                    <img src="<?= htmlspecialchars($p['image_url']) ?>" class="zoomable-img" style="width: 100%; height: 100%; object-fit: cover; cursor: zoom-in;" alt="<?= htmlspecialchars($p['name']) ?>">
                                <?php else: ?>
                                    <span style="font-size: 1.6rem;"><?= htmlspecialchars($p['image_url'] ?? '🍔') ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <span class="fs-6 fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></span>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <span class="text-muted small"><?= number_format($actual_price, 0, ',', '.') ?>đ ×</span>
                                    <form method="POST" class="m-0 d-inline-flex align-items-center gap-1 border rounded px-1" style="background-color: #F8FAFC;">
                                        <input type="hidden" name="action" value="update_qty">
                                        <input type="hidden" name="product_id" value="<?= $p_id ?>">
                                        <button type="submit" name="quantity" value="<?= $qty - 1 ?>" class="btn btn-link text-secondary p-0 px-2 fw-bold text-decoration-none" style="font-size: 1rem; line-height: 1;">-</button>
                                        <span class="px-1 fw-bold text-dark" style="font-size: 0.9rem;"><?= $qty ?></span>
                                        <button type="submit" name="quantity" value="<?= $qty + 1 ?>" class="btn btn-link text-secondary p-0 px-2 fw-bold text-decoration-none" style="font-size: 1rem; line-height: 1;">+</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <span class="text-danger fw-bold fs-6" style="font-family: 'Nunito', sans-serif;"><?= number_format($item_total, 0, ',', '.') ?>đ</span>
                            <form method="POST" class="m-0">
                                <input type="hidden" name="action" value="remove_item">
                                <input type="hidden" name="product_id" value="<?= $p_id ?>">
                                <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" style="font-size: 1.1rem;">✕</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <form method="POST" class="m-0 confirm-form" data-confirm-text="Bạn có chắc chắn muốn xóa sạch giỏ hàng này không?">
                            <input type="hidden" name="action" value="clear_cart">
                            <button type="submit" class="btn btn-outline-secondary btn-sm" style="border-radius: 6px; font-weight: 600;">Xóa giỏ hàng</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 p-4 shadow-sm" style="border-radius: 12px; background: var(--white); border-top: 5px solid var(--red) !important;">
                    <h4 class="mb-4 fw-bold text-dark" style="font-size: 1.2rem;">Tóm tắt hóa đơn</h4>
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Tạm tính:</span>
                        <strong class="text-dark"><?= number_format($cart_total,0,',','.') ?>đ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Phí giao hàng:</span>
                        <strong class="text-dark"><?= $cart_total >= 150000 ? 'Miễn phí' : '20.000đ' ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-4 fs-5 fw-bold border-top pt-3 text-danger" style="border-top-style: dashed !important; font-family: 'Nunito', sans-serif;">
                        <span>Tổng thanh toán:</span>
                        <span><?= number_format($cart_total >= 150000 ? $cart_total : $cart_total+20000, 0,',','.') ?>đ</span>
                    </div>
                    <a href="index.php?page=checkout" class="btn btn-jollibee w-100 py-2.5" style="border-radius: 8px; font-size: 0.95rem;">Tiến Hành Thanh Toán</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function scrollToCategory(catId) {
    const section = document.getElementById('category-sec-' + catId);
    if (section) {
        // Scroll to element with offset for sticky navbar + category nav
        const yOffset = -140; 
        const y = section.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({top: y, behavior: 'smooth'});
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const restoreScroll = () => {
        const scrollPosition = localStorage.getItem('homeScrollPosition');
        if (scrollPosition !== null) {
            window.scrollTo(0, parseInt(scrollPosition));
        }
    };

    // Khôi phục ngay khi DOM sẵn sàng
    restoreScroll();

    // Khôi phục lại lần nữa khi toàn bộ tài nguyên (ảnh, CSS) load xong để đảm bảo chính xác chiều cao trang
    window.addEventListener('load', () => {
        restoreScroll();
        localStorage.removeItem('homeScrollPosition');
    });

    // Xử lý gửi giỏ hàng bằng AJAX (Fetch) để cập nhật phần tử giỏ hàng mà không cần reload trang, tránh nhấp nháy giao diện
    document.addEventListener('submit', (e) => {
        const form = e.target;
        
        // Nếu form yêu cầu xác nhận và chưa được nhấn "Có" trên SweetAlert, KHÔNG chạy gửi AJAX ngay
        if (form.classList.contains('confirm-form') && form.dataset.confirmed !== 'true') {
            return;
        }

        const actionInput = form.querySelector('input[name="action"]');
        if (actionInput && ['add_to_cart', 'update_qty', 'remove_item', 'clear_cart'].includes(actionInput.value)) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitter = e.submitter;
            if (submitter && submitter.name) {
                formData.append(submitter.name, submitter.value);
            }
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(() => {
                // Tải ngầm HTML mới từ trang hiện tại sau khi đã cập nhật session
                return fetch(window.location.href);
            }).then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Thay đổi nội dung của thẻ #cart bằng HTML giỏ hàng mới
                const newCart = doc.getElementById('cart');
                const currentCart = document.getElementById('cart');
                if (newCart && currentCart) {
                    currentCart.innerHTML = newCart.innerHTML;
                }
                
                // Hiển thị toast thông báo nếu có
                const newToast = doc.querySelector('.toast-jollibee');
                if (newToast) {
                    const oldToast = document.querySelector('.toast-jollibee');
                    if (oldToast) oldToast.remove();
                    
                    document.body.appendChild(newToast);
                    newToast.style.display = 'block';
                    setTimeout(() => {
                        newToast.style.opacity = '0';
                        setTimeout(() => newToast.remove(), 500);
                    }, 2500);
                }
            }).catch(err => {
                console.error("Lỗi cập nhật giỏ hàng: ", err);
                // Nếu có lỗi mạng thì fallback reload trang
                window.location.reload();
            });
        }
    });

    const sections = document.querySelectorAll('.category-section');
    const navButtons = document.querySelectorAll('.cat-pill');

    const observerOptions = {
        root: null,
        rootMargin: '-150px 0px -60% 0px',
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const catId = entry.target.getAttribute('data-cat-id');
                navButtons.forEach(btn => {
                    if (btn.getAttribute('data-cat-id') === catId) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }
        });
    }, observerOptions);

    sections.forEach(section => observer.observe(section));
});
</script>
