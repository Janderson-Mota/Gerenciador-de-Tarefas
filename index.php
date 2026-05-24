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
      <input type="text" class="input" id="input-title" placeholder="nova tarefa..." autocomplete="off" />
      <button type="submit" class="btn-add" id="adicionar_btn">+</button>
    </form>

    <!-- Filtros -->
    <div class="filters">
      <button class="filter active" data-filter="all">todas</button>
      <button class="filter" data-filter="pending">pendentes</button>
      <button class="filter" data-filter="done">concluídas</button>
    </div>

    <!-- Lista de tarefas -->
    <ul class="task-list" id="task-list"></ul>

    <!-- Rodapé com contagem -->
    <div class="d-flex justify-content-end">
      <div class="alert alert-light p-1 w-25" id="contagem" role="alert">
        <span id="count"></span>
      </div>
    </div>


    <!-- Mensagem de feedback (erros, sucesso) -->
    <div class="toast" id="toast"></div>

    <!-- MODAL DE EDIÇÃO -->
    <div id="modal-editar" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Editar Tarefa</h3>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit-id">

          <div class="form-group">
            <label for="edit-titulo">Título da Tarefa</label>
            <input type="text" id="edit-titulo" class="form-control" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="edit-prioridade">Prioridade</label>
            <select id="edit-prioridade" class="form-control">
              <option value="alta">Alta</option>
              <option value="media">Média</option>
              <option value="baixa">Baixa</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary fechar-modal">Cancelar</button>
          <button class="btn btn-primary" id="btn-salvar-edicao">Salvar Alterações</button>
        </div>
      </div>
    </div>

    <!-- MODAL DE EXCLUSÃO -->
    <div id="modal-deletar" class="modal-overlay">
      <div class="modal-content modal-sm">
        <div class="modal-header">
          <h3>Confirmar Exclusão</h3>
        </div>
        <div class="modal-body">
          <p>Tem certeza que deseja excluir esta tarefa?</p>
          <p class="text-danger">Esta ação não poderá ser desfeita.</p>
          <input type="hidden" id="delete-id">
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary fechar-modal">Cancelar</button>
          <button class="btn btn-danger" id="btn-confirmar-delecao">Excluir</button>
        </div>
      </div>
    </div>

    <script src="./recursos/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"></script>

</body>

</html>