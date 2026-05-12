<?php
require_once '../../config/db.php';
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT sg.group_id, sg.group_name FROM StudyGroup sg JOIN GroupMember gm ON sg.group_id = gm.group_id WHERE gm.student_id = ? AND gm.role = 'Admin'");
    $stmt->execute([$_SESSION['user_id']]);
    $admin_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT sg.group_id, sg.group_name FROM StudyGroup sg JOIN GroupMember gm ON sg.group_id = gm.group_id WHERE gm.student_id = ?");
    $stmt2->execute([$_SESSION['user_id']]);
    $enrolled_groups = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'user' => [
            'student_id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'department' => $_SESSION['user_dept'],
            'role' => $_SESSION['user_role'] ?? 'Student',
            'admin_groups' => $admin_groups,
            'enrolled_groups' => $enrolled_groups
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
}
?>
