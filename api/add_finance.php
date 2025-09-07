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
        'expense_value',
        'expense_standard_category',
        'expense_payment_type',
        'expense_isFixed',
        'expense_desc',
        'expense_date'
    ];
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            http_response_code(400);
            echo json_encode(["error" => "Campo '$field' é obrigatório"]);
            exit;
        }
    }

    $valid_standard_categories = ['Alimentação', 'Lazer', 'Transporte', 'Saúde', 'Casa', 'Entretenimento', 'Educação', 'Recebimento']; // Ajuste para seus ENUM reais
    $valid_payment_types = ['Dinheiro', 'Transferência','Crédito', 'Débito']; // Ajuste para seus ENUM reais
    $valid_fixed = ['fixed', 'notFixed']; // Ajuste conforme seu ENUM

    if(!in_array($data['expense_standard_category'],$valid_standard_categories)){
        http_response_code(400);
        echo json_encode(["error" => "Categoria inválida"]);
        exit;
    }

    if (!in_array($data['expense_payment_type'], $valid_payment_types)) {
        http_response_code(400);
        echo json_encode(["error" => "Tipo de pagamento inválido"]);
        exit;
    }
    if (!in_array($data['expense_isFixed'], $valid_fixed)) {
        http_response_code(400);
        echo json_encode(["error" => "Valor de despesa fixa inválido"]);
        exit;
    }

    $db = new DbConnect();
    $conn = $db->connect();

    $stmt = $conn->prepare(
        "INSERT INTO finance (
                user_id,
                transaction_value,
                standard_category,
                transaction_type,
                fixed_expense,
                transaction_desc,
                transaction_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $data['expense_value'],
        $data['expense_standard_category'],
        $data['expense_payment_type'],
        $data['expense_isFixed'],
        $data['expense_desc'],
        $data['expense_date']
    ]);

    echo json_encode(["success" => true, "message" => "Movimento adicionado com sucesso"]);


} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
} finally {
    $conn = null;
}