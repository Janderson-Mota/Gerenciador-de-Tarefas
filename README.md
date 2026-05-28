# Gerenciador de Tarefas (To-Do List)
#### Nota de Escopo: Este é um projeto desenvolvido exclusivamente para fins estudantis e didáticos. O objetivo principal foi consolidar conhecimentos em integração de sistemas usando JavaScript Moderno (Vanilla JS) no Front-end e PHP Puro no Back-end, sem o uso de frameworks externos de reatividade.

Uma aplicação simples, responsiva e funcional de lista de tarefas para ajudar na organização do dia a dia. O projeto simula o ciclo completo de uma aplicação real, realizando requisições assíncronas para persistência de dados locais, além de contar com sistema de busca e filtros dinâmicos de interface.

## Tecnologias Aprendidas e Aplicadas
O desenvolvimento deste ecossistema envolveu o aprendizado prático de diversas tecnologias e conceitos de arquitetura:

#### Front-end
HTML5 e CSS3: Estruturação semântica e estilização utilizando componentes do Bootstrap (como Modais e Badges) combinados com manipulação nativa de classes utilitárias para controle de estado visual.

JavaScript Assíncrono (ES6+): Uso de async/await e API fetch para comunicação com o servidor e envio de métodos HTTP.

Manipulação e Otimização do DOM: Renderização performática com DocumentFragment, delegação de eventos (Event Delegation) nos contêineres principais e alternância de visibilidade via manipulação do objeto classList.

Algoritmos de Filtro Client-side: Implementação de motor de busca em tempo real escutando o evento de input com o método includes(), além de gerenciamento de estado das abas de navegação.

#### Back-end & Banco de Dados
PHP 8 (Puro): Criação de uma API RESTful simplificada atuando como controlador de rotas baseadas nos parâmetros e métodos de requisição (GET, POST, PUT, DELETE).

Manipulação de Streams: Captura e processamento de payloads JSON brutos enviados pelo cliente utilizando php://input.

Passagem por Referência: Uso do operador &$variavel para modificar diretamente itens de coleções na memória durante a manipulação dos dados.

Persistência em Arquivo JSON: Simulação de banco de dados através da leitura (file_get_contents) e escrita (file_put_contents) em um arquivo estruturado db.json.

## Funcionalidades Implementadas
[x] Listagem Dinâmica: Renderização assíncrona das tarefas consumidas da API na inicialização da página.

[x] Criação de Tarefas: Formulário para adição de novos itens, incluindo níveis de prioridade, com atualização na base de dados e no DOM.

[x] Gerenciamento de Estado (Conclusão): O clique no checkbox altera o estado visual da tarefa, trava ações de edição/exclusão e dispara a requisição de atualização (PUT) para a API.

[x] Edição e Exclusão: Interface com modais de confirmação para editar os atributos da tarefa ou removê-la permanentemente.

[x] Sistema de Abas (Filtros): Segmentação da visualização em painéis para "Todas", "Pendentes" e "Concluídas", sem necessidade de recarregar a página.

[x] Busca em Tempo Real: Campo de pesquisa que filtra as tarefas instantaneamente pela correspondência de texto no título.

[x] Registro de Logs Temporais: O back-end registra automaticamente metadados com data e hora exatas de cada operação de criação ou modificação de status.
