# 📝 Gerenciador de Tarefas (To-Do List)

> ⚠️ **Nota de Escopo:** Este é um projeto desenvolvido exclusivamente para **fins estudantis** e didáticos. O objetivo principal foi consolidar conhecimentos em integração de sistemas usando JavaScript Moderno (Vanilla JS) no Front-end e PHP Puro no Back-end, sem o uso de frameworks externos.

Uma aplicação simples, responsiva e funcional de lista de tarefas para ajudar na organização do dia a dia. O projeto simula o ciclo completo de uma aplicação real, realizando requisições assíncronas para persistência de dados local.

---

## 🚀 Tecnologias Aprendidas e Aplicadas

O desenvolvimento deste ecossistema envolveu o aprendizado prático de diversas tecnologias e conceitos de arquitetura:

### **Front-end**
* **HTML5 Semântico:** Estruturação da página priorizando acessibilidade (A11y) e boas práticas de leitura para leitores de tela.
* **CSS3 Customizado:** Estilização modular moderna usando variáveis globais e flexbox para garantir responsividade nativa.
* **JavaScript Assíncrono (ES6+):** * Uso de `async/await` e API `fetch` para comunicação com o servidor.
    * **Delegação de Eventos (Event Delegation):** Implementação de ouvintes de eventos otimizados no elemento pai (`<ul>`) para gerenciar dinamicamente elementos gerados via API.
    * Manipulação eficiente do DOM em memória através do `DocumentFragment`.

### **Back-end & Banco de Dados**
* **PHP 8 (Puro):** Criação de uma API RESTful simplificada utilizando estruturas de controle como `switch/case`.
* **Manipulação de Streams (`php://input`):** Aprendizado crítico sobre como capturar e fazer o `json_decode()` de payloads brutos em formato JSON enviados pelo Front-end.
* **Passagem por Referência (`&$variavel`):** Uso do operador `&` para modificar diretamente itens de coleções em memória dentro de loops.
* **Banco de Dados JSON:** Armazenamento, leitura (`file_get_contents`) e escrita (`file_put_contents`) de dados persistidos diretamente em um arquivo estruturado `db.json`.

---

## ⚙️ Funcionalidades Implementadas

* [x] **Listagem Dinâmica:** Renderização assíncrona das tarefas direto do "banco de dados" local.
* [x] **Gerenciamento de Estado (Conclusão):** O clique no checkbox risca o título da tarefa via CSS, desabilita os botões de ação e salva o novo estado instantaneamente no servidor.
* [x] **Registro de Logs de Tempo:** O servidor carimba automaticamente a data e hora exata de modificação sempre que uma tarefa sofre alterações.
* [ ] **Criação de Tarefas** *(Em desenvolvimento)*
* [ ] **Edição de Títulos com Lápis** *(Em desenvolvimento)*
* [ ] **Exclusão de Tarefas com Lixeira** *(Em desenvolvimento)*
