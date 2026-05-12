<?php
require_once '../../config/db.php';
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Only admins can manage student records']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = trim($_POST['student_id'] ?? '');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['gsuite_email'] ?? '');
$dept = trim($_POST['department'] ?? '');
$password = $_POST['password'] ?? '';

if (!$id || !$name || !$email || !$dept || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $checkId = $pdo->prepare("SELECT student_id FROM Student WHERE student_id = ?");
    $checkId->execute([$id]);
    if ($checkId->fetch()) {
        echo json_encode(['success' => false, 'message' => 'A student with this ID already exists. Please use a different ID or sign in.']);
        exit;
    }

    $checkEmail = $pdo->prepare("SELECT student_id FROM Student WHERE gsuite_email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->fetch()) {
        echo json_encode(['success' => false, 'message' => 'This email is already registered. Please use a different email or sign in.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO Student (student_id, name, gsuite_email, department, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $name, $email, $dept, password_hash($password, PASSWORD_DEFAULT)]);
    
    echo json_encode(['success' => true, 'message' => 'Student added successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
