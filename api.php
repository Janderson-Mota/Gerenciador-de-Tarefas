<?php

require_once __DIR__ . '/db/conexao.php';
require_once __DIR__ . '/src/includes/functions.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$recurso = $_GET['recurso'] ?? '';

switch ($metodo) {

    case "GET": 
    
        echo $listaTarefa = buscarTarefas();
        break;

    case "POST": 

        break;

    case "PUT": 
      
        $dadosRecebidos = json_decode(file_get_contents('php://input'), true);
        echo $resposta = atualizartarefas($recurso , $dadosRecebidos);
        break; 

    case "DELETE": 
       
        break;

    default:
        http_response_code(405); 
        echo json_encode(['status' => 'error', 'mensagem' => 'Método HTTP não permitido']);
        break;
}
?>