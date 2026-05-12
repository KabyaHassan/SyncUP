<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'join';
$filter = $_GET['filter'] ?? '1';

try {
    if ($type === 'join') {
        $stmt = $pdo->query("SELECT s.name, SUM(gm.total_points) as total_points FROM GroupMember gm JOIN Student s ON gm.student_id = s.student_id GROUP BY s.student_id ORDER BY total_points DESC LIMIT 10");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } elseif ($type === 'nested') {
        $stmt = $pdo->prepare("SELECT s.name, gm.total_points FROM GroupMember gm JOIN Student s ON gm.student_id = s.student_id WHERE gm.group_id = ? ORDER BY gm.total_points DESC");
        $stmt->execute([$filter]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } else {
        $stmt = $pdo->query("SELECT s.name, SUM(gm.total_points) as total_points FROM GroupMember gm JOIN Student s ON gm.student_id = s.student_id GROUP BY s.student_id ORDER BY total_points DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
