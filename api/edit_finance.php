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

    if (!isset($data['id']) || empty($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Campo 'id' é obrigatório"]);
        exit;
    }

    $required = [
        'id',
        'transaction_value',
        'standard_category',
        'transaction_type',
        'fixed_expense',
        'transaction_desc',
        'transaction_date'
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
        "UPDATE finance 
        SET transaction_value = ?, 
        standard_category = ?, 
        transaction_type = ?, 
        fixed_expense = ?, 
        transaction_desc = ?, 
        transaction_date = ?
        WHERE id = ? AND user_id = ?
        "
    );

    $stmt->execute([
        $data['transaction_value'],
        $data['standard_category'],
        $data['transaction_type'],
        $data['fixed_expense'],
        $data['transaction_desc'],
        $data['transaction_date'],
        $data['id'],
        $_SESSION['user_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Despesa atualizada com sucesso"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Despesa não encontrada ou não pertence ao usuário"]);
    }


} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
}