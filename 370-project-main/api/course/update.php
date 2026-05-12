<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$code = $_POST['course_code'] ?? '';
$name = $_POST['course_name'] ?? '';

try {
    $stmt = $pdo->prepare("UPDATE Course SET course_name = ? WHERE course_code = ?");
    $stmt->execute([$name, $code]);
    echo json_encode(['success' => true, 'message' => 'Course updated']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
