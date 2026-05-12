<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']); exit;
}

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in to add courses']); exit;
}

$code = $_POST['course_code'] ?? '';
$name = $_POST['course_name'] ?? '';

if (!$code || !$name) {
    echo json_encode(['success' => false, 'message' => 'All fields required']); exit;
}

try {
    $stmtCheck = $pdo->prepare("SELECT 1 FROM Course WHERE course_code = ?");
    $stmtCheck->execute([$code]);
    if ($stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Course code already exists']); exit;
    }

    $stmt = $pdo->prepare("INSERT INTO Course (course_code, course_name) VALUES (?, ?)");
    $stmt->execute([$code, $name]);
    echo json_encode(['success' => true, 'message' => 'Course added successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
