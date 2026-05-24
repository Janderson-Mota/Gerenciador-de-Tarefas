// ELEMENTOS GLOBAIS
const adicionar = document.getElementById("adicionar_btn");
const listaTarefas = document.getElementById("task-list");
const contagemTarefas = document.getElementById("count");
const fecharModal = document.getElementsByClassName('#fechar-modal');

// CAMINHOS DA API
const urlApi = "api.php";

// RETORNA A CLASSE DO BADGE CONFORME E PRIORIDADE
const getBadgeClass = (prioridade) => {
  const classes = {
    alta: "text-bg-danger",
    media: "text-bg-warning",
    baixa: "text-bg-success",
  };
  return `badge ${classes[prioridade] ?? "text-bg-secondary"}`;
};

// RENDERIZAR UMA TAREFA COMO ELEMENTO <li>
const renderizarTarefa = (tarefa) => {
  const li = document.createElement("li");
  li.className = "task-item";

  li.innerHTML = `

    <input type="checkbox" class="task-check" id="checkbox"  ${tarefa.concluida ? "checked" : ""} />
    <span class="task-title ${tarefa.concluida ? "concluida" : ""}">${tarefa.titulo}</span>
    <input type="text" value="${tarefa.id}" class="task-id d-none">
    <input type="text" value="${tarefa.concluida}" class="task-status d-none">
    <span class="badge  ${getBadgeClass(tarefa.prioridade)} task-prioridade">${tarefa.prioridade ?? ""}</span>
    <button class="task-edit" data-id="${tarefa.id}" aria-label="Editar tarefa">
      <svg class="icon-pencil" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 20h9"></path>
        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
      </svg>
    </button>
    <button class="task-delete" data-id="${tarefa.id}" aria-label="Excluir tarefa">
      <svg class="icon-task icon-trash" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 6 5 6 21 6"></polyline>
        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        <line x1="10" y1="11" x2="10" y2="17"></line>
        <line x1="14" y1="11" x2="14" y2="17"></line>
      </svg>
    </button>
  `;

  return li;
};

// REQUISIÇÕES
async function carregarTarefas() {
  try {
    const response = await fetch(urlApi);

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();
    const tarefas = Array.isArray(data) ? data : (data.tarefas ?? []);

    const fragmento = document.createDocumentFragment();
    tarefas.forEach((tarefa) => {
      tarefa.concluida = Number(tarefa.concluida) === 1;
      fragmento.appendChild(renderizarTarefa(tarefa));
    });

    contagemTarefas.innerHTML = `<i class="bi bi-bar-chart-line"></i> ${tarefas.length} Tarefas`;
    listaTarefas.appendChild(fragmento);
  } catch (error) {
    console.error("Erro na requisição:", error.message);
    listaTarefas.innerHTML = `<li>Não foi possível carregar as tarefas. Tente novamente mais tarde.</li>`;
  }
}

async function atualizarTarefa(tarefa) {
  try {
    const response = await fetch(tarefa);
    const result = await response.json();
  } catch (error) {
    console.error("Error:", error);
  }
}

// CHECKBOX
listaTarefas.addEventListener("change", (event) => {
  if (event.target.classList.contains("task-check")) {
    const checkboxModificado = event.target;
    const itemTarefa = checkboxModificado.closest(".task-item");
    const tituloModificado = checkboxModificado.nextElementSibling;
    const botaoEditar = itemTarefa.querySelector(".task-edit");
    const botaoDeletar = itemTarefa.querySelector(".task-delete");
    const idTarefa = itemTarefa.querySelector(".task-id").value;
    const novoStatus = checkboxModificado.checked;

    // URL PARA O STATUS
    const urlUpdateStatus = urlApi + "?recurso=status";
    const atualizarStatus = new Request(urlUpdateStatus, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: idTarefa, concluida: novoStatus }),
    });

    if (checkboxModificado.checked) {
      tituloModificado.classList.add("concluida");
      botaoEditar.disabled = true;
      botaoDeletar.disabled = true;
      atualizarTarefa(atualizarStatus);
    } else {
      tituloModificado.classList.remove("concluida");
      botaoEditar.disabled = false;
      botaoDeletar.disabled = false;
      atualizarTarefa(atualizarStatus);
    }
  }
});

// EDIÇÃO E DELETAR
listaTarefas.addEventListener("click", (event) => {
  
  const btnDeletar = event.target.closest(".task-delete");
  const modalExcluir = document.getElementById('modal-deletar');
  const btnEditar = event.target.closest(".task-edit");
  const modalEditar = document.getElementById('modal-editar');

  // FLUXO DE EXCLUSÃO
  if (btnDeletar) {
    const idTarefa = btnDeletar.dataset.id;
    const itemTarefa = btnDeletar.closest(".task-item"); 
    modalExcluir.classList.add('active');
    
  }

  // FLUXO DE EDIÇÃO
  if (btnEditar) {
    const idTarefa = btnEditar.dataset.id;
     modalEditar.classList.add('active');
  }
});

// MODAL




// INICIALIZAÇÃO
carregarTarefas();
