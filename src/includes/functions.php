<?php

function buscarTarefas()
{

    global $conn;

    $sql = " SELECT rt.id,
 		         rt.titulo, 
                 rt.concluida, 
                 rt.criado, 
                 rt.atualizado_em, 
                 tp.prioridade 
	FROM tarefas.registro_tarefa AS rt
	JOIN tarefas.prioridades tp ON tp.id = rt.prioridade
    WHERE arquivado = 0;";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Falha ao preparar buscarTarefas: " . $conn->error);
        return [];
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    $dadosProcessados = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    http_response_code(200);
    return json_encode(['status' => 'ok', 'tarefas' => $dadosProcessados]);

}
function atualizartarefas($recurso, $dadosRecebidos)
{

    if (!is_array($dadosRecebidos)) {
        $dadosRecebidos = [];
    }

    $id = $dadosRecebidos['id'] ?? null;
    $titulo = $dadosRecebidos['titulo'] ?? null;
    $concluida = $dadosRecebidos['concluida'] ?? false;
    $atualizado_em = $dadosRecebidos['atualizado_em'] ?? null;
    $prioridade = $dadosRecebidos['prioridade'] ?? null;

    global $conn;

    switch ($recurso) {

        case "status":

            if (empty($id) || !isset($concluida)) {
                http_response_code(400);
                return json_encode(['status' => 'error', 'mensagem' => 'ID e status são obrigatórios!']);
            }

            $ehVerdadeiro = filter_var($concluida, FILTER_VALIDATE_BOOLEAN);
            $novoStatus = $ehVerdadeiro ? 1 : 0;

            $sql = "UPDATE tarefas.registro_tarefa SET concluida = ? , atualizado_em = now() WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                http_response_code(500);
                return json_encode(['status' => 'error', 'mensagem' => 'Erro na preparação da query.']);
            }

            $stmt->bind_param("ii", $novoStatus, $id);

            if ($stmt->execute()) {
                $stmt->close();
                http_response_code(200);
                return json_encode(['status' => 'ok', 'mensagem' => 'Tarefa atualizada com sucesso!']);
            }

            $stmt->close();
            http_response_code(500);
            return json_encode(['status' => 'error', 'mensagem' => 'Erro ao atualizar no banco de dados.']);


        case "tarefa":

            if (empty($id) || empty($titulo) || empty($prioridade)) {
                http_response_code(400);
                return json_encode(['status' => 'error', 'mensagem' => 'ID , titulo e prioridadee são obrigatórios!']);
            }


            $prioridadeBase = [
                "alta" => 3,
                "media" => 2,
                "baixa" => 1,
            ];

            if (array_key_exists($prioridade, $prioridadeBase)) {
                $prioridade = $prioridadeBase[$prioridade];
            }

            $sql = "UPDATE tarefas.registro_tarefa SET titulo = ? , prioridade = ? ,atualizado_em = now() WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                http_response_code(500);
                return json_encode(['status' => 'error', 'mensagem' => 'Erro na preparação da query.']);
            }

            $stmt->bind_param("sii", $titulo, $prioridade, $id);

            if ($stmt->execute()) {
                $stmt->close();
                http_response_code(200);
                return json_encode(['status' => 'ok', 'mensagem' => 'Tarefa atualizada com sucesso!']);
            }

            $stmt->close();
            http_response_code(500);
            return json_encode(['status' => 'error', 'mensagem' => 'Erro ao atualizar no banco de dados.']);

        case "arquivar":

            if (empty($id)) {
                http_response_code(400);
                return json_encode(['status' => 'error', 'mensagem' => 'ID é obrigatório!']);
            }

            $sql = "UPDATE tarefas.registro_tarefa SET arquivado = 1, atualizado_em = now() WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                http_response_code(500);
                return json_encode(['status' => 'error', 'mensagem' => 'Erro na preparação da query.']);
            }

            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $stmt->close();
                http_response_code(200);
                return json_encode(['status' => 'ok', 'mensagem' => 'Tarefa deletada com sucesso!']);
            }

            $stmt->close();
            http_response_code(500);
            return json_encode(['status' => 'error', 'mensagem' => 'Erro ao atualizar no banco de dados.']);

        default:
            http_response_code(400);
            return json_encode(['status' => 'error', 'mensagem' => 'Recurso de atualização inválido']);

    }
}

function criarTarefas($recurso, $dadosRecebidos){

 if (!is_array($dadosRecebidos)) {
        $dadosRecebidos = [];
    }

    $titulo = $dadosRecebidos['titulo'] ?? null;
    $prioridade = $dadosRecebidos['prioridade'] ?? null;

    global $conn;

     if (empty($titulo) || empty($prioridade)) {
                http_response_code(400);
                return json_encode(['status' => 'error', 'mensagem' => 'Titulo e prioridadee são obrigatórios!']);
            }

            $sql = "INSERT INTO registro_tarefa (titulo, prioridade, criado)
                    VALUES ( ?, ?, NOW()); ";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                http_response_code(500);
                return json_encode(['status' => 'error', 'mensagem' => 'Erro na preparação da query.']);
            }

            $stmt->bind_param("si", $titulo, $prioridade);

            if ($stmt->execute()) {
                $stmt->close();
                http_response_code(200);
                return json_encode(['status' => 'ok', 'mensagem' => 'Tarefa criada com sucesso!']);
            }

            $stmt->close();
            http_response_code(500);
            return json_encode(['status' => 'error', 'mensagem' => 'Erro ao criar tarefa no banco de dados.']);


}