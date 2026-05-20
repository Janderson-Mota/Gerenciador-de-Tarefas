<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tarefas</title>
    <link rel="stylesheet" href="./src/style.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <div class="container_base">
      <header class="header">
        <h1 class="logo">tarefas</h1>
        <p class="subtitle">organize seu dia</p>
      </header>

      <!-- Formulário de nova tarefa -->
      <form class="form" id="form-add">
        <input
          type="text"
          class="input"
          id="input-title"
          placeholder="nova tarefa..."
          autocomplete="off"
        />
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
      <footer class="footer">
        <span id="count"></span>
      </footer>
    </div>

    <!-- Mensagem de feedback (erros, sucesso) -->
    <div class="toast" id="toast"></div>

    <script src="./src/main.js"></script>

  </body>
</html>
