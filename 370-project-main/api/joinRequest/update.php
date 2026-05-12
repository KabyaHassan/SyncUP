<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$id = $_POST['request_id'] ?? '';
$status = $_POST['status'] ?? '';

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing data']); exit;
}

try {
    $stmtG = $pdo->prepare("SELECT group_id FROM JoinRequest WHERE request_id = ?");
    $stmtG->execute([$id]);
    $req = $stmtG->fetch();
    if (!$req) {
        echo json_encode(['success' => false, 'message' => 'Request not found']); exit;
    }

    requireGroupAdmin($pdo, $req['group_id']);

    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("UPDATE JoinRequest SET status = ? WHERE request_id = ?");
    $stmt->execute([$status, $id]);

    if ($status === 'Accepted') {
        $stmt2 = $pdo->prepare("INSERT INTO GroupMember (student_id, group_id) SELECT student_id, group_id FROM JoinRequest WHERE request_id=?");
        $stmt2->execute([$id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => "Request $status"]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
