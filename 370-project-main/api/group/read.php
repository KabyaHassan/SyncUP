<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';
$filter = $_GET['filter'] ?? '';

session_start();
$user_id = $_SESSION['user_id'] ?? null;

try {
    if ($type === 'join') {
        $stmt = $pdo->prepare("SELECT sg.group_id, sg.group_name, c.course_name, gm.role as user_role FROM StudyGroup sg JOIN Course c ON sg.course_code = c.course_code LEFT JOIN GroupMember gm ON sg.group_id = gm.group_id AND gm.student_id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'nested') {
        $stmt = $pdo->prepare("SELECT sg.*, gm.role as user_role, c.course_name FROM StudyGroup sg LEFT JOIN Course c ON sg.course_code = c.course_code LEFT JOIN GroupMember gm ON sg.group_id = gm.group_id AND gm.student_id = ? WHERE sg.group_id = ?");
        $stmt->execute([$user_id, $filter]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } else {
        $stmt = $pdo->prepare("SELECT sg.*, gm.role as user_role, c.course_name FROM StudyGroup sg LEFT JOIN Course c ON sg.course_code = c.course_code LEFT JOIN GroupMember gm ON sg.group_id = gm.group_id AND gm.student_id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
