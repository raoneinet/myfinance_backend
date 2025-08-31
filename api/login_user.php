<?php
session_start([
    'cookie_lifetime' => 86400, // Sessão válida por 1 dia
    'cookie_secure' => false,    // true se usar HTTPS
    'cookie_httponly' => true,   // impede JS de acessar o cookie
    'cookie_samesite' => 'Lax'   // permite envio de cookies cross-site básico
]);
require 'headers.php'; // <-- Inclua primeiro!
include 'connectDb.php';

header('Content-Type: application/json');

$db = new DbConnect();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents('php://input');
    $input = json_decode($rawData, true);

    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dbUser && password_verify($password, $dbUser['password'])) {
        $_SESSION['user_id'] = $dbUser['id'];

        echo json_encode([
            "user" => [
                "id" => $dbUser['id'],
                "fullname" => $dbUser['fullname'],
                "email" => $dbUser['email']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Credenciais inválidas"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
}
