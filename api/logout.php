<?php
session_start();
require "headers.php";

header('Content-Type: application/json');

session_unset();
session_destroy();

echo json_encode(["message" => "Logout realizado com sucesso!"]);