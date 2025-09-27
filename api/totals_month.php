<?php
session_start();
require "headers.php";
include "connectDb.php";

header("Content-Type: application/json");

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => "Não autenticado"]);
        exit;
    }

    $month = max(1, min(12, (int) ($_GET['month'] ?? date('m'))));
    $year = (int) ($_GET['year'] ?? date('Y'));

    $db = new DbConnect();
    $conn = $db->connect();

    $stmt = $conn->prepare(
        "SELECT 
            SUM(CASE WHEN standard_category = 'Recebimento' THEN transaction_value ELSE 0 END) AS extra_income_total,
            SUM(CASE WHEN standard_category != 'Recebimento' THEN transaction_value ELSE 0 END) AS total_geral
            from finance WHERE user_id = ?
            AND MONTH(transaction_date) = ?
            AND YEAR(transaction_date) = ?"
    );

    $stmt->execute([$_SESSION['user_id'], $month, $year]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "total_geral" => isset($result['total_geral']) ? (float) $result['total_geral'] : 0,
        "extra_income" => isset($result['extra_income_total']) ? (float) $result['extra_income_total'] : 0
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
}