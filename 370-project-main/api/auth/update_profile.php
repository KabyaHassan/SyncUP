<?php
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$email = $_POST['gsuite_email'] ?? '';
$dept = $_POST['department'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (!$name || !$email || !$dept) {
    echo json_encode(['success' => false, 'message' => 'Name, Email and Department are required']);
    exit;
}

try {
    if ($new_password) {
        $stmt = $pdo->prepare("UPDATE Student SET name = ?, gsuite_email = ?, department = ?, password = ? WHERE student_id = ?");
        $stmt->execute([$name, $email, $dept, password_hash($new_password, PASSWORD_DEFAULT), $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE Student SET name = ?, gsuite_email = ?, department = ? WHERE student_id = ?");
        $stmt->execute([$name, $email, $dept, $user_id]);
    }

    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_dept'] = $dept;

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => [
            'student_id' => $user_id,
            'name' => $name,
            'email' => $email,
            'department' => $dept,
            'role' => $_SESSION['user_role']
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
