<?php

require_once __DIR__ . '/db/conexao.php';
require_once __DIR__ . '/src/includes/functions.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$recurso = $_GET['recurso'] ?? '';

switch ($metodo) {

    case "GET":

        echo buscarTarefas();
        break;

    case "POST":
        
        $dadosRecebidos = json_decode(file_get_contents('php://input'), true);
        echo criarTarefas($recurso, $dadosRecebidos);
        break;

    case "PUT":

        $dadosRecebidos = json_decode(file_get_contents('php://input'), true);
        echo atualizartarefas($recurso, $dadosRecebidos);
        break;

    case "DELETE":

        $dadosRecebidos = json_decode(file_get_contents('php://input'), true);
        echo atualizartarefas($recurso, $dadosRecebidos);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'mensagem' => 'Método HTTP não permitido']);
        break;
}
?>