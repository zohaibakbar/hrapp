<?php
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Get single employee with department name
            $stmt = $pdo->prepare("
                SELECT employees.*, departments.name as department_name 
                FROM employees 
                LEFT JOIN departments ON employees.department_id = departments.id 
                WHERE employees.id = ?
            ");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
        } else {
            // Get all employees with department names
            $stmt = $pdo->query("
                SELECT employees.*, departments.name as department_name 
                FROM employees 
                LEFT JOIN departments ON employees.department_id = departments.id
            ");
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $password = password_hash($data['password'], PASSWORD_BCRYPT); // Hash the password before storing
        $stmt = $pdo->prepare("INSERT INTO employees (name, email, department_id, phone, basic_salary, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['email'], $data['department_id'], $data['phone'], $data['basic_salary'], $password]);
        echo json_encode(['status' => 'Employee created successfully!']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['password'])) {
            // If password is provided, hash it and update the record
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE employees SET name = ?, email = ?, department_id = ?, phone = ?, basic_salary = ?, password = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['email'], $data['department_id'], $data['phone'], $data['basic_salary'], $password, $data['id']]);
        } else {
            // Update without password
            $stmt = $pdo->prepare("UPDATE employees SET name = ?, email = ?, department_id = ?, phone = ?, basic_salary = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['email'], $data['department_id'], $data['phone'], $data['basic_salary'], $data['id']]);
        }
        
        echo json_encode(['status' => 'Employee updated successfully!']);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(['status' => 'Employee deleted successfully!']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'Method Not Allowed']);
        break;
}
?>
