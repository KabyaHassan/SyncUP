<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$id = $_POST['resource_id'] ?? '';
$title = $_POST['title'] ?? '';
$url_path = $_POST['url_path'] ?? '';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing data']); exit;
}

try {
    if ($title && $url_path) {
        $stmt = $pdo->prepare("UPDATE Resource SET title = ?, url_path = ? WHERE resource_id = ?");
        $stmt->execute([$title, $url_path, $id]);
    } elseif ($title) {
        $stmt = $pdo->prepare("UPDATE Resource SET title = ? WHERE resource_id = ?");
        $stmt->execute([$title, $id]);
    } elseif ($url_path) {
        $stmt = $pdo->prepare("UPDATE Resource SET url_path = ? WHERE resource_id = ?");
        $stmt->execute([$url_path, $id]);
    }
    echo json_encode(['success' => true, 'message' => 'Resource updated']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
