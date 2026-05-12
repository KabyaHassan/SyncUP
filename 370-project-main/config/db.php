<?php
$host = '127.0.0.1';
$port = '3306';
$dbname = 'StudyGroupSystem';
$user = 'php_user';
$pass = 'password123';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Database Connection Failed", "error" => $e->getMessage()]);
    exit;
}
?>
