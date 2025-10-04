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

    // Soma de todos os salários do usuário
    $stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(salary_amount), 0) AS total_salaries
        FROM salary
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "total_salaries" => (float) ($result['total_salaries'] ?? 0)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => "Erro no servidor: " . $e->getMessage()]);
} finally {
    $conn = null;
}
