<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
    <script>
        function filterCat(catId) {
            const cards = document.querySelectorAll('.product-item');
            cards.forEach(card => {
                if (catId === 'all' || card.getAttribute('data-cat') == catId) { card.style.display = 'block'; }
                else { card.style.display = 'none'; }
            });
        }
        setTimeout(() => { const t = document.querySelector('.toast-jollibee'); if(t) t.style.display='none'; }, 3000);
        
        if(window.location.hash === '#menu') {
            setTimeout(() => {
                const menuSection = document.getElementById('menu');
                if(menuSection) menuSection.scrollIntoView({ behavior: 'smooth' });
            }, 300);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Lắng nghe sự kiện submit của Form xác nhận (.confirm-form)
            document.body.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.classList.contains('confirm-form')) {
                    // Nếu đã nhấn "Có" rồi thì cho phép gửi đi
                    if (form.dataset.confirmed === 'true') {
                        return;
                    }
                    
                    const text = form.getAttribute('data-confirm-text') || 'Bạn có chắc chắn muốn thực hiện hành động này?';
                    
                    if (typeof Swal !== 'undefined') {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Xác nhận',
                            text: text,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Có',
                            cancelButtonText: 'Không',
                            customClass: {
                                confirmButton: 'btn btn-danger px-4 py-2 mx-1 fw-bold fs-7',
                                cancelButton: 'btn btn-secondary px-4 py-2 mx-1 fw-bold fs-7'
                            },
                            buttonsStyling: false,
                            focusCancel: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.dataset.confirmed = 'true';
                                if (typeof form.requestSubmit === 'function') {
                                    form.requestSubmit();
                                } else {
                                    form.submit();
                                }
                            }
                        });
                    } else {
                        // Fallback sang confirm mặc định nếu không tải được thư viện
                        if (!confirm(text)) {
                            e.preventDefault();
                        }
                    }
                }
            });

            // 2. Lắng nghe sự kiện click của Liên kết xác nhận (.confirm-link)
            document.body.addEventListener('click', function(e) {
                const link = e.target.closest('.confirm-link');
                if (link) {
                    const url = link.getAttribute('href');
                    const text = link.getAttribute('data-confirm-text') || 'Bạn có chắc chắn muốn thực hiện?';
                    
                    if (typeof Swal !== 'undefined') {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Xác nhận',
                            text: text,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Có',
                            cancelButtonText: 'Không',
                            customClass: {
                                confirmButton: 'btn btn-danger px-4 py-2 mx-1 fw-bold fs-7',
                                cancelButton: 'btn btn-secondary px-4 py-2 mx-1 fw-bold fs-7'
                            },
                            buttonsStyling: false,
                            focusCancel: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = url;
                            }
                        });
                    } else {
                        // Fallback sang confirm mặc định nếu không tải được thư viện
                        if (!confirm(text)) {
                            e.preventDefault();
                        }
                    }
                }
            });

            // 3. Lắng nghe sự kiện click phóng to ảnh (.zoomable-img)
            document.body.addEventListener('click', function(e) {
                const img = e.target.closest('.zoomable-img');
                if (img) {
                    // Tạo container lightbox
                    const lightbox = document.createElement('div');
                    lightbox.id = 'img-lightbox';
                    lightbox.style.position = 'fixed';
                    lightbox.style.top = '0';
                    lightbox.style.left = '0';
                    lightbox.style.width = '100vw';
                    lightbox.style.height = '100vh';
                    lightbox.style.backgroundColor = 'rgba(15, 23, 42, 0.9)'; // Dark slate
                    lightbox.style.backdropFilter = 'blur(10px)';
                    lightbox.style.display = 'flex';
                    lightbox.style.alignItems = 'center';
                    lightbox.style.justifyContent = 'center';
                    lightbox.style.zIndex = '10000';
                    lightbox.style.opacity = '0';
                    lightbox.style.transition = 'opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1)';
                    lightbox.style.cursor = 'zoom-out';

                    // Tạo phần tử ảnh phóng to
                    const largeImg = document.createElement('img');
                    largeImg.src = img.src;
                    largeImg.style.maxHeight = '85vh';
                    largeImg.style.maxWidth = '90vw';
                    largeImg.style.objectFit = 'contain';
                    largeImg.style.borderRadius = '12px';
                    largeImg.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.5)';
                    largeImg.style.transform = 'scale(0.92)';
                    largeImg.style.transition = 'transform 0.25s cubic-bezier(0.4, 0, 0.2, 1)';

                    // Tạo nút đóng ✕
                    const closeBtn = document.createElement('span');
                    closeBtn.innerHTML = '✕';
                    closeBtn.style.position = 'absolute';
                    closeBtn.style.top = '20px';
                    closeBtn.style.right = '30px';
                    closeBtn.style.color = '#fff';
                    closeBtn.style.fontSize = '2.2rem';
                    closeBtn.style.fontWeight = 'bold';
                    closeBtn.style.cursor = 'pointer';
                    closeBtn.style.textShadow = '0 2px 4px rgba(0,0,0,0.5)';

                    lightbox.appendChild(largeImg);
                    lightbox.appendChild(closeBtn);
                    document.body.appendChild(lightbox);

                    // Kích hoạt animation
                    setTimeout(() => {
                        lightbox.style.opacity = '1';
                        largeImg.style.transform = 'scale(1)';
                    }, 10);

                    // Đóng lightbox khi click
                    lightbox.addEventListener('click', function() {
                        lightbox.style.opacity = '0';
                        largeImg.style.transform = 'scale(0.92)';
                        setTimeout(() => {
                            lightbox.remove();
                        }, 250);
                    });
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
