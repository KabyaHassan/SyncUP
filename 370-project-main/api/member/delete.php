<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['member_id'] ?? '';
$groupId = $_POST['group_id'] ?? '';
$currentUser = $_SESSION['user_id'] ?? '';

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

    if ($currentUser !== $id) {
        requireGroupAdmin($pdo, $groupId);
    }

    $stmtCreator = $pdo->prepare("SELECT created_by FROM StudyGroup WHERE group_id = ?");
    $stmtCreator->execute([$groupId]);
    $group = $stmtCreator->fetch();

    if ($group && $group['created_by'] === $id) {
        $stmtNext = $pdo->prepare("SELECT student_id FROM GroupMember WHERE group_id = ? AND student_id != ? ORDER BY joining_date DESC LIMIT 1");
        $stmtNext->execute([$groupId, $id]);
        $nextMember = $stmtNext->fetch();

        if ($nextMember) {
            $nextId = $nextMember['student_id'];
            $stmtUpdGroup = $pdo->prepare("UPDATE StudyGroup SET created_by = ? WHERE group_id = ?");
            $stmtUpdGroup->execute([$nextId, $groupId]);
            $stmtUpdRole = $pdo->prepare("UPDATE GroupMember SET role = 'Admin' WHERE group_id = ? AND student_id = ?");
            $stmtUpdRole->execute([$groupId, $nextId]);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM GroupMember WHERE student_id = ? AND group_id = ?");
    $stmt->execute([$id, $groupId]);
    echo json_encode(['success' => true, 'message' => 'Member removed']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
