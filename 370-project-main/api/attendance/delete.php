<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$id = $_POST['attendance_id'] ?? '';

try {
    $stmt = $pdo->prepare("DELETE FROM Attendance WHERE attendance_id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Attendance deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
