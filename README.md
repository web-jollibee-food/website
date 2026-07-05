# 🍗 Jollibee Food — Web Đặt Hàng Thức Ăn Online

> Dự án môn Lập Trình Web | Nhóm **web-jollibee-food**  
> Công nghệ: **PHP thuần + SQLite (tương thích MySQL) + Bootstrap 5**

---

## 📋 Mục lục

1. [Giới thiệu dự án](#1-giới-thiệu-dự-án)
2. [Tính năng](#2-tính-năng)
3. [Công nghệ sử dụng](#3-công-nghệ-sử-dụng)
4. [Cấu trúc thư mục](#4-cấu-trúc-thư-mục)
5. [Cơ sở dữ liệu](#5-cơ-sở-dữ-liệu)
6. [Hướng dẫn cài đặt](#6-hướng-dẫn-cài-đặt)
7. [Cấu hình môi trường (.env)](#7-cấu-hình-môi-trường-env)
8. [Tài khoản mặc định](#8-tài-khoản-mặc-định)
9. [Hướng dẫn sử dụng](#9-hướng-dẫn-sử-dụng)
10. [Thanh toán & Webhook](#10-thanh-toán--webhook)
11. [Gửi Email (SMTP / Log)](#11-gửi-email-smtp--log)
12. [Bảo mật](#12-bảo-mật)
13. [Phân công nhóm](#13-phân-công-nhóm)
14. [Hướng phát triển](#14-hướng-phát-triển)

---

## 1. Giới thiệu dự án

**Jollibee Food** là website đặt hàng thức ăn trực tuyến lấy cảm hứng từ chuỗi nhà hàng Jollibee. Người dùng có thể duyệt thực đơn, thêm món vào giỏ hàng, đặt hàng và thanh toán qua chuyển khoản ngân hàng (quét mã QR VietQR) hoặc tiền mặt COD. Hệ thống có trang quản trị dành cho admin để quản lý toàn bộ nội dung.

**Điểm đặc biệt kỹ thuật:** Dự án được viết với cú pháp MySQLi nhưng chạy trên **SQLite** (không cần cài MySQL Server) nhờ lớp wrapper tương thích `SQLiteMySQLiConnection` trong `config.php`. Điều này giúp chạy được ngay trên mọi máy có PHP mà không cần cấu hình thêm. Khi deploy lên server thật, chỉ cần đổi kết nối sang MySQL qua file `jollibee_food.sql` kèm theo.

---

## 2. Tính năng

### 👤 Phía người dùng (User)

| Tính năng           | Mô tả                                                       |
| ------------------- | ----------------------------------------------------------- |
| Trang chủ           | Hero banner, danh sách sản phẩm theo danh mục, lọc theo tab |
| Giỏ hàng            | Thêm/xoá/cập nhật số lượng, lưu trong Session               |
| Đặt hàng            | Form checkout điền thông tin, áp mã giảm giá, tính phí ship |
| Phí ship            | Miễn phí khi đơn hàng ≥ 150.000đ, phí 20.000đ nếu dưới      |
| Thanh toán QR       | Tự động tạo QR VietQR, đồng hồ đếm ngược 5 phút             |
| Thanh toán COD      | Thanh toán khi nhận hàng                                    |
| Lịch sử đơn hàng    | Xem danh sách và chi tiết đơn đã đặt                        |
| Đăng ký / Đăng nhập | Tạo tài khoản, đăng nhập, đăng xuất                         |
| Quên mật khẩu       | Gửi link reset qua email (SMTP hoặc lưu file log)           |
| Hồ sơ cá nhân       | Xem và cập nhật thông tin, đổi mật khẩu                     |
| Tin tức             | Đọc bài viết, tin khuyến mãi                                |
| Mã giảm giá         | Nhập code tại checkout để được giảm giá                     |

### 🔧 Phía quản trị (Admin)

| Tính năng           | Mô tả                                                                   |
| ------------------- | ----------------------------------------------------------------------- |
| Dashboard           | Thống kê tổng quan: doanh thu, đơn hàng, sản phẩm                       |
| Quản lý danh mục    | Thêm / sửa / xoá danh mục (CRUD)                                        |
| Quản lý sản phẩm    | Thêm / sửa / xoá sản phẩm, upload ảnh, giá gốc & giá khuyến mãi         |
| Quản lý đơn hàng    | Xem danh sách, chi tiết đơn, cập nhật trạng thái giao hàng & thanh toán |
| Quản lý mã giảm giá | Thêm / sửa / xoá mã (%, số tiền cố định), giới hạn số lần dùng          |
| Quản lý tin tức     | Thêm / sửa / xoá bài viết, upload ảnh                                   |

---

## 3. Công nghệ sử dụng

| Thành phần            | Công nghệ                                                           |
| --------------------- | ------------------------------------------------------------------- |
| Ngôn ngữ backend      | PHP 8.x (không dùng framework)                                      |
| Cơ sở dữ liệu runtime | **SQLite 3** (file `jollibee.db`, không cần cài MySQL)              |
| Schema tương thích    | MySQL 8 (`jollibee_food.sql` để deploy production)                  |
| Frontend              | Bootstrap 5, Font Awesome 6, Google Fonts (Fredoka One, Nunito)     |
| Thanh toán QR         | VietQR API (`img.vietqr.io`) — tạo QR ngân hàng                     |
| Webhook thanh toán    | SePay — tự động xác nhận khi nhận được chuyển khoản                 |
| Gửi email             | SMTP thuần (Gmail SSL port 465) hoặc ghi file log khi chưa cấu hình |
| Session               | PHP native Session (httponly, SameSite=Strict)                      |
| Bảo mật mật khẩu      | `password_hash()` bcrypt cost=12                                    |
| Upload ảnh            | PHP `move_uploaded_file()`, lưu vào `uploads/`                      |

---

## 4. Cấu trúc thư mục

```
jollibee/
│
├── index.php                  # Entry point duy nhất — router tập trung
├── config.php                 # Cấu hình DB, SMTP, wrapper SQLite↔MySQLi
├── import_db.php              # Script import dữ liệu mẫu vào SQLite
├── jollibee.db                # File cơ sở dữ liệu SQLite (runtime)
├── jollibee_food.sql          # Schema MySQL (dùng khi deploy production)
│
├── .env                       # Biến môi trường bí mật (KHÔNG commit git)
├── .env.example               # Mẫu cấu hình .env
├── .gitignore                 # Bỏ qua .env, jollibee.db, logs_emails/
│
├── includes/
│   ├── header.php             # Layout header, navbar, giỏ hàng mini
│   └── footer.php             # Layout footer, script JS
│
├── pages/                     # View — mỗi file là một trang
│   ├── home.php               # Trang chủ, thực đơn
│   ├── login.php              # Đăng nhập
│   ├── register.php           # Đăng ký
│   ├── forgot_password.php    # Quên mật khẩu
│   ├── reset_password.php     # Đặt lại mật khẩu (từ link email)
│   ├── profile.php            # Hồ sơ cá nhân
│   ├── checkout.php           # Trang đặt hàng & thanh toán
│   ├── qrcode.php             # Hiển thị QR chuyển khoản + đếm ngược
│   ├── check_payment_status.php # AJAX kiểm tra trạng thái thanh toán
│   ├── sepay_webhook.php      # Nhận webhook từ SePay xác nhận thanh toán
│   ├── success.php            # Trang đặt hàng thành công
│   ├── my_orders.php          # Lịch sử đơn hàng của user
│   ├── news.php               # Danh sách tin tức
│   ├── news_detail.php        # Chi tiết bài viết
│   │
│   ├── admin_dashboard.php    # Dashboard quản trị
│   ├── category_add.php       # Thêm danh mục
│   ├── category_edit.php      # Sửa danh mục
│   ├── admin_products.php     # Danh sách sản phẩm
│   ├── admin_product_add.php  # Thêm sản phẩm
│   ├── admin_product_edit.php # Sửa sản phẩm
│   ├── admin_orders.php       # Danh sách đơn hàng
│   ├── admin_order_detail.php # Chi tiết đơn hàng
│   ├── admin_promotions.php   # Danh sách mã giảm giá
│   ├── admin_promotion_add.php# Thêm mã giảm giá
│   └── admin_promotion_edit.php# Sửa mã giảm giá
│   ├── admin_news.php         # Quản lý tin tức
│   ├── admin_news_add.php     # Thêm bài viết
│   └── admin_news_edit.php    # Sửa bài viết
│
├── process/
│   ├── actions.php            # Xử lý toàn bộ POST request (controller)
│   ├── db_migration.php       # Migration: tạo bảng mới, thêm cột
│   └── send_mail_bg.php       # Gửi mail nền (background)
│
├── uploads/                   # Ảnh sản phẩm & tin tức do admin upload
└── logs_emails/               # Email log khi chưa cấu hình SMTP thật
```

---

## 5. Cơ sở dữ liệu

Dự án dùng **SQLite** khi chạy local (file `jollibee.db`) và cung cấp file `jollibee_food.sql` để import vào MySQL khi deploy production.

### Sơ đồ các bảng

```
roles ──────────< users >──────────< orders >──────< order_items
                                                          │
                                                       products
                                                          │
categories ───────────────────────────────────────────────┘

promotions   (độc lập, liên kết qua mã code trong orders)
news         (độc lập)
```

### Chi tiết bảng

#### `roles` — Vai trò

| Cột  | Kiểu        | Mô tả               |
| ---- | ----------- | ------------------- |
| id   | INTEGER PK  |                     |
| name | TEXT UNIQUE | `admin` hoặc `user` |

#### `users` — Người dùng

| Cột           | Kiểu          | Mô tả                  |
| ------------- | ------------- | ---------------------- |
| id            | INTEGER PK    |                        |
| role_id       | INTEGER FK    | Liên kết `roles.id`    |
| full_name     | TEXT NOT NULL | Họ tên                 |
| email         | TEXT UNIQUE   | Email đăng nhập        |
| phone         | TEXT          | Số điện thoại          |
| password_hash | TEXT          | bcrypt hash (cost=12)  |
| reset_token   | TEXT          | Token đặt lại mật khẩu |
| reset_expires | TEXT          | Hết hạn token          |
| address       | TEXT          | Địa chỉ mặc định       |

#### `categories` — Danh mục thực đơn

| Cột  | Kiểu        | Mô tả        |
| ---- | ----------- | ------------ |
| id   | INTEGER PK  |              |
| name | TEXT UNIQUE | Tên danh mục |

> Dữ liệu mẫu: Gà Giòn Vui Vẻ, Mì Ý Jolly, Burger & Sandwich, Món Đi Kèm, Nước Uống & Tráng Miệng

#### `products` — Sản phẩm

| Cột            | Kiểu       | Mô tả                           |
| -------------- | ---------- | ------------------------------- |
| id             | INTEGER PK |                                 |
| category_id    | INTEGER FK | Liên kết `categories.id`        |
| name           | TEXT       | Tên sản phẩm                    |
| description    | TEXT       | Mô tả                           |
| price          | REAL       | Giá gốc                         |
| price_discount | DECIMAL    | Giá khuyến mãi (0 = không giảm) |
| image_url      | TEXT       | Đường dẫn ảnh                   |

#### `orders` — Đơn hàng

| Cột            | Kiểu       | Mô tả                                                           |
| -------------- | ---------- | --------------------------------------------------------------- |
| id             | INTEGER PK |                                                                 |
| user_id        | INTEGER FK | `users.id` (NULL = khách vãng lai)                              |
| ho_ten         | TEXT       | Tên người nhận                                                  |
| so_dien_thoai  | TEXT       | SĐT giao hàng                                                   |
| dia_chi        | TEXT       | Địa chỉ giao                                                    |
| tong_tien      | REAL       | Tổng tiền sau giảm giá + phí ship                               |
| phuong_thuc_tt | TEXT       | `COD` hoặc `bank_transfer`                                      |
| trang_thai     | TEXT       | Trạng thái đơn: Mới / Đang xử lý / Đang giao / Hoàn thành / Huỷ |
| trang_thai_tt  | TEXT       | Trạng thái thanh toán: `unpaid` / `paid`                        |
| trang_thai_gh  | TEXT       | Trạng thái giao hàng                                            |
| ma_giam_gia    | TEXT       | Mã promo đã dùng                                                |
| so_tien_giam   | REAL       | Số tiền đã giảm                                                 |
| ghi_chu        | TEXT       | Ghi chú của khách                                               |
| ngay_dat       | TIMESTAMP  | Thời điểm đặt hàng                                              |

#### `order_items` — Chi tiết đơn hàng

| Cột        | Kiểu       | Mô tả                                     |
| ---------- | ---------- | ----------------------------------------- |
| id         | INTEGER PK |                                           |
| order_id   | INTEGER FK | Liên kết `orders.id`                      |
| product_id | INTEGER FK | Liên kết `products.id`                    |
| ten_sp     | TEXT       | Tên sản phẩm (snapshot tại thời điểm đặt) |
| gia_ban    | REAL       | Giá tại thời điểm đặt                     |
| so_luong   | INTEGER    | Số lượng                                  |
| thanh_tien | REAL       | Thành tiền (gia_ban × so_luong)           |

#### `promotions` — Mã giảm giá

| Cột            | Kiểu        | Mô tả                                          |
| -------------- | ----------- | ---------------------------------------------- |
| id             | INTEGER PK  |                                                |
| code           | TEXT UNIQUE | Mã nhập (vd: `JOLLIBEE10`)                     |
| discount_value | REAL        | Giá trị giảm                                   |
| discount_type  | TEXT        | `percentage` (%) hoặc `flat` (số tiền cố định) |
| expiry_date    | TEXT        | Ngày hết hạn `YYYY-MM-DD`                      |
| status         | INTEGER     | `1` = kích hoạt, `0` = tắt                     |
| max_uses       | INTEGER     | Số lần dùng tối đa (NULL = không giới hạn)     |
| used_count     | INTEGER     | Số lần đã dùng                                 |

> Dữ liệu mẫu: `JOLLIBEE10` (giảm 10%), `FREESHIP` (giảm 20.000đ)

#### `news` — Tin tức / Bài viết

| Cột        | Kiểu       | Mô tả                             |
| ---------- | ---------- | --------------------------------- |
| id         | INTEGER PK |                                   |
| title      | TEXT       | Tiêu đề                           |
| summary    | TEXT       | Tóm tắt (hiển thị trên danh sách) |
| content    | TEXT       | Nội dung đầy đủ                   |
| image_url  | TEXT       | Ảnh bìa                           |
| created_at | TIMESTAMP  | Ngày đăng                         |

---

## 6. Hướng dẫn cài đặt

### Yêu cầu hệ thống

- **PHP** >= 8.0 với các extension: `pdo`, `pdo_sqlite`, `openssl`, `fileinfo`, `gd`
- **Web server**: Apache (XAMPP/WAMP/Laragon) hoặc PHP built-in server
- **KHÔNG cần** cài MySQL hay bất kỳ database server nào

### Cài đặt nhanh (Local)

**Bước 1:** Tải và giải nén dự án vào thư mục web server

```bash
# XAMPP: đặt vào C:\xampp\htdocs\jollibee
# WAMP:  đặt vào C:\wamp64\www\jollibee
# Laragon: đặt vào C:\laragon\www\jollibee
```

**Bước 2:** Sao chép file cấu hình môi trường

```bash
cp .env.example .env
```

**Bước 3:** Khởi tạo database (nếu `jollibee.db` chưa có dữ liệu)

Truy cập trình duyệt:

```
http://localhost/jollibee/import_db.php
```

> File này sẽ tạo toàn bộ bảng và chèn dữ liệu mẫu (sản phẩm, danh mục, tài khoản admin).

**Bước 4:** Chạy migration để đảm bảo schema đầy đủ

```
http://localhost/jollibee/process/db_migration.php
```

**Bước 5:** Truy cập website

```
http://localhost/jollibee/
```

### Chạy bằng PHP built-in server (không cần XAMPP)

```bash
cd jollibee
php -S localhost:8000
```

Rồi mở: `http://localhost:8000`

---

## 7. Cấu hình môi trường (.env)

Sao chép `.env.example` thành `.env` rồi điền thông tin thật:

```env
# ── SMTP - Gửi email (Gmail) ──────────────────────────────
SMTP_HOST=smtp.gmail.com
SMTP_PORT=465
SMTP_USER=your_email@gmail.com        # Gmail của bạn
SMTP_PASS=your_16_char_app_password   # App Password (không phải mật khẩu Gmail)

# ── Thông tin tài khoản ngân hàng (hiển thị QR VietQR) ────
BANK_ID=MB                            # Mã ngân hàng (MB, VCB, TCB, ACB...)
BANK_ACCOUNT=0123456789               # Số tài khoản
BANK_ACC_NAME=NGUYEN VAN A            # Tên chủ tài khoản (IN HOA)

# ── SePay Webhook (tự động xác nhận thanh toán) ───────────
SEPAY_API_KEY=your_sepay_api_key      # Lấy từ dashboard SePay
PAYMENT_BASE_URL=https://xxxx.ngrok-free.app  # URL công khai (dùng ngrok khi test)

# ── URL website (tuỳ chọn, để trống = tự nhận diện) ───────
PAYMENT_BASE_URL=
```

### Cách lấy Gmail App Password

1. Bật **2-Step Verification** trong tài khoản Google
2. Vào **Google Account** → **Security** → **App passwords**
3. Chọn app: `Mail`, device: `Other` → Đặt tên → **Generate**
4. Chép 16 ký tự vào `SMTP_PASS`

> **Lưu ý:** Nếu không cấu hình SMTP, hệ thống tự động lưu email vào thư mục `logs_emails/` để xem nội dung khi test.

---

## 8. Tài khoản mặc định

| Vai trò   | Email               | Mật khẩu   |
| --------- | ------------------- | ---------- |
| **Admin** | `admin@jollibee.vn` | `admin123` |
| **User**  | _(tự đăng ký)_      | _(tự đặt)_ |

> Có thể đổi mật khẩu admin sau khi đăng nhập vào trang hồ sơ.

---

## 9. Hướng dẫn sử dụng

### Luồng đặt hàng của khách hàng

```
Trang chủ
  → Xem thực đơn theo danh mục
  → Nhấn "Thêm vào giỏ" (không cần đăng nhập)
  → Nhấn nút giỏ hàng trên navbar
  → Nhấn "Đặt hàng" → Bắt buộc đăng nhập
  → Trang Checkout: điền thông tin, nhập mã giảm giá (tuỳ chọn)
  → Chọn phương thức: COD hoặc Chuyển khoản
  → Nếu chuyển khoản → trang QR → quét mã → tự động xác nhận
  → Trang thành công
  → Xem lại trong "Đơn hàng của tôi"
```

### Luồng quản trị (Admin)

```
Đăng nhập bằng tài khoản admin
  → Dashboard: xem thống kê
  → Quản lý danh mục: /index.php?page=admin_dashboard
  → Quản lý sản phẩm: /index.php?page=admin_products
  → Quản lý đơn hàng: /index.php?page=admin_orders
  → Cập nhật trạng thái đơn hàng trong trang chi tiết
  → Quản lý mã giảm giá: /index.php?page=admin_promotions
  → Quản lý tin tức: /index.php?page=admin_news
```

### URL các trang chính

| Trang               | URL                                    |
| ------------------- | -------------------------------------- |
| Trang chủ           | `index.php` hoặc `index.php?page=home` |
| Đăng nhập           | `index.php?page=login`                 |
| Đăng ký             | `index.php?page=register`              |
| Giỏ hàng / Checkout | `index.php?page=checkout`              |
| Lịch sử đơn hàng    | `index.php?page=my_orders`             |
| Hồ sơ cá nhân       | `index.php?page=profile`               |
| Tin tức             | `index.php?page=news`                  |
| Admin Dashboard     | `index.php?page=admin_dashboard`       |

---

## 10. Thanh toán & Webhook

### QR VietQR

Khi khách chọn thanh toán chuyển khoản, hệ thống tự động tạo mã QR dùng API công khai của VietQR:

```
https://img.vietqr.io/image/{BANK_ID}-{BANK_ACCOUNT}-compact.png
  ?amount={tong_tien}
  &addInfo=JB{order_id}       ← Nội dung chuyển khoản (để SePay nhận dạng)
  &accountName={BANK_ACC_NAME}
```

Đồng hồ đếm ngược **5 phút** hiển thị trên trang QR. Trang tự động kiểm tra trạng thái thanh toán mỗi 5 giây qua AJAX (`check_payment_status.php`).

### SePay Webhook (tự động xác nhận)

SePay là dịch vụ theo dõi tài khoản ngân hàng và gọi webhook khi nhận được tiền chuyển vào.

**Cách cấu hình:**

1. Đăng ký tại [SePay](https://sepay.vn) và kết nối tài khoản ngân hàng
2. Cấu hình Webhook URL trong SePay dashboard:
   ```
   https://your-domain.com/index.php?page=sepay_webhook
   ```
   > Khi test local, dùng **ngrok** để có URL công khai:
   >
   > ```bash
   > ngrok http 80
   > # Sau đó set PAYMENT_BASE_URL=https://xxxx.ngrok-free.app trong .env
   > ```
3. Đặt `SEPAY_API_KEY` vào `.env` để xác thực webhook

**Luồng webhook:**

```
Khách chuyển khoản nội dung "JB000042"
  → SePay nhận giao dịch
  → SePay POST tới sepay_webhook.php
  → Hệ thống regex tìm "JB(\d+)" → lấy order_id = 42
  → So khớp số tiền với tong_tien trong DB
  → Cập nhật trang_thai_tt = 'paid'
  → Trang QR của khách tự refresh → hiển thị "Thanh toán thành công"
```

---

## 11. Gửi Email (SMTP / Log)

Hệ thống gửi email trong chức năng **Quên mật khẩu**. Link reset có hiệu lực **1 giờ**.

**Khi đã cấu hình SMTP** (`SMTP_USER` khác `YOUR_EMAIL@gmail.com`):

- Kết nối trực tiếp Gmail qua SSL port 465
- Email HTML được gửi tới địa chỉ của người dùng

**Khi chưa cấu hình SMTP** (chế độ demo):

- Email được lưu thành file `.txt` tại `logs_emails/`
- Tên file: `email_to_{email}_{timestamp}.txt`
- Mở file để lấy link reset password khi test

---

## 12. Bảo mật

| Lỗ hổng            | Biện pháp                                                                            |
| ------------------ | ------------------------------------------------------------------------------------ |
| SQL Injection      | Toàn bộ query dùng Prepared Statement (PDO `?` binding)                              |
| XSS                | `htmlspecialchars()` trên mọi output từ DB ra HTML                                   |
| Direct file access | Hằng `SECURE_ACCESS` kiểm tra ở đầu mỗi page trong `pages/`                          |
| Session Hijacking  | `session_regenerate_id(true)` sau đăng nhập, cookie httponly + SameSite=Strict       |
| Password leak      | `password_hash()` bcrypt cost=12, không lưu plaintext                                |
| Webhook giả mạo    | Kiểm tra API Key trong header Authorization của SePay                                |
| Unauthorized admin | `checkAdminOrDie()` kiểm tra `$_SESSION['user_role'] === 'admin'` ở mọi action admin |
| File upload        | Kiểm tra `getimagesize()` xác nhận là ảnh thật trước khi lưu                         |

---

## 13. Phân công nhóm

| Thành viên   | Vai trò  | Nội dung phụ trách                                                                                 |
| ------------ | -------- | -------------------------------------------------------------------------------------------------- |
| **Huynh** ⭐ | Leader   | Thiết kế database, hệ thống đăng nhập/đăng ký/đăng xuất, phân quyền admin/user, session, UML & ERD |
| **Hiếu**     | UI User  | Giao diện trang khách hàng, responsive, ERD & UML user, báo cáo phần user                          |
| **Đức**      | UI Admin | Giao diện trang admin, CRUD đầy đủ các chức năng, ERD & UML admin                                  |
| **Tín**      | Mua hàng | Giao diện đặt hàng & thanh toán, ERD & UML mua hàng, báo cáo mua hàng                              |
| **Sáng**     | Báo cáo  | Phân tích hệ thống (chức năng & phi chức năng), gộp báo cáo, UML tổng hợp, kết luận                |
| **Thiện**    | Deploy   | Ghép code, test & fix bug, deploy lên hosting                                                      |

---

## 14. Hướng phát triển

- [ ] Tích hợp thanh toán VNPay / MoMo (sandbox đã cấu hình sẵn trong `.env.example`)
- [ ] Chuyển từ SQLite sang MySQL/MariaDB khi deploy production (dùng `jollibee_food.sql`)
- [ ] Thêm chức năng đánh giá sản phẩm (rating & review)
- [ ] Thêm thông báo real-time cho admin khi có đơn mới (WebSocket hoặc SSE)
- [ ] Tối ưu ảnh upload (resize tự động, WebP)
- [ ] Thêm tính năng tìm kiếm sản phẩm
- [ ] Hỗ trợ đa ngôn ngữ (Tiếng Anh)
- [ ] Viết Unit Test cho các hàm xử lý đơn hàng

---

## Ghi chú kỹ thuật

**Tại sao dùng SQLite thay vì MySQL khi chạy local?**

Dự án dùng lớp wrapper `SQLiteMySQLiConnection` trong `config.php` để giả lập API của MySQLi nhưng thực chất chạy trên PDO + SQLite. Điều này cho phép:

- Chạy ngay không cần cài MySQL Server
- Dễ dàng share dự án (chỉ cần copy thư mục, không cần export/import DB)
- Code logic (`actions.php`, các trang `pages/`) giữ nguyên cú pháp MySQLi quen thuộc

Để chuyển sang MySQL production: import `jollibee_food.sql` vào MySQL server, sau đó thay `$conn = new SQLiteMySQLiConnection(...)` bằng `$conn = new mysqli(...)` trong `config.php`.

---

Link demo: http://g5jollibee.infinityfreeapp.com.

# Hướng dẫn cài đặt và khởi chạy dự án Website Jollibee

Dự án này được thiết kế để có thể khởi chạy linh hoạt tùy theo môi trường trên máy tính của bạn. Dưới đây là thông tin tài khoản đăng nhập quản trị mặc định và hướng dẫn chi tiết dành cho các trường hợp khởi chạy.

### Thông tin tài khoản quản trị mặc định

Bạn có thể đăng nhập vào trang quản lý của Admin bằng tài khoản mặc định dưới đây để kiểm tra các tính năng quản trị:

- **Địa chỉ Email:** admin@jollibee.vn
- **Mật khẩu đăng nhập:** Admin@123

---

## Trường hợp 1: Chạy dự án trực tiếp bằng câu lệnh PHP (Dành cho máy không cài XAMPP, hoặc máy CÓ cài XAMPP nhưng muốn chạy nhanh không qua Apache)

Cách này cho phép bạn khởi chạy website ngay tại thư mục dự án mà không cần phải di chuyển mã nguồn vào thư mục htdocs của XAMPP hay cấu hình cơ sở dữ liệu MySQL phức tạp. Hệ thống sẽ tự động kết nối vào cơ sở dữ liệu SQLite có sẵn là `jollibee.db`.

Các bước thực hiện như sau:

1. Mở chương trình Command Prompt trên Windows hoặc Terminal trên macOS và Linux.
2. Sử dụng lệnh di chuyển thư mục `cd` để đi vào thư mục gốc chứa mã nguồn của dự án này.
3. Gõ câu lệnh để kích hoạt máy chủ ảo tùy thuộc vào hệ thống của bạn:
   - **Nếu hệ thống đã cài đặt PHP độc lập:**
     ```bash
     php -S localhost:8000
     ```
   - **Nếu máy có cài đặt XAMPP nhưng muốn chạy nhanh bằng lệnh (không cần copy vào htdocs):**
     Sử dụng trực tiếp đường dẫn bộ dịch PHP có sẵn trong thư mục XAMPP của bạn bằng câu lệnh sau:
     ```bash
     C:\xampp\php\php -S localhost:8000
     ```
4. Mở trình duyệt web và truy cập vào địa chỉ sau để trải nghiệm trang web:
   ```text
   http://localhost:8000
   ```

---

## Trường hợp 2: Chạy dự án thông qua máy chủ web của XAMPP (Dành cho máy có cài đặt XAMPP)

Nếu bạn muốn chạy dự án theo quy trình tiêu chuẩn của phần mềm XAMPP, bạn có thể cấu hình chạy theo hai cách dưới đây.

### Cách 2.1: Chạy bằng cơ sở dữ liệu mặc định SQLite để khởi động nhanh

Cách này giúp bạn kiểm tra nhanh giao diện và tính năng bằng Apache của XAMPP mà không cần bật MySQL.

1. Sao chép toàn bộ thư mục dự án jollibee vào thư mục htdocs của XAMPP tại đường dẫn mặc định là: `C:\xampp\htdocs\jollibee`.
2. Khởi động phần mềm XAMPP Control Panel và nhấp vào nút Start ở dòng Apache. Bạn không cần bật dịch vụ MySQL.
3. Mở trình duyệt và truy cập vào đường dẫn:
   ```text
   http://localhost/jollibee
   ```

### Cách 2.2: Chạy bằng cơ sở dữ liệu MySQL chuyên nghiệp

Cách này giúp dự án kết nối và lưu trữ dữ liệu trên máy chủ MySQL thật của XAMPP.

1. Sao chép toàn bộ thư mục dự án jollibee vào thư mục htdocs của XAMPP tại đường dẫn mặc định là: `C:\xampp\htdocs\jollibee`.
2. Khởi động phần mềm XAMPP Control Panel và nhấp vào nút Start ở cả hai dòng Apache và MySQL.
3. Truy cập vào trang quản trị cơ sở dữ liệu bằng đường dẫn: `http://localhost/phpmyadmin`.
4. Nhấp chọn mục Mới ở danh sách bên trái để tạo cơ sở dữ liệu mới. Điền tên cơ sở dữ liệu là `jollibee_food` và chọn bảng mã Collation bên cạnh là `utf8mb4_unicode_ci` rồi nhấn nút Tạo.
5. Nhấp chọn cơ sở dữ liệu `jollibee_food` vừa tạo ở danh sách bên trái, sau đó chọn tab Nhập ở thanh công cụ phía bên phải.
6. Nhấp vào nút Chọn tệp và tìm đến file `jollibee_food.sql` nằm ở thư mục gốc của dự án jollibee rồi nhấn nút Nhập ở dưới cùng trang web để tiến hành nạp cơ sở dữ liệu.
7. Mở file có tên `.env` trong thư mục dự án của bạn và chỉnh sửa cấu hình kết nối từ SQLite sang MySQL theo các thông số dưới đây:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jollibee_food
DB_USERNAME=root
DB_PASSWORD=
```

8. Mở trình duyệt và truy cập vào đường dẫn sau để chạy ứng dụng:
   ```text
   http://localhost/jollibee
   ```

Link Github:

_Made with ❤️ by nhóm web-jollibee-food_
