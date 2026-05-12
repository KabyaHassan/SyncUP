<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['resource_id'] ?? '';

$stmtGroup = $pdo->prepare("SELECT group_id FROM Resource WHERE resource_id = ?");
$stmtGroup->execute([$id]);
$res = $stmtGroup->fetch();
$groupId = $res['group_id'] ?? null;

requireGroupAdmin($pdo, $groupId);

try {
    $stmt = $pdo->prepare("DELETE FROM Resource WHERE resource_id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Resource deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
