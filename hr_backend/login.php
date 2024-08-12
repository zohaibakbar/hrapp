<?php
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = $data['email'];
    $password = $data['password'];

    // Fetch the employee record by email
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->execute([$email]);
    $employee = $stmt->fetch();

    if ($employee && password_verify($password, $employee['password'])) {
        // Password matches
        echo json_encode([
            'status' => 'success',
            'employee_id' => $employee['id'],
            'name' => $employee['name'],
            'email' => $employee['email']
        ]);
    } else {
        // Invalid credentials
        echo json_encode(['status' => 'fail', 'message' => 'Invalid email or password']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'Method Not Allowed']);
}
?>
