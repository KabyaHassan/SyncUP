<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

session_start();
$user_id = $_SESSION['user_id'] ?? null;
$is_admin = ($_SESSION['user_role'] ?? '') === 'Admin';

$type = $_GET['type'] ?? 'all';
$filter = $_GET['filter'] ?? '';

$scope_sql = "";
$params = [$user_id];

if (!$is_admin && $user_id) {
    $scope_sql = " AND ss.group_id IN (SELECT group_id FROM GroupMember WHERE student_id = ?)";
    $params[] = $user_id;
}

try {
    if ($type === 'join' || $type === 'all') {
        $sql = "SELECT ss.*, sg.group_name, gm.role as user_role FROM StudySession ss JOIN StudyGroup sg ON ss.group_id = sg.group_id LEFT JOIN GroupMember gm ON ss.group_id = gm.group_id AND gm.student_id = ? WHERE 1=1" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'my') {
        $stmt = $pdo->prepare("SELECT ss.*, sg.group_name, gm.role as user_role FROM StudySession ss JOIN GroupMember gm ON ss.group_id = gm.group_id JOIN StudyGroup sg ON ss.group_id = sg.group_id WHERE gm.student_id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'nested') {
        $sql = "SELECT ss.*, sg.group_name, gm.role as user_role FROM StudySession ss JOIN StudyGroup sg ON ss.group_id = sg.group_id LEFT JOIN GroupMember gm ON ss.group_id = gm.group_id AND gm.student_id = ? WHERE ss.group_id = ?" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $exec_params = array_merge([$user_id, $filter], $is_admin ? [] : [$user_id]);
        $stmt->execute($exec_params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
