<?php

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; 
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

define('DB_HOST',    getenv('DB_HOST'));
define('DB_NOME',    getenv('DB_NAME')); 
define('DB_USUARIO', getenv('DB_USER'));
define('DB_SENHA',   getenv('DB_PASS'));

$conn = new mysqli(DB_HOST, DB_USUARIO, DB_SENHA, DB_NOME);

if ($conn->connect_error) {
    error_log("Anomalia no DB: " . $conn->connect_error);
    
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode(["erro" => "Falha de ignição no Banco Central [Roadmap]."]));
}

$conn->set_charset("utf8mb4");

?>