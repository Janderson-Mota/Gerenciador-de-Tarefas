<?php

// $envFile = __DIR__ . './.env';
// if (file_exists($envFile)) {
//     $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//     foreach ($lines as $line) {
//         if (strpos(trim($line), '#') === 0) continue; 
//         list($name, $value) = explode('=', $line, 2);
//         putenv(trim($name) . '=' . trim($value));
//     }
// }





$envFile = __DIR__ . '/.env'; 

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lines !== false) { 
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; 
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                putenv(trim($name) . '=' . trim($value));
            }
        }
    }
}

define('DB_HOST',    getenv('DB_HOST'));
define('DB_NOME',    getenv('DB_NAME')); 
define('DB_USUARIO', getenv('DB_USER'));
define('DB_SENHA',   getenv('DB_PASS'));

function db(): mysqli {
    static $conn = null;
    
    if ($conn === null) {
        // Habilita exceções para o MySQLi (comportamento nativo no PHP 8.1+)
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            $conn = new mysqli(DB_HOST, DB_USUARIO, DB_SENHA, DB_NOME);
        } catch (Exception $e) {
            error_log("Anomalia no DB: " . $e->getMessage());
            
            http_response_code(500);
            header('Content-Type: application/json');
            die(json_encode(["erro" => "Falha de ignição no Banco Central."]));
        }
    }
    
    return $conn;
}

// ─── Upload de imagem ─────────────────────────────────────────
function uploadImagem(array $file, string $pasta = 'uploads'): string|false {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowed)) return false;
    if ($file['size'] > 5 * 1024 * 1024) return false; // 5MB

    $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nome = uniqid('img_', true) . '.' . strtolower($ext);
    $dest = __DIR__ . '/' . $pasta . '/' . $nome;

    if (!is_dir(__DIR__ . '/' . $pasta)) mkdir(__DIR__ . '/' . $pasta, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

    return $pasta . '/' . $nome;
}
