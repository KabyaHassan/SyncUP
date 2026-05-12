<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$id = $_POST['request_id'] ?? '';

try {
    $stmt = $pdo->prepare("DELETE FROM JoinRequest WHERE request_id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Request deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
