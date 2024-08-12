<?php
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['employee_id']) && isset($_GET['month']) && isset($_GET['year'])) {
                // Get salary for a specific employee and month
                $stmt = $pdo->prepare("SELECT * FROM salaries WHERE employee_id = ? AND month = ? AND year = ?");
                $stmt->execute([$_GET['employee_id'], $_GET['month'], $_GET['year']]);
                echo json_encode($stmt->fetch());
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'Bad Request']);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            // Fetch the basic salary from the employee table
            $stmt = $pdo->prepare("SELECT basic_salary FROM employees WHERE id = ?");
            $stmt->execute([$data['employee_id']]);
            $employee = $stmt->fetch();

            if (!$employee) {
                throw new Exception("Employee not found");
            }

            $basic_salary = $employee['basic_salary'];
            $month = $data['month'];
            $year = $data['year'];

            // Calculate unpaid leaves
            $stmt = $pdo->prepare("SELECT SUM(DATEDIFF(end_date, start_date) + 1) AS unpaid_days FROM vacations WHERE employee_id = ? AND is_paid = 0 AND MONTH(start_date) = ? AND YEAR(start_date) = ?");
            $stmt->execute([$data['employee_id'], $month, $year]);
            $unpaid_leaves = $stmt->fetch()['unpaid_days'] ?? 0;

            // Calculate deductions
            if ($basic_salary == 0) {
                throw new Exception("Basic salary cannot be zero");
            }

            $daily_salary = $basic_salary / 30; // Assuming a 30-day month
            $deductions = $daily_salary * $unpaid_leaves;

            // Overtime (if applicable)
            $overtime = $data['overtime'] ?? 0;

            // Calculate net salary
            $net_salary = $basic_salary - $deductions + $overtime;

            // Store the calculated salary
            $stmt = $pdo->prepare("INSERT INTO salaries (employee_id, month, year, basic_salary, deductions, overtime, net_salary) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['employee_id'], $month, $year, $basic_salary, $deductions, $overtime, $net_salary]);

            echo json_encode(['status' => 'Salary calculated successfully!', 'net_salary' => $net_salary]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'Method Not Allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'Internal Server Error', 'error' => $e->getMessage()]);
}
?>
