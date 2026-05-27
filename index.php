<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tarefas</title>
  <link rel="stylesheet" href="./recursos/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>

<body>
  <div class="container_base">
    <header class="header">
      <h1 class="logo">tarefas</h1>
      <p class="subtitle">organize seu dia</p>
    </header>

    <!-- Formulário de nova tarefa -->
    <form class="form" id="form-add">
      <input type="text" class="input" id="input-title" placeholder="buscar tarefa..." autocomplete="off" />
      <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#exampleModal"
        id="adicionar_btn">+</button>
    </form>

    <!-- Filtros -->
    <div class="filters">
      <button class="filter active" data-filter="all">todas</button>
      <button class="filter" data-filter="pending">pendentes</button>
      <button class="filter" data-filter="done">concluídas</button>
    </div>

    <!-- Lista de tarefas completo-->
    <ul class="task-list" id="task-list"></ul>

    <!-- Lista de tarefas concluidas-->
    <ul class="task-list d-none" id="task-list-concluidas"></ul>

    <!-- Lista de tarefas pendentes-->
    <ul class="task-list d-none" id="task-list-pendentes"></ul>

    <!-- Mensagem de feedback (erros, sucesso) -->
    <div class="toast" id="toast"></div>

    <!-- MODAL DE EDIÇÃO -->
    <div class="modal fade" id="modal-editar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalEditarLabel">Editar Tarefa</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit-id">

            <div class="form-group mb-3">
              <label for="edit-titulo" class="form-label">Título da Tarefa</label>
              <input type="text" id="edit-titulo" class="form-control" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="edit-prioridade" class="form-label">Prioridade</label>
              <select id="edit-prioridade" class="form-select">
                <option value="alta">Alta</option>
                <option value="media">Média</option>
                <option value="baixa">Baixa</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btn-salvar-edicao">Salvar Alterações</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL DE DELETAR -->
    <div class="modal fade" id="modal-deletar" tabindex="-1" aria-labelledby="modalDeletarLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalDeletarLabel">Confirmar Exclusão</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <p>Tem certeza que deseja excluir esta tarefa?</p>
            <p class="text-danger mb-0">Esta ação não poderá ser desfeita.</p>
            <input type="hidden" id="delete-id">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="btn-confirmar-delecao">Excluir</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL DE CRIAÇÃO -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Criar Tarefa</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form method="POST" id="CriarTarefa">
            <div class="modal-body">
              <div class="mb-3">
                <label for="titulo" class="form-label">Nome da Tarefa</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
              </div>

              <div class="mb-3">
                <label for="prioridade" class="form-label">Prioridade</label>
                <select id="prioridade" name="prioridade" class="form-select">
                  <option value="1">Baixa</option>
                  <option value="2">Média</option>
                  <option value="3">Alta</option>
                </select>
              </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Criar</button>
            </div>
          </form>
          
        </div>
      </div>
    </div>

    <script src="./recursos/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"></script>

</body>

</html>