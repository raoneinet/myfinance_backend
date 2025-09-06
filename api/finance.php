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

    $db = new DbConnect();
    $conn = $db->connect();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "Não autenticado"]);
        exit;
    }

    $userId = $_SESSION['user_id'];

    if ($userId) {
        $stmt = $conn->prepare(
            "SELECT 
                            id,
                            category_id,
                            standard_category, 
                            transaction_value, 
                            transaction_type, 
                            transaction_desc, 
                            transaction_date 
                    FROM finance WHERE user_id = ?
                    ORDER BY id DESC"
        );
        $stmt->execute([$userId]);
        $user_finance = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'user_id' => $userId,
            'finance' => $user_finance ?? []
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
} finally {
    $conn = null; // Fecha conexão
}


