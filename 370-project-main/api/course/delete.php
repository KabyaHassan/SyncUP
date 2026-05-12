<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Only admins can delete courses']);
    exit;
}

$code = $_POST['course_code'] ?? '';

if (!$code) {
    echo json_encode(['success' => false, 'message' => 'Course code is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM Course WHERE course_code = ?");
    $stmt->execute([$code]);
    echo json_encode(['success' => true, 'message' => 'Course deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
