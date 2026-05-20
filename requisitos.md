# Gerenciador de Tarefas — Requisitos do Projeto

> Full stack · JS puro + PHP puro · Nível intermediário

---

## Restrições técnicas

| Item | Decisão |
|---|---|
| Frontend | JavaScript puro (sem frameworks) |
| Backend | PHP puro (sem Laravel/Slim) |
| Persistência | Arquivo JSON local |
| Banco de dados | Não utilizado |

---

## RF — Requisitos Funcionais

### RF-01 · Listar tarefas `JS + PHP`

Ao carregar a página, o JS faz um GET para a API PHP que retorna todas as tarefas em JSON. A lista é renderizada no DOM sem recarregar a página.

### RF-02 · Criar tarefa `JS + PHP`

O usuário digita um título e clica em adicionar. O JS envia um POST com os dados; o PHP valida, gera um ID único, salva no JSON e retorna a tarefa criada.

### RF-03 · Marcar como concluída `JS + PHP`

Ao clicar no checkbox, o JS envia um POST de atualização. O PHP altera o campo `done` no JSON e devolve a tarefa atualizada.

### RF-04 · Editar tarefa `JS + PHP`

Clique duplo no título abre um campo de edição inline. Ao confirmar, o JS envia o novo texto; o PHP atualiza o registro no JSON.

### RF-05 · Excluir tarefa `JS + PHP`

Botão de exclusão envia DELETE (via POST com `_method=delete`). O PHP remove o item do JSON e confirma com status 200.

### RF-06 · Filtrar tarefas `JS`

Botões "Todas", "Pendentes" e "Concluídas" filtram a lista localmente no JS, sem nova requisição ao servidor.

---

## RNF — Requisitos Não Funcionais

### RNF-01 · Comunicação via JSON

Toda resposta da API deve ter o header `Content-Type: application/json` e retornar um objeto com os campos `status` e `data`.

### RNF-02 · Sem recarregamento de página

Todas as operações CRUD devem usar `fetch()`. Nenhuma ação deve causar reload ou redirect.

### RNF-03 · Tratamento de erros

O PHP deve retornar erros com HTTP status code adequado (400, 404, 500). O JS deve exibir mensagens de feedback para o usuário em caso de falha.

### RNF-04 · Validação nos dois lados

O JS valida campos obrigatórios antes de enviar. O PHP revalida os dados recebidos e rejeita entradas inválidas, nunca confiando apenas no frontend.

---

## Estrutura de dados — Tarefa

```json
{
  "id": "uuid-gerado-pelo-php",
  "title": "Título da tarefa",
  "done": false,
  "created_at": "2026-05-17T10:00:00"
}
```

---

## Endpoints — `api.php`

| Método | Parâmetro | Ação |
|---|---|---|
| GET | `?action=list` | Lista todas as tarefas |
| POST | `?action=create` | Cria nova tarefa |
| POST | `?action=update` | Atualiza status ou texto |
| POST | `?action=delete` | Remove uma tarefa |

---

## Estrutura de arquivos

```
todo-app/
├── index.html
├── style.css
├── app.js          ← JS puro (fetch, DOM, eventos)
├── api.php         ← PHP puro (CRUD, lê/grava JSON)
└── tasks.json      ← Persistência local
```

---

## Desafios extras — opcionais

### EX-01 · Prioridade

Campo com três níveis (baixa, média, alta) com cores distintas na interface.

### EX-02 · Prazo com alerta

Data limite por tarefa. JS destaca visualmente tarefas vencidas comparando com `Date.now()`.

### EX-03 · Busca em tempo real

Campo de busca que filtra a lista localmente a cada tecla pressionada, usando o evento `input`.











// // Instante atual
// let agora = new Date();

// console.log(agora);

// // Formatar para o fuso de São Paulo
// const formatoSP = new Intl.DateTimeFormat('pt-BR', {
//   timeZone: 'America/Sao_Paulo',
//   dateStyle: 'full',
//   timeStyle: 'short'
// });

// console.log('São Paulo:', formatoSP.format(agora));








2xx — Sucesso

200 OK → requisição bem-sucedida (listar, atualizar, deletar)
201 Created → tarefa criada com sucesso

4xx — Erro do cliente

400 Bad Request → dado inválido (ex: título vazio)
404 Not Found → tarefa com aquele ID não existe

5xx — Erro do servidor

500 Internal Server Error → falha ao ler/gravar o tasks.json