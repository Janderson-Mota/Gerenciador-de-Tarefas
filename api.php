<?php

$action = $_GET['action'] ?? '';
$caminhoDb = './db/db.json';
$listaTarefa = json_decode(file_get_contents($caminhoDb), true) ?? [];

switch ($action) {

    case "list":

        http_response_code(200);
        echo json_encode(['status' => 'ok', 'tarefas' => $listaTarefa]);
        break;

    case "create":

        $novaTarefa = "create tarefas";

        http_response_code(201);
        echo json_encode(['status' => 'ok', 'tarefas' => $novaTarefa]);
        break;

    case "update":

        $dadosRecebidos = json_decode(file_get_contents('php://input'), true);
        $idRecebido = $dadosRecebidos['id'] ?? null;
        $tarefaEncontrada = false;

        foreach ($listaTarefa as &$tarefa) {
            if ($tarefa['id'] == $idRecebido) {

                foreach ($dadosRecebidos as $chave => $valor) {
                    if ($chave !== 'id') {
                        $tarefa[$chave] = $valor;
                    }
                }
                $tarefa['atualizado_em'] = date('Y-m-d H:i:s');
                $tarefaEncontrada = true;
                break;
            }
        }

        if ($tarefaEncontrada) {
            file_put_contents($caminhoDb, json_encode($listaTarefa, JSON_PRETTY_PRINT));
            http_response_code(200);
            echo json_encode(['status' => 'ok', 'mensagem' => 'Tarefa atualizada com sucesso']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'mensagem' => 'Tarefa não encontrada']);
        }
        break;

    case "delete":

        $deleteTarefa = "delete tarefas";

        http_response_code(200);
        echo json_encode(['status' => 'ok', 'tarefas' => $deleteTarefa]);
        break;

    default:
        http_response_code(404);
        echo json_encode(['status' => 'error', 'mensagem' => 'Ação não encontrada']);

}

?>