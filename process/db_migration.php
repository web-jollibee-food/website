<?php
// process/db_migration.php
define('SECURE_ACCESS', true);
require_once __DIR__ . '/../config.php';

try {
    $db = db();
    
    // 1. Tạo bảng promotions
    $db->exec("CREATE TABLE IF NOT EXISTS promotions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT NOT NULL UNIQUE,
        discount_value REAL NOT NULL,
        discount_type TEXT NOT NULL, -- 'percentage' hoặc 'flat'
        expiry_date TEXT NOT NULL,
        status INTEGER DEFAULT 1
    );");
    echo "Bảng promotions đã sẵn sàng!<br>";

    // Helper kiểm tra cột
    function column_exists($db, $table, $column) {
        $st = $db->query("PRAGMA table_info($table)");
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            if ($row['name'] === $column) {
                return true;
            }
        }
        return false;
    }

    // 2. Thêm cột ma_giam_gia và so_tien_giam vào bảng orders
    if (!column_exists($db, 'orders', 'ma_giam_gia')) {
        $db->exec("ALTER TABLE orders ADD COLUMN ma_giam_gia TEXT;");
        echo "Thêm cột ma_giam_gia vào orders thành công!<br>";
    }
    if (!column_exists($db, 'orders', 'so_tien_giam')) {
        $db->exec("ALTER TABLE orders ADD COLUMN so_tien_giam REAL DEFAULT 0;");
        echo "Thêm cột so_tien_giam vào orders thành công!<br>";
    }

    // 3. Thêm cột address vào bảng users
    if (!column_exists($db, 'users', 'address')) {
        $db->exec("ALTER TABLE users ADD COLUMN address TEXT;");
        echo "Thêm cột address vào users thành công!<br>";
    }

    // 4. Thêm mã giảm giá mẫu
    $st = $db->prepare("INSERT OR IGNORE INTO promotions (code, discount_value, discount_type, expiry_date, status) VALUES (?, ?, ?, ?, 1)");
    $st->execute(['JOLLIBEE10', 10.00, 'percentage', '2030-12-31']);
    $st->execute(['FREESHIP', 20000.00, 'flat', '2030-12-31']);
    echo "Chèn mã giảm giá mẫu thành công!<br>";
    
    echo "<h3>Nâng cấp CSDL hoàn thành xuất sắc!</h3>";
} catch (Exception $e) {
    echo "<h3>Lỗi nâng cấp CSDL: " . $e->getMessage() . "</h3>";
}
?>
