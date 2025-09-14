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

    $db = new DbConnect();
    $conn = $db->connect();

    // Se veio um ID -> busca somente uma movimentação
    if (isset($_GET['id'])) {
        $stmt = $conn->prepare(
            "SELECT id,
                    category_id,
                    standard_category, 
                    transaction_value, 
                    transaction_type,
                    fixed_expense, 
                    transaction_desc, 
                    transaction_date  
                    FROM finance 
                    WHERE id = ? AND user_id = ?"
        );

        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Movimento não encontrado"]);
        }
    } else {
        echo json_encode(["error" => "Erro ao buscar movimentos"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
}