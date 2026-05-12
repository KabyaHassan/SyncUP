<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$session_id = $_POST['session_id'] ?? '';
$student_id = $_POST['member_id'] ?? '';
$status = $_POST['status'] ?? 'Present';

if (!$session_id || !$student_id) {
    echo json_encode(['success' => false, 'message' => 'Session ID and Student ID are required']); exit;
}

try {
    $stmtS = $pdo->prepare("SELECT group_id FROM StudySession WHERE session_id = ?");
    $stmtS->execute([$session_id]);
    $sess = $stmtS->fetch();
    if (!$sess) {
        echo json_encode(['success' => false, 'message' => 'Session not found']); exit;
    }

    requireGroupAdmin($pdo, $sess['group_id']);

    $stmt = $pdo->prepare("INSERT INTO Attendance (session_id, student_id, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");
    $stmt->execute([$session_id, $student_id, $status]);
    echo json_encode(['success' => true, 'message' => 'Attendance recorded manually']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
