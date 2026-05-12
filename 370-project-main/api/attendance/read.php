<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

session_start();
$user_id = $_SESSION['user_id'] ?? null;
$is_admin = ($_SESSION['user_role'] ?? '') === 'Admin';

$type = $_GET['type'] ?? 'join';
$filter = $_GET['filter'] ?? 'Normalization';

$scope_sql = "";
$params = [];

if (!$is_admin && $user_id) {
    $scope_sql = " AND (a.student_id = ? OR ss.group_id IN (SELECT group_id FROM GroupMember WHERE student_id = ? AND role = 'Admin'))";
    $params[] = $user_id;
    $params[] = $user_id;
}

try {
    if ($type === 'join') {
        $sql = "SELECT a.attendance_id, a.student_id, s.name, a.status, a.marked_at, ss.topic, ss.group_id FROM Attendance a JOIN Student s ON a.student_id = s.student_id JOIN StudySession ss ON a.session_id = ss.session_id WHERE 1=1" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'nested') {
        $sql = "SELECT a.attendance_id, a.session_id, a.student_id, s.name, a.status, a.marked_at, ss.topic, ss.group_id FROM Attendance a JOIN Student s ON a.student_id = s.student_id JOIN StudySession ss ON a.session_id = ss.session_id WHERE ss.topic = ?" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $exec_params = array_merge([$filter], $params);
        $stmt->execute($exec_params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } else {
        $sql = "SELECT a.*, ss.group_id FROM Attendance a JOIN StudySession ss ON a.session_id = ss.session_id WHERE 1=1" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
