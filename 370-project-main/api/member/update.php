<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['member_id'] ?? '';
$groupId = $_POST['group_id'] ?? '';
$role = $_POST['role'] ?? '';

if (!$groupId) {
    $stmtG = $pdo->prepare("SELECT group_id FROM GroupMember WHERE student_id = ?");
    $stmtG->execute([$id]);
    $m = $stmtG->fetch();
    if ($m) $groupId = $m['group_id'];
}

try {
    if (!$groupId) {
        echo json_encode(['success' => false, 'message' => 'Group ID missing']); exit;
    }

    requireGroupAdmin($pdo, $groupId);

    if ($role === 'Admin') {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) as count FROM GroupMember WHERE group_id = ? AND role = 'Admin'");
        $stmtCheck->execute([$groupId]);
        $res = $stmtCheck->fetch();
        if ($res && $res['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'This group already has an Admin. You cannot assign another.']);
            exit;
        }
    }

    $stmt = $pdo->prepare("UPDATE GroupMember SET role = ? WHERE student_id = ? AND group_id = ?");
    $stmt->execute([$role, $id, $groupId]);
    echo json_encode(['success' => true, 'message' => 'Role updated']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
