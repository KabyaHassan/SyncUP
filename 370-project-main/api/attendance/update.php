<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['attendance_id'] ?? '';
$status = $_POST['status'] ?? '';

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing data']); exit;
}

try {
    $stmtG = $pdo->prepare("SELECT ss.group_id FROM Attendance a JOIN StudySession ss ON a.session_id = ss.session_id WHERE a.attendance_id = ?");
    $stmtG->execute([$id]);
    $g = $stmtG->fetch();
    if (!$g) {
        echo json_encode(['success' => false, 'message' => 'Attendance record not found']); exit;
    }
    requireGroupAdmin($pdo, $g['group_id']);

    $stmt = $pdo->prepare("UPDATE Attendance SET status = ? WHERE attendance_id = ?");
    $stmt->execute([$status, $id]);
    echo json_encode(['success' => true, 'message' => 'Attendance updated']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
