<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$student_id = $_POST['student_id'] ?? '';
$group_id = $_POST['group_id'] ?? '';

if (!$student_id || !$group_id) {
    echo json_encode(['success' => false, 'message' => 'Student ID and Group ID required']); exit;
}

try {
    $stmtStudent = $pdo->prepare("SELECT student_id FROM Student WHERE student_id = ?");
    $stmtStudent->execute([$student_id]);
    if (!$stmtStudent->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Your account was not found. Please sign out and register again.']); exit;
    }

    $stmtExisting = $pdo->prepare("SELECT 1 FROM GroupMember WHERE student_id = ? AND group_id = ?");
    $stmtExisting->execute([$student_id, $group_id]);
    if ($stmtExisting->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You are already a member of this group']);
        exit;
    }

    $stmtGroup = $pdo->prepare("SELECT visibility FROM StudyGroup WHERE group_id = ?");
    $stmtGroup->execute([$group_id]);
    $group = $stmtGroup->fetch();

    if (!$group) {
        echo json_encode(['success' => false, 'message' => 'Group not found']); exit;
    }

    if ($group['visibility'] === 'Public') {
        $stmtJoin = $pdo->prepare("INSERT INTO GroupMember (student_id, group_id, role, total_points) VALUES (?, ?, 'General Member', 0)");
        $stmtJoin->execute([$student_id, $group_id]);
        echo json_encode(['success' => true, 'message' => 'Joined group successfully!']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO JoinRequest (student_id, group_id) SELECT ?, ? WHERE NOT EXISTS (SELECT 1 FROM JoinRequest WHERE student_id=? AND group_id=?)");
        $stmt->execute([$student_id, $group_id, $student_id, $group_id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Join request sent']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request already exists']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
