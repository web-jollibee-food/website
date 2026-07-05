<?php
// import_db.php
require_once 'config.php';

// First, check if jollibee_food database exists and drop it to start clean
$temp_conn = new mysqli($host, $user, $pass);
if ($temp_conn->connect_error) {
    die("Lỗi kết nối MySQL: " . $temp_conn->connect_error);
}
$temp_conn->query("DROP DATABASE IF EXISTS jollibee_food");
$temp_conn->query("CREATE DATABASE jollibee_food CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$temp_conn->close();

// Now connect to the new database
$conn = new mysqli($host, $user, $pass, 'jollibee_food');
if ($conn->connect_error) {
    die("Lỗi kết nối jollibee_food: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$sql = file_get_contents('jollibee_food.sql');

// Remove CREATE DATABASE and USE statements from the SQL file since we already handle database creation
$sql = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql);
$sql = preg_replace('/USE [^;]+;/i', '', $sql);

// Split queries by semicolon (simple splitter, works for this file)
$queries = explode(';', $sql);

$conn->begin_transaction();
try {
    foreach ($queries as $query) {
        $query = trim($query);
        if ($query !== '') {
            if (!$conn->query($query)) {
                throw new Exception("Lỗi khi chạy query: " . $conn->error . "\nQuery: " . $query);
            }
        }
    }
    $conn->commit();
    echo "Khởi tạo database jollibee_food thành công với mã hóa UTF-8 đầy đủ!\n";
} catch (Exception $e) {
    $conn->rollback();
    echo "Lỗi: " . $e->getMessage() . "\n";
}
$conn->close();
?>
