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

    $db = new DbConnect();
    $conn = $db->connect();

    $stmt = $conn->prepare(
        "SELECT transaction_date 
                FROM finance 
                WHERE user_id = ?"
    );

    $stmt->execute([$_SESSION['user_id']]);
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