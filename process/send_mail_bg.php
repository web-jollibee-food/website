<?php
// process/send_mail_bg.php
define('SECURE_ACCESS', true);
require_once __DIR__ . '/../config.php';

// Chỉ cho phép chạy từ Command Line (CLI) để bảo mật
if (php_sapi_name() !== 'cli') {
    die('Truy cập bị từ chối!');
}

if ($argc < 4) {
    die("Thiếu tham số!\n");
}

$to = $argv[1];
$subject = base64_decode($argv[2]);
$message = base64_decode($argv[3]);

// Gửi email qua kết nối SMTP Gmail thật
send_mail_smtp($to, $subject, $message);
?>
