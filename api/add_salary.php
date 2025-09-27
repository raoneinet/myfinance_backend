<?php
session_start();
require "headers.php";
include "connectDb.php";

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => "Método não permitido"]);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => "Não autenticado"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $required = [
        'salary_amount',
        'payment_date'
    ];

    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            http_response_code(400);
            echo json_encode(["error" => "Campo '$field' é obrigatório"]);
            exit;
        }
    }

    $db = new DbConnect();
    $conn = $db->connect();

    $stmt = $conn->prepare(
        "INSERT INTO salary (
                user_id,
                salary_amount,
                salary_desc,
                salary_company,
                payment_date
                ) VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $data['salary_amount'],
        $data['salary_desc'] ?? null,
        $data['salary_company'] ?? null,
        $data['payment_date']
    ]);

    echo json_encode(["success" => true, "message" => "Movimento adicionado com sucesso"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
}