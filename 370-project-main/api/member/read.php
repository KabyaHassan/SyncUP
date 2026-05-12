<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

session_start();
$user_id = $_SESSION['user_id'] ?? null;
$is_admin = ($_SESSION['user_role'] ?? '') === 'Admin';

$type = $_GET['type'] ?? 'all'; 
$filter = $_GET['filter'] ?? '';

$scope_sql = "";
$params = [];

if (!$is_admin && $user_id) {
    $scope_sql = " AND gm.group_id IN (SELECT group_id FROM GroupMember WHERE student_id = ?)";
    $params[] = $user_id;
}

try {
    if ($type === 'join') {
        $sql = "SELECT gm.student_id, gm.group_id, sg.group_name, s.name, gm.role, gm.total_points FROM GroupMember gm JOIN Student s ON gm.student_id = s.student_id JOIN StudyGroup sg ON gm.group_id = sg.group_id WHERE 1=1" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'nested') {
        $sql = "SELECT gm.student_id, gm.group_id, sg.group_name, s.name, gm.role, gm.total_points FROM GroupMember gm JOIN Student s ON gm.student_id = s.student_id JOIN StudyGroup sg ON gm.group_id = sg.group_id WHERE gm.group_id = ?" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $exec_params = array_merge([$filter], $params);
        $stmt->execute($exec_params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } else {
        $sql = "SELECT gm.student_id, gm.group_id, sg.group_name, s.name, gm.role, gm.total_points FROM GroupMember gm JOIN Student s ON gm.student_id = s.student_id JOIN StudyGroup sg ON gm.group_id = sg.group_id WHERE 1=1" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
