<?php
session_start([
    'cookie_lifetime' => 86400, // Sessão válida por 1 dia
    'cookie_secure' => false,    // true se usar HTTPS
    'cookie_httponly' => true,   // impede JS de acessar o cookie
    'cookie_samesite' => 'Lax'   // permite envio de cookies cross-site básico
]);
require "headers.php";
include "connectDb.php";

header('Content-Type: application/json');

$db = new DbConnect();
$conn = $db->connect();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}

// Busca usuário pelo ID da sessão
$stmt = $conn->prepare("SELECT id, fullname, email FROM users WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user) {
    echo json_encode($user);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Usuário não encontrado"]);
}
