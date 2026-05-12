<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']); exit;
}

$name = $_POST['group_name'] ?? '';
$visibility = $_POST['visibility'] ?? 'Public';
$course_code = strtoupper($_POST['course_code'] ?? '');

session_start();
$created_by = $_SESSION['user_id'] ?? $_POST['created_by'] ?? '';

if (!$name || !$course_code || !$created_by) {
    echo json_encode(['success' => false, 'message' => 'All fields required']); exit;
}

try {
    $stmtStudent = $pdo->prepare("SELECT student_id FROM Student WHERE student_id = ?");
    $stmtStudent->execute([$created_by]);
    if (!$stmtStudent->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Your account was not found. Please sign out and register again.']); exit;
    }

    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO StudyGroup (group_name, visibility, course_code, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $visibility, $course_code, $created_by]);
    $groupId = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO GroupMember (student_id, group_id, role, total_points) VALUES (?, ?, 'Admin', 0)");
    $stmt2->execute([$created_by, $groupId]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Group created successfully']);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
