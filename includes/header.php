<?php
defined('SECURE_ACCESS') or die('Truy cập trực tiếp bị cấm!');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jollibee Vietnam Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Nunito:wght@600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { 
            --red: #D71B2A; 
            --red-dark: #B61320; 
            --gold: #FFC72C; 
            --gold-dark: #E5A81C; 
            --cream: #F8FAFC; 
            --white: #FFFFFF; 
            --dark: #1E293B; 
            --border: #E2E8F0;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }
        body { 
            font-family: 'Inter', 'Nunito', sans-serif; 
            color: var(--dark); 
            background: var(--cream); 
            line-height: 1.6; 
        }
        
        /* Navbar Styling */
        .navbar-jollibee { 
            position: sticky; 
            top: 0; 
            z-index: 100; 
            background: var(--red); 
            border-bottom: 3px solid var(--gold);
            height: 70px; 
            display: flex; 
            align-items: center; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .logo { 
            font-family: 'Fredoka One', cursive; 
            font-size: 1.7rem; 
            color: var(--white) !important; 
            text-decoration: none; 
            transition: transform 0.2s ease;
        }
        .logo:hover {
            transform: scale(1.03);
        }
        .logo-text { color: var(--gold); }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-links a { 
            color: #fff; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 0.95rem;
            padding: 8px 16px; 
            border-radius: 8px; 
            transition: all 0.2s ease;
        }
        .nav-links a:hover { 
            background: rgba(255, 255, 255, 0.15); 
            color: #fff; 
        }
        
        /* Premium Buttons */
        .btn-jollibee { 
            background: var(--red); 
            color: #fff; 
            border-radius: 8px; 
            font-weight: 700; 
            padding: 10px 24px;
            border: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-jollibee:hover { 
            background: var(--red-dark); 
            color: #fff; 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(215, 27, 42, 0.2);
        }
        .btn-jollibee:active {
            transform: translateY(0);
        }
        .btn-outline-jollibee {
            background: transparent;
            color: var(--red);
            border: 2px solid var(--red);
            border-radius: 8px;
            font-weight: 700;
            padding: 8px 20px;
            transition: all 0.2s ease;
        }
        .btn-outline-jollibee:hover {
            background: var(--red);
            color: #fff;
        }

        /* Product Cards */
        .product-card { 
            background: var(--white); 
            border-radius: 12px; 
            border: 1px solid var(--border);
            padding: 24px; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            height: 100%; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(215, 27, 42, 0.25);
        }
        .product-image-container {
            width: 100%;
            height: 160px;
            background-color: #F8FAFC;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 16px;
            border: 1px solid var(--border);
            position: relative;
        }
        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-emoji-container {
            font-size: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #FFF1F2;
            width: 100%;
            height: 100%;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-card:hover .product-image-container img {
            transform: scale(1.05);
        }
        .product-card:hover .product-emoji-container {
            transform: scale(1.05) rotate(5deg);
        }

        /* Premium Forms */
        .form-control, .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: var(--white);
            color: var(--dark);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(215, 27, 42, 0.15);
            color: var(--dark);
        }

        /* Toast Alert */
        .toast-jollibee { 
            position: fixed; 
            bottom: 24px; 
            left: 24px; 
            background: #0F172A; 
            color: #fff; 
            padding: 16px 28px; 
            border-radius: 10px; 
            border-left: 5px solid var(--gold); 
            box-shadow: var(--shadow-lg);
            z-index: 999; 
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        /* Auth Card */
        .auth-card { 
            background: var(--white); 
            border-radius: 12px; 
            border: 1px solid var(--border);
            padding: 40px; 
            box-shadow: var(--shadow); 
            max-width: 480px; 
            margin: 0 auto; 
            border-top: 5px solid var(--red);
        }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

    <!-- NAV CHUNG -->
    <header class="navbar-jollibee no-print">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Trái: Logo thương hiệu -->
            <a href="index.php?page=home" class="logo">🐝 <span class="logo-text">Jollibee</span></a>
            
            <!-- Giữa: Menu điều hướng chính -->
            <nav class="nav-links d-none d-lg-flex">
                <a href="index.php?page=home">Trang Chủ</a>
                <a href="index.php?page=home#menu">Thực Đơn</a>
                <a href="index.php?page=news">Tin Tức</a>
                <?php if (isset($_SESSION['logged_in'])): ?>
                    <a href="index.php?page=my_orders" style="font-size: 0.95rem;">Đơn hàng của tôi</a>
                <?php endif; ?>
            </nav>
            
            <!-- Phải: Tài khoản & Quản trị -->
            <div class="nav-links">
                <?php if (isset($_SESSION['logged_in'])): ?>
                    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                        <div class="dropdown d-inline ms-2">
                            <button class="btn btn-sm dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: rgba(0,0,0,0.2); border: 1px solid var(--gold); border-radius: 20px; font-weight: 700; padding: 5px 15px; font-size: 0.9rem;">
                                ⚙️ Quản trị
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                                <li><a class="dropdown-item py-2 fw-bold text-dark" href="index.php?page=admin_dashboard"><i class="fa fa-list me-2 text-danger"></i>Quản lý danh mục</a></li>
                                <li><a class="dropdown-item py-2 fw-bold text-dark" href="index.php?page=admin_products"><i class="fa fa-hamburger me-2 text-danger"></i>Quản lý món ăn</a></li>
                                <li><a class="dropdown-item py-2 fw-bold text-dark" href="index.php?page=admin_orders"><i class="fa fa-shopping-bag me-2 text-danger"></i>Quản lý đơn hàng</a></li>
                                <li><a class="dropdown-item py-2 fw-bold text-dark" href="index.php?page=admin_promotions"><i class="fa fa-tag me-2 text-danger"></i>Quản lý khuyến mãi</a></li>
                                <li><a class="dropdown-item py-2 fw-bold text-dark" href="index.php?page=admin_news"><i class="fa fa-newspaper me-2 text-danger"></i>Quản lý tin tức</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <a href="index.php?page=profile" class="text-white fw-bold ms-3 text-decoration-none" style="font-size: 0.95rem;" title="Xem thông tin cá nhân">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
                    <a href="index.php?page=logout" style="background:rgba(0,0,0,0.25); padding: 6px 15px; border-radius: 20px; font-size: 0.9rem; margin-left: 10px;">Đăng xuất</a>
                <?php else: ?>
                    <a href="index.php?page=login" style="font-size: 0.95rem;">Đăng Nhập</a>
                    <a href="index.php?page=register" style="font-size: 0.95rem; background: var(--gold); color: #000; border-radius: 20px; padding: 6px 16px; margin-left: 10px;">Đăng Ký</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
