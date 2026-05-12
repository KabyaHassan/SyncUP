<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'join';
$filter = $_GET['filter'] ?? 'DB Warriors';

session_start();
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? 'Student';

try {
    if ($user_role === 'Admin') {
        $stmt = $pdo->query("SELECT jr.*, s.name as student_name, sg.group_name FROM JoinRequest jr JOIN Student s ON jr.student_id = s.student_id JOIN StudyGroup sg ON jr.group_id = sg.group_id");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'nested') {
        if (!$filter) {
            echo json_encode(['success' => true, 'data' => []]); exit;
        }
        
        if ($user_role !== 'Admin') {
            $check = $pdo->prepare("SELECT 1 FROM GroupMember WHERE group_id = ? AND student_id = ? AND role = 'Admin'");
            $check->execute([$filter, $user_id]);
            if (!$check->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
            }
        }

        $stmt = $pdo->prepare("SELECT jr.*, sg.group_name, s.name as student_name FROM JoinRequest jr JOIN StudyGroup sg ON jr.group_id = sg.group_id JOIN Student s ON jr.student_id = s.student_id WHERE jr.group_id = ?");
        $stmt->execute([$filter]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } else {
        $stmt = $pdo->prepare("SELECT jr.*, s.name as student_name, sg.group_name FROM JoinRequest jr JOIN Student s ON jr.student_id = s.student_id JOIN StudyGroup sg ON jr.group_id = sg.group_id JOIN GroupMember gm ON sg.group_id = gm.group_id WHERE gm.student_id = ? AND gm.role = 'Admin'");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
