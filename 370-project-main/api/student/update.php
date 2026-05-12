<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['student_id'] ?? '';
$dept = $_POST['department'] ?? '';
$role = $_POST['role'] ?? '';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit;
}

try {
    if ($role) {
        $stmt = $pdo->prepare("UPDATE Student SET role = ? WHERE student_id = ?");
        $stmt->execute([$role, $id]);
    } else if ($dept) {
        $stmt = $pdo->prepare("UPDATE Student SET department = ? WHERE student_id = ?");
        $stmt->execute([$dept, $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nothing to update']);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
