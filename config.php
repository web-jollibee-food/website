<?php
// config.php
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Đã được cấu hình tự động thích ứng với cơ sở dữ liệu SQLite jollibee.db cục bộ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Tải các biến môi trường cấu hình bảo mật từ tệp .env
function load_env($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $name = trim($parts[0]);
            $value = trim($parts[1]);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
load_env(__DIR__ . '/.env');

// Hàm đọc biến môi trường kèm giá trị mặc định
function get_env_val($key, $default = '') {
    $val = getenv($key);
    return ($val !== false) ? $val : $default;
}

// ==========================================================================
// CẤU HÌNH GỬI MAIL SMTP (CHO QUÊN MẬT KHẨU)
// ==========================================================================
define('SMTP_HOST', get_env_val('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', (int)get_env_val('SMTP_PORT', '465'));
define('SMTP_USER', get_env_val('SMTP_USER', 'YOUR_EMAIL@gmail.com'));
define('SMTP_PASS', get_env_val('SMTP_PASS', 'YOUR_APP_PASSWORD'));
define('SMTP_FROM', get_env_val('SMTP_FROM', ''));

// ==========================================================================
// CẤU HÌNH CỔNG THANH TOÁN (SANDBOX THỬ NGHIỆM MẶC ĐỊNH)
// ==========================================================================
define('PAYMENT_BASE_URL', get_env_val('PAYMENT_BASE_URL', ''));

// ==========================================================================
// THƯ VIỆN BỔ TRỢ: GIẢ LẬP MYSQLI SANG SQLITE (GIỮ NGUYÊN CODE LOGIC DỰ ÁN)
// ==========================================================================
class SQLiteMySQLiResult {
    private $rows;
    private $index = 0;
    public $num_rows = 0;

    public function __construct($rows) {
        $this->rows = $rows ?: [];
        $this->num_rows = count($this->rows);
    }

    public function fetch_all($mode = 1) {
        return $this->rows;
    }

    public function fetch_assoc() {
        if ($this->index < $this->num_rows) {
            return $this->rows[$this->index++];
        }
        return null;
    }

    public function close() {
        return true;
    }
}

class SQLiteMySQLiStmt {
    private $pdo;
    private $stmt;
    private $connection;
    private $params = [];

    public function __construct($connection, $pdo, $sql) {
        $this->connection = $connection;
        $this->pdo = $pdo;
        
        // Chuyển đổi các cú pháp thời gian MySQL sang SQLite tương ứng
        $sql = str_ireplace('DATE_ADD(NOW(), INTERVAL 1 HOUR)', "datetime('now', '+1 hour', 'localtime')", $sql);
        $sql = str_ireplace('NOW()', "datetime('now', 'localtime')", $sql);
        
        $this->stmt = $pdo->prepare($sql);
    }

    public function bind_param($types, &...$vars) {
        $this->params = $vars;
        return true;
    }

    public function execute() {
        foreach ($this->params as $index => &$value) {
            $this->stmt->bindValue($index + 1, $value);
        }
        $res = $this->stmt->execute();
        $this->connection->insert_id = $this->pdo->lastInsertId();
        return $res;
    }

    public function get_result() {
        $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        return new SQLiteMySQLiResult($rows);
    }

    public function close() {
        $this->stmt = null;
        return true;
    }
}

class SQLiteMySQLiConnection {
    private $pdo;
    public $insert_id = 0;
    public $error = '';

    public function __construct($db_file) {
        $this->pdo = new PDO("sqlite:" . $db_file);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("PRAGMA foreign_keys = ON;");
    }

    public function query($sql) {
        try {
            $stmt = $this->pdo->query($sql);
            if ($stmt) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return new SQLiteMySQLiResult($rows);
            }
            return false;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function prepare($sql) {
        return new SQLiteMySQLiStmt($this, $this->pdo, $sql);
    }

    public function begin_transaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        $result = $this->pdo->commit();
        $this->insert_id = $this->pdo->lastInsertId();
        return $result;
    }

    public function rollback() {
        return $this->pdo->rollBack();
    }

    public function set_charset($charset) {
        return true;
    }
}

// 1. Tạo kết nối giả lập MySQLi dùng SQLite (Không cần cài MySQL server)
$db_path = __DIR__ . '/jollibee.db';
$conn = new SQLiteMySQLiConnection($db_path);

// 2. Tạo kết nối dạng PDO Singleton (Cho các file chạy PDO cũ)
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO("sqlite:" . __DIR__ . '/jollibee.db');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("PRAGMA foreign_keys = ON;");
        } catch (PDOException $e) {
            die("Kết nối PDO SQLite thất bại: " . $e->getMessage());
        }
    }
    return $pdo;
}

// 3. Hàm gửi mail chuyên nghiệp qua SMTP (Hỗ trợ Gmail) hoặc mô phỏng qua tệp tin logs
function send_mail_smtp($to, $subject, $message) {
    $smtp_host = SMTP_HOST;
    $smtp_port = SMTP_PORT;
    $smtp_user = SMTP_USER;
    $smtp_pass = SMTP_PASS;
    
    // Nếu chưa cấu hình email thật, hệ thống sẽ lưu thư vào thư mục logs_emails để demo
    if ($smtp_user === 'YOUR_EMAIL@gmail.com') {
        $log_dir = __DIR__ . '/logs_emails';
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        $log_file = $log_dir . '/email_to_' . preg_replace('/[^a-zA-Z0-9@._-]/', '', $to) . '_' . time() . '.txt';
        $content = "====== EMAIL TO: $to ======\n";
        $content .= "Subject: $subject\n";
        $content .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Content:\n$message\n";
        $content .= "==================================\n";
        file_put_contents($log_file, $content);
        return 'logged';
    }

    $socket = @fsockopen("ssl://" . $smtp_host, $smtp_port, $errno, $errstr, 10);
    if (!$socket) {
        return false;
    }

    function get_smtp_response($socket) {
        $response = "";
        while ($line = fgets($socket, 256)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    }

    get_smtp_response($socket);
    
    fwrite($socket, "EHLO localhost\r\n");
    get_smtp_response($socket);

    fwrite($socket, "AUTH LOGIN\r\n");
    get_smtp_response($socket);

    fwrite($socket, base64_encode($smtp_user) . "\r\n");
    get_smtp_response($socket);

    fwrite($socket, base64_encode($smtp_pass) . "\r\n");
    get_smtp_response($socket);

    $smtp_from = (defined('SMTP_FROM') && SMTP_FROM !== '') ? SMTP_FROM : $smtp_user;

    fwrite($socket, "MAIL FROM: <$smtp_from>\r\n");
    get_smtp_response($socket);

    fwrite($socket, "RCPT TO: <$to>\r\n");
    get_smtp_response($socket);

    fwrite($socket, "DATA\r\n");
    get_smtp_response($socket);

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "To: <$to>\r\n";
    $headers .= "From: Jollibee Food <$smtp_from>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";

    fwrite($socket, $headers . "\r\n" . $message . "\r\n.\r\n");
    get_smtp_response($socket);

    fwrite($socket, "QUIT\r\n");
    fclose($socket);
    return true;
}
?>