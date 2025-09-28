<?php
session_start();
require "headers.php";
include "connectDb.php";

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => "Não autenticado"]);
        exit;
    }

    $db = new DbConnect();
    $conn = $db->connect();

    // mês e ano via query string ou usa atual
    $month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('n'); // n = sem zero à esquerda
    $year  = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');

    $stmt = $conn->prepare(
        "SELECT 
            id,
            salary_amount,
            salary_desc,
            salary_company,
            payment_date
        FROM salary
        WHERE user_id = ?
          AND MONTH(payment_date) = ?
          AND YEAR(payment_date) = ?
        LIMIT 1
    ");

    $stmt->execute([$_SESSION['user_id'], $month, $year]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            "id" => $result['id'],
            "salary_amount" => (float) $result['salary_amount'],
            "salary_desc" => $result['salary_desc'],
            "salary_company" => $result['salary_company'],
            "payment_date" => $result['payment_date'],
            "month" => $month,
            "year" => $year
        ]);
    } else {
        echo json_encode([
            "id" => null,
            "salary_amount" => 0,
            "salary_desc" => null,
            "salary_company" => null,
            "payment_date" => null,
            "month" => $month,
            "year" => $year
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "erro no servidor: " . $e->getMessage()]);
}