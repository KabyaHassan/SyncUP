<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['group_id'] ?? '';
requireGroupAdmin($pdo, $id);

try {
    $stmt = $pdo->prepare("DELETE FROM StudyGroup WHERE group_id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Group deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
