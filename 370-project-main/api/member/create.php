<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$student_id = $_POST['student_id'] ?? '';
$group_id = $_POST['group_id'] ?? '';

if (!$student_id || !$group_id) {
    echo json_encode(['success' => false, 'message' => 'Fields required']); exit;
}

try {
    requireGroupAdmin($pdo, $group_id);

    $stmt = $pdo->prepare("INSERT INTO GroupMember (student_id, group_id) VALUES (?, ?)");
    $stmt->execute([$student_id, $group_id]);
    echo json_encode(['success' => true, 'message' => 'Member added']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
