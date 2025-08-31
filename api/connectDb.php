<?php

/*
Connection to Database
*/
class DbConnect {
    private $server = "localhost";
    private $dbname = "my_finance";
    private $user = "root";
    private $password = "";

    public function connect() {
        try {
            $conn = new PDO(
                "mysql:host={$this->server};dbname={$this->dbname}",
                $this->user,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Database Error: " . $e->getMessage()
            ]);
            exit; // interrompe execução
        }
    }
}

