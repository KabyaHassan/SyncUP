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
    $scope_sql = " AND r.group_id IN (SELECT group_id FROM GroupMember WHERE student_id = ?)";
    $params[] = $user_id;
}

try {
    if ($type === 'join') {
        $sql = "SELECT r.*, s.name as uploader_name, sg.group_name FROM Resource r LEFT JOIN Student s ON r.uploaded_by = s.student_id LEFT JOIN StudyGroup sg ON r.group_id = sg.group_id LEFT JOIN GroupMember gm ON r.group_id = gm.group_id AND gm.student_id = ? WHERE 1=1" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $exec_params = array_merge([$user_id], $params);
        $stmt->execute($exec_params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'my') {
        $stmt = $pdo->prepare("SELECT r.*, s.name as uploader_name, sg.group_name FROM Resource r JOIN GroupMember gm ON r.group_id = gm.group_id LEFT JOIN Student s ON r.uploaded_by = s.student_id LEFT JOIN StudyGroup sg ON r.group_id = sg.group_id WHERE gm.student_id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'nested') {
        $sql = "SELECT r.*, s.name as uploader_name, sg.group_name FROM Resource r LEFT JOIN Student s ON r.uploaded_by = s.student_id LEFT JOIN StudyGroup sg ON r.group_id = sg.group_id WHERE r.group_id = ?" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $exec_params = array_merge([$filter], $params);
        $stmt->execute($exec_params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } else {
        $sql = "SELECT r.*, s.name as uploader_name, sg.group_name FROM Resource r LEFT JOIN Student s ON r.uploaded_by = s.student_id LEFT JOIN StudyGroup sg ON r.group_id = sg.group_id WHERE 1=1" . $scope_sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
