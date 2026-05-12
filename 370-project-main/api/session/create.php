<?php
require_once '../../config/db.php';
require_once '../../config/auth.php';
header('Content-Type: application/json');

$group_id = $_POST['group_id'] ?? '';
$session_date = $_POST['session_date'] ?? '';
$session_time = $_POST['session_time'] ?? '';
$duration = $_POST['duration'] ?? '';
$topic = $_POST['topic'] ?? '';
$platform_link = $_POST['platform_link'] ?? '';

requireGroupMember($pdo, $group_id);

if (!$group_id || !$session_date || !$session_time || !$duration || !$topic) {
    echo json_encode(['success' => false, 'message' => 'All fields required']); exit;
}

try {
    $stmtNum = $pdo->prepare("SELECT COALESCE(MAX(session_number), 0) + 1 FROM StudySession WHERE group_id = ?");
    $stmtNum->execute([$group_id]);
    $session_number = $stmtNum->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO StudySession (group_id, session_number, session_date, session_time, duration, topic, platform_link) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$group_id, $session_number, $session_date, $session_time, $duration, $topic, $platform_link]);
    
    $new_session_id = $pdo->lastInsertId();

    $stmtMembers = $pdo->prepare("SELECT student_id FROM GroupMember WHERE group_id = ?");
    $stmtMembers->execute([$group_id]);
    $members = $stmtMembers->fetchAll();

    $stmtAtt = $pdo->prepare("INSERT INTO Attendance (session_id, student_id, status) VALUES (?, ?, 'Absent')");
    foreach ($members as $member) {
        $stmtAtt->execute([$new_session_id, $member['student_id']]);
    }

    echo json_encode(['success' => true, 'message' => 'Session created and attendance table initialized']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
