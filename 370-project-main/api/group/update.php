<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['group_id'] ?? '';
requireGroupAdmin($pdo, $id);

try {
    $stmtCurrent = $pdo->prepare("SELECT group_name, visibility FROM StudyGroup WHERE group_id = ?");
    $stmtCurrent->execute([$id]);
    $current = $stmtCurrent->fetch();
    if (!$current) {
        echo json_encode(['success' => false, 'message' => 'Group not found']); exit;
    }

    $name = $_POST['group_name'] ?? $current['group_name'];
    $visibility = $_POST['visibility'] ?? $current['visibility'];

    $stmt = $pdo->prepare("UPDATE StudyGroup SET group_name = ?, visibility = ? WHERE group_id = ?");
    $stmt->execute([$name, $visibility, $id]);
    echo json_encode(['success' => true, 'message' => 'Group updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
