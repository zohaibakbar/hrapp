<?php
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Get single vacation
            $stmt = $pdo->prepare("SELECT * FROM vacations WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
        } else if (isset($_GET['employee_id'])) {
            // Get all vacations for an employee
            $stmt = $pdo->prepare("SELECT * FROM vacations WHERE employee_id = ?");
            $stmt->execute([$_GET['employee_id']]);
            echo json_encode($stmt->fetchAll());
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'Bad Request', 'message' => 'employee_id is required']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        // Convert boolean is_paid to integer (1 or 0)
        $is_paid = $data['is_paid'] ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO vacations (employee_id, start_date, end_date, type, is_paid) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['employee_id'], $data['start_date'], $data['end_date'], $data['type'], $is_paid]);
        echo json_encode(['status' => 'Vacation added successfully!']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        // Convert boolean is_paid to integer (1 or 0)
        $is_paid = $data['is_paid'] ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE vacations SET start_date = ?, end_date = ?, type = ?, is_paid = ? WHERE id = ?");
        $stmt->execute([$data['start_date'], $data['end_date'], $data['type'], $is_paid, $data['id']]);
        echo json_encode(['status' => 'Vacation updated successfully!']);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("DELETE FROM vacations WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(['status' => 'Vacation deleted successfully!']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'Method Not Allowed']);
        break;
}
?>
