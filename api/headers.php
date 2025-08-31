<?php
// Permitir o frontend específico
header("Access-Control-Allow-Origin: http://localhost:3000");

// Permitir envio de cookies
header("Access-Control-Allow-Credentials: true");

// Permitir cabeçalhos usados pelo Axios
header("Access-Control-Allow-Headers: Content-Type");

// Permitir métodos OPTIONS (preflight) e POST/GET
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Responder requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}