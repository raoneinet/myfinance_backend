<?php
require 'headers.php';
include 'connectDb.php';

$db = new DbConnect();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents('php://input');
    $user = json_decode($data);

    if (!$user || !isset($user->fullname, $user->email, $user->password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos']);
        exit;
    }

    try{
        //password hash
        $hashed_password = password_hash($user->password, PASSWORD_DEFAULT);
        //user creation time
        $created_at = date("Y-m-d H:i:s");

        //Query
        $sql = "INSERT INTO users(fullname, email, password, created_at) VALUES (:fullname, :email, :password, :created_at)";
        $stmt = $conn->prepare($sql);

        //Bind values
        $stmt->bindParam(':fullname', $user->fullname);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':created_at', $created_at);

        if($stmt->execute()){
            http_response_code(201);
            echo json_encode(['message' => 'Usuário cadastrado com sucesso']);
        }else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao cadastrar usuário']);
        }

    } catch(PDOException $e){
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}