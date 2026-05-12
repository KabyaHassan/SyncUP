<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$id = $_POST['session_id'] ?? '';
$topic = $_POST['topic'] ?? '';

try {
    $stmt = $pdo->prepare("UPDATE StudySession SET topic = ? WHERE session_id = ?");
    $stmt->execute([$topic, $id]);
    echo json_encode(['success' => true, 'message' => 'Session topic updated']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
