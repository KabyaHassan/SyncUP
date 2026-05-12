<?php
require_once '../../config/db.php';
session_start();
$user_id = $_SESSION['user_id'] ?? null;
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';
$filter_id = $_GET['student_id'] ?? '';

try {
    if ($type === 'join') {
        $stmt = $pdo->query("SELECT s.name, sg.group_name FROM Student s JOIN GroupMember gm ON s.student_id = gm.student_id JOIN StudyGroup sg ON gm.group_id = sg.group_id");
        $data = $stmt->fetchAll();
    } elseif ($type === 'nested') {
        $id = $filter_id ?: $user_id;
        if (!$id) {
             echo json_encode(['success' => false, 'message' => 'No student ID provided']); exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM Student WHERE department = (SELECT department FROM Student WHERE student_id = ?) AND student_id != ?");
        $stmt->execute([$id, $id]);
        $data = $stmt->fetchAll();
    } else {
        $stmt = $pdo->query("SELECT student_id, name, gsuite_email, department, role, created_at FROM Student ORDER BY created_at DESC");
        $data = $stmt->fetchAll();
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
