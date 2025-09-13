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
    $db = new DbConnect();
    $conn = $db->connect();

    $stmt = $conn->prepare("DELETE FROM finance WHERE user_id = ? AND id= ?");

    $stmt->execute([
        $_SESSION['user_id'], 
        $data['id']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
}