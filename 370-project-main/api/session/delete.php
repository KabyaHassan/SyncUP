<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['session_id'] ?? '';

$stmtGroup = $pdo->prepare("SELECT group_id FROM StudySession WHERE session_id = ?");
$stmtGroup->execute([$id]);
$session = $stmtGroup->fetch();
$groupId = $session['group_id'] ?? null;

requireGroupAdmin($pdo, $groupId);

try {
    $stmt = $pdo->prepare("DELETE FROM StudySession WHERE session_id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Session deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
