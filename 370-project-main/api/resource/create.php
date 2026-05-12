<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$title = $_POST['title'] ?? '';
$type = $_POST['type'] ?? 'Other';
$url = $_POST['url_path'] ?? '';
$group_id = $_POST['group_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$title || !$url || !$group_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Fields required (ensure logged in)']); exit;
}

requireGroupMember($pdo, $group_id);

try {
    $stmt = $pdo->prepare("INSERT INTO Resource (title, type, url_path, group_id, uploaded_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $type, $url, $group_id, $user_id]);
    echo json_encode(['success' => true, 'message' => 'Resource added']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
