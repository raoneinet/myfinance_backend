<?php
session_start();
require "headers.php";
include "connectDb.php";

header('Content-Type: application/json');

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(["error" => "Método não permitido"]);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "Não autenticado"]);
        exit;
    }

    $month = isset($_GET['month']) ? (int) $_GET['month'] : date('m');
    $year = isset($_GET['year']) ? (int) $_GET['year'] : date('Y');

    $db = new DbConnect();
    $conn = $db->connect();

    $stmt = $conn->prepare(
        "SELECT 
        id,
        category_id,
        standard_category, 
        transaction_value, 
        transaction_type,
        fixed_expense, 
        transaction_desc, 
        transaction_date
        FROM finance
        WHERE user_id = ?
        AND MONTH(transaction_date) = ?
        AND YEAR(transaction_date) = ?
        ORDER BY transaction_date DESC
    "
    );

    $stmt->execute([$_SESSION['user_id'], $month, $year]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Movimento não encontrado"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
}