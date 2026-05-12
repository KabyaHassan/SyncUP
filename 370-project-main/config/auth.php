<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isGroupAdmin($pdo, $groupId) {
    if (!isset($_SESSION['user_id'])) return false;
    
    $studentId = $_SESSION['user_id'];
    $role = $_SESSION['user_role'] ?? 'Student';

    if ($role === 'Admin') return true;

    if (!$groupId) return false;

    $stmt = $pdo->prepare("SELECT role FROM GroupMember WHERE group_id = ? AND student_id = ?");
    $stmt->execute([$groupId, $studentId]);
    $member = $stmt->fetch();
    
    return $member && $member['role'] === 'Admin';
}

function requireGroupAdmin($pdo, $groupId) {
    if (!isGroupAdmin($pdo, $groupId)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Only Group Admins can perform this action']);
        exit;
    }
}

function isGroupMember($pdo, $groupId) {
    if (!isset($_SESSION['user_id'])) return false;
    
    $studentId = $_SESSION['user_id'];
    $role = $_SESSION['user_role'] ?? 'Student';

    if ($role === 'Admin') return true;

    if (!$groupId) return false;

    $stmt = $pdo->prepare("SELECT role FROM GroupMember WHERE group_id = ? AND student_id = ?");
    $stmt->execute([$groupId, $studentId]);
    $member = $stmt->fetch();
    
    return $member !== false;
}

function requireGroupMember($pdo, $groupId) {
    if (!isGroupMember($pdo, $groupId)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Only Group Members can perform this action']);
        exit;
    }
}
?>
