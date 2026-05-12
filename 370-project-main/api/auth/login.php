<?php
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$student_id = $_POST['student_id'] ?? '';
$password = $_POST['password'] ?? '';

if (!$student_id || !$password) {
    echo json_encode(['success' => false, 'message' => 'Student ID and Password are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT student_id, name, gsuite_email, department, password, role FROM Student WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid Student ID']);
        exit;
    }

    if ($password === $user['password'] || password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['student_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['gsuite_email'];
        $_SESSION['user_dept'] = $user['department'];
        $_SESSION['user_role'] = $user['role'] ?? 'Student';

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'student_id' => $user['student_id'],
                'name' => $user['name'],
                'email' => $user['gsuite_email'],
                'department' => $user['department'],
                'role' => $user['role'] ?? 'Student'
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
