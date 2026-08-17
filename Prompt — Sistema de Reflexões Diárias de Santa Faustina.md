# Prompt — Sistema de Reflexões Diárias de Santa Faustina

## 1. Contexto

Vamos desenvolver do zero um sistema web chamado:

**Reflexões Diárias de Santa Faustina**

O projeto atualmente **não possui Laravel instalado**. Portanto, a primeira etapa será inicializar corretamente o projeto Laravel e preparar todo o ambiente necessário.

O objetivo do sistema é disponibilizar uma reflexão diária baseada no Diário de Santa Faustina.

Na página inicial do site, o usuário deverá visualizar automaticamente a reflexão cadastrada para o dia atual.

O sistema também terá uma área administrativa protegida por autenticação, onde o administrador poderá cadastrar, editar, excluir e consultar as reflexões diárias.

---

# 2. Stack obrigatória

Utilize as seguintes tecnologias:

- PHP 8.5
- Laravel 13
- MySQL
- Docker / Docker Compose para o banco de dados
- Blade
- Tailwind CSS, utilizando a integração padrão/recomendada pelo Laravel
- Vite, conforme configuração padrão do Laravel
- Eloquent ORM
- Migrations
- Seeders
- Factories, caso sejam úteis
- PHPUnit/Pest, conforme o padrão adotado pela versão do Laravel
- Git

Não utilize frameworks frontend como Vue, React ou Angular. A aplicação deve ser simples e utilizar principalmente **Laravel + Blade + Tailwind CSS**.

---

# 3. Inicialização do projeto

Como o Laravel ainda não está instalado no projeto, faça a inicialização completa.

Antes de implementar as funcionalidades:

1. Verifique a versão disponível do PHP.
2. Verifique se o Composer está disponível.
3. Crie um novo projeto utilizando Laravel 13.
4. Configure o arquivo `.env`.
5. Configure a conexão com MySQL.
6. Crie a infraestrutura Docker para o MySQL.
7. Faça o banco de dados subir corretamente.
8. Execute as migrations.
9. Configure o frontend com Vite/Tailwind conforme o padrão do Laravel 13.
10. Verifique se a aplicação inicia corretamente.

Caso seja necessário instalar dependências adicionais, utilize as versões compatíveis com Laravel 13 e PHP 8.5.

---

# 4. Docker / MySQL

Crie uma configuração Docker adequada para desenvolvimento.

Preferencialmente utilize:

- MySQL 8.x
- Volume persistente para os dados
- Porta configurável por variável de ambiente
- Database configurado por variável de ambiente
- Usuário configurável
- Senha configurável

Crie um arquivo:

```text
docker-compose.yml
```

ou outra estrutura equivalente caso considere tecnicamente melhor.

O projeto deve permitir iniciar o banco utilizando algo equivalente a:

```bash
docker compose up -d
```

Não coloque senhas reais no repositório.

Crie ou mantenha um `.env.example` documentando as variáveis necessárias.

---

# 5. Funcionalidade principal

A aplicação deverá possuir uma página pública inicial.

Rota:

```text
/
```

Ao acessar a página inicial, o sistema deverá:

1. Identificar a data atual.
2. Buscar no banco de dados a reflexão correspondente à data atual.
3. Exibir o número do parágrafo do Diário de Santa Faustina.
4. Exibir a reflexão.
5. Apresentar uma interface agradável, limpa e apropriada para o contexto religioso.

Exemplo conceitual:

```text
Reflexão do Dia

Diário de Santa Faustina
Parágrafo 123

"[texto da reflexão]"
```

A página não deve exigir login.

---

# 6. Banco de dados

Crie uma tabela para armazenar as reflexões diárias.

Nome sugerido:

```text
daily_reflections
```

A tabela deverá possuir, no mínimo:

```text
id
paragrafo
reflexao
dia
created_at
updated_at
deleted_at
```

Utilize:

- `id` como chave primária
- `paragrafo` para armazenar o número do parágrafo do Diário
- `reflexao` para armazenar o conteúdo da reflexão
- `dia` para representar o dia em que a reflexão será exibida
- `created_at`
- `updated_at`
- `deleted_at` utilizando Soft Deletes

Analise os tipos de dados adequados para cada campo.

Sugestão:

```text
paragrafo -> inteiro
reflexao -> texto longo
dia -> date
```

Adicione índices quando fizer sentido.

---

# 7. Regra importante para a data

Cada reflexão deverá estar associada a uma data específica.

Não deve existir mais de uma reflexão ativa para o mesmo dia.

Implemente essa regra de forma adequada.

Considere:

- validação na aplicação;
- índice/constraint no banco, quando compatível com Soft Delete;
- comportamento de registros excluídos.

Uma reflexão excluída via Soft Delete não deve aparecer na página pública.

---

# 8. Model

Crie o model:

```text
DailyReflection
```

Utilize:

```php
use Illuminate\Database\Eloquent\SoftDeletes;
```

Configure corretamente:

- `$fillable`
- casts
- SoftDeletes
- relacionamentos, caso futuramente sejam necessários

O campo `dia` deve possuir cast apropriado para data.

---

# 9. Área administrativa

O sistema deverá possuir uma área administrativa protegida por autenticação.

Sugestão:

```text
/admin
```

ou:

```text
/admin/reflections
```

O administrador deverá conseguir:

- visualizar todas as reflexões;
- cadastrar uma nova reflexão;
- editar uma reflexão;
- excluir uma reflexão;
- visualizar os dados da reflexão;
- pesquisar/filtrar por data;
- pesquisar/filtrar por parágrafo.

A interface deve ser simples e responsiva.

---

# 10. Autenticação

Utilize a solução de autenticação recomendada pelo próprio Laravel 13 para uma aplicação Blade simples.

Não implemente um sistema de autenticação manual se existir uma solução oficial/recomendada adequada.

O administrador deverá possuir:

- login;
- logout;
- proteção das rotas administrativas através de middleware de autenticação.

A área pública não deve exigir autenticação.

---

# 11. Usuário administrador

Crie um Seeder para criar um usuário administrador inicial.

Não deixe uma senha real hardcoded diretamente no código de produção.

Utilize uma estratégia apropriada para desenvolvimento, por exemplo:

- variáveis no `.env`;
- configuração documentada;
- ou outra solução segura.

Documente claramente como criar/acessar o primeiro usuário administrador.

---

# 12. CRUD das reflexões

Implemente o CRUD completo.

## Listagem

A listagem deve apresentar pelo menos:

- ID
- Data
- Parágrafo
- Resumo da reflexão
- Data de criação
- Ações

Ações:

```text
Visualizar
Editar
Excluir
```

Não exiba a reflexão inteira na tabela caso ela seja muito grande.

Utilize paginação.

---

## Cadastro

Formulário contendo:

### Parágrafo

Campo numérico.

Obrigatório.

Deve aceitar apenas valores válidos.

### Reflexão

Campo de texto longo.

Obrigatório.

### Dia

Campo de data.

Obrigatório.

Não permitir cadastro de duas reflexões ativas para a mesma data.

---

## Edição

Permitir alterar:

- parágrafo;
- reflexão;
- dia.

Manter as mesmas regras de validação do cadastro.

---

## Exclusão

Utilizar Soft Delete.

Antes de excluir, solicitar confirmação ao usuário.

---

# 13. Validação

Utilize Form Requests do Laravel sempre que fizer sentido.

Crie validações claras.

Exemplo conceitual:

```text
paragrafo:
required
integer
min:1

reflexao:
required
string

dia:
required
date
```

A regra de unicidade da data também deve ser implementada corretamente.

As mensagens de validação devem estar em português.

---

# 14. Página pública

A página inicial deve possuir uma aparência agradável e minimalista.

Sugestão visual:

- fundo claro;
- tipografia elegante;
- título "Reflexão do Dia";
- referência ao Diário de Santa Faustina;
- número do parágrafo em destaque;
- texto da reflexão centralizado ou com boa largura de leitura;
- layout responsivo;
- boa experiência em celular;
- rodapé simples.

Evite uma interface exageradamente complexa.

O foco deve ser o conteúdo da reflexão.

---

# 15. Comportamento quando não existir reflexão para o dia

Caso não exista uma reflexão cadastrada para a data atual, a página inicial não deve apresentar erro.

Exibir uma mensagem amigável, por exemplo:

```text
Ainda não há uma reflexão cadastrada para hoje.
Volte mais tarde.
```

Não utilize uma reflexão de outro dia automaticamente.

A reflexão exibida deve obrigatoriamente corresponder à data atual.

---

# 16. Timezone

Configure corretamente o timezone da aplicação.

Como o sistema será utilizado no Brasil, utilize:

```text
America/Sao_Paulo
```

Certifique-se de que a determinação da "reflexão de hoje" utilize o timezone configurado pela aplicação.

Não utilize diretamente o timezone UTC para determinar o dia da reflexão.

---

# 17. Rotas

Organize as rotas de forma limpa.

Exemplo:

```text
GET  /                         -> página pública
GET  /admin                    -> dashboard administrativo

GET  /admin/reflections       -> listagem
GET  /admin/reflections/create -> formulário
POST /admin/reflections       -> criação
GET  /admin/reflections/{id}  -> visualização
GET  /admin/reflections/{id}/edit -> edição
PUT/PATCH /admin/reflections/{id} -> atualização
DELETE /admin/reflections/{id} -> exclusão
```

Utilize Route Model Binding quando apropriado.

Proteja as rotas administrativas com autenticação.

---

# 18. Controller

Utilize Controllers organizados.

Sugestão:

```text
HomeController
Admin/DailyReflectionController
```

Evite colocar regra de negócio complexa diretamente nas Views.

Também evite controllers excessivamente grandes.

---

# 19. Views

Organize as views de forma limpa.

Sugestão:

```text
resources/views/
├── layouts/
├── home/
│   └── index.blade.php
├── auth/
└── admin/
    ├── dashboard.blade.php
    └── reflections/
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── show.blade.php
```

Adapte a estrutura se a solução oficial de autenticação escolhida pelo Laravel recomendar outra organização.

Utilize componentes Blade quando ajudarem a evitar repetição.

---

# 20. Dashboard administrativo

Crie um dashboard simples.

Ele pode apresentar:

```text
Reflexões cadastradas
Reflexões para os próximos dias
Reflexões sem cadastro
```

Não complique o dashboard.

O objetivo principal é permitir o gerenciamento das reflexões.

---

# 21. Seed de dados de exemplo

Crie um Seeder com algumas reflexões fictícias/de exemplo apenas para testar a aplicação.

IMPORTANTE:

Não invente citações e apresente-as como se fossem textos reais de Santa Faustina.

Caso seja necessário utilizar dados de exemplo, deixe claramente indicado que são dados fictícios.

O sistema deve permitir posteriormente inserir o conteúdo real.

---

# 22. Testes

Crie testes automatizados para os principais comportamentos.

No mínimo, teste:

### Página inicial

- retorna HTTP 200;
- exibe a reflexão correspondente ao dia;
- não exibe reflexão de outro dia;
- comportamento quando não existe reflexão para hoje.

### CRUD

- administrador autenticado consegue acessar a listagem;
- usuário não autenticado não consegue acessar área administrativa;
- administrador consegue criar reflexão;
- administrador consegue editar reflexão;
- administrador consegue excluir reflexão;
- reflexão excluída não aparece na página pública.

### Validação

- parágrafo obrigatório;
- reflexão obrigatória;
- data obrigatória;
- não permitir duas reflexões ativas para a mesma data.

---

# 23. Segurança

Siga as boas práticas do Laravel.

Verifique:

- CSRF;
- autenticação;
- autorização;
- mass assignment;
- validação de entrada;
- escaping automático das Blade Views;
- proteção das rotas administrativas;
- não exposição de credenciais;
- não utilização de `APP_DEBUG=true` em produção;
- tratamento adequado de erros.

Não utilize `{!! !!}` para conteúdo proveniente do usuário sem necessidade.

---

# 24. README

Crie um `README.md` completo explicando:

## Requisitos

- PHP 8.5
- Composer
- Docker
- Docker Compose
- Node.js/NPM, se necessário

## Instalação

Exemplo:

```bash
composer install
```

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

Subir MySQL:

```bash
docker compose up -d
```

Executar migrations:

```bash
php artisan migrate
```

Executar seed:

```bash
php artisan db:seed
```

Instalar frontend:

```bash
npm install
```

Executar:

```bash
npm run dev
```

E também explicar como executar a aplicação Laravel.

---

# 25. Configuração de desenvolvimento

Se necessário, disponibilize comandos simples para iniciar o ambiente.

Por exemplo:

```bash
docker compose up -d
php artisan migrate --seed
npm run dev
```

Caso exista uma forma melhor ou mais integrada de executar o ambiente no Laravel 13, utilize-a e documente no README.

---

# 26. Qualidade do código

O código deve seguir boas práticas do Laravel.

Priorize:

- código simples;
- SOLID quando aplicável;
- separação de responsabilidades;
- nomes claros;
- Controllers enxutos;
- Form Requests para validações;
- Eloquent;
- Blade Components;
- migrations bem estruturadas;
- testes;
- código legível.

Não crie abstrações desnecessárias para um sistema tão pequeno.

Evite overengineering.

---

# 27. Antes de implementar

Antes de começar a criar os arquivos, faça uma análise do projeto e verifique:

1. Se o diretório está vazio ou se já existem arquivos.
2. Qual versão do PHP está instalada.
3. Qual versão do Composer está instalada.
4. Se Docker está disponível.
5. Se Node/NPM estão disponíveis.
6. Se existe alguma configuração prévia que deve ser preservada.

Depois dessa análise, inicialize o Laravel 13.

---

# 28. Execução incremental

Implemente o projeto em etapas:

## Etapa 1 — Bootstrap

- Laravel 13
- PHP 8.5
- Docker
- MySQL
- `.env`
- `.env.example`
- Vite
- Tailwind
- README inicial

## Etapa 2 — Banco

- migration
- model
- SoftDeletes
- factory
- seeders

## Etapa 3 — Autenticação

- instalação/configuração da autenticação Laravel
- login
- logout
- usuário administrador
- proteção das rotas

## Etapa 4 — Administração

- dashboard
- CRUD de reflexões
- validações
- paginação
- filtros
- mensagens de sucesso/erro

## Etapa 5 — Página pública

- busca da reflexão do dia
- layout
- estado sem reflexão
- responsividade

## Etapa 6 — Testes

- testes unitários/feature
- testes de autenticação
- testes do CRUD
- testes da reflexão diária

## Etapa 7 — Revisão

Após implementar tudo:

- execute os testes;
- execute migrations do zero em um banco limpo;
- execute seeders;
- verifique o CRUD manualmente;
- verifique a página pública;
- verifique a autenticação;
- verifique o Soft Delete;
- verifique a regra de uma reflexão por dia;
- verifique o timezone;
- verifique possíveis erros ou warnings;
- revise o código procurando problemas de segurança.

---

# 29. Regra importante sobre conteúdo religioso

O sistema é apenas uma plataforma para cadastro e exibição do conteúdo.

Não tente gerar, completar ou alterar automaticamente textos atribuídos a Santa Faustina.

O conteúdo deverá ser fornecido pelo administrador.

Quando houver conteúdo de exemplo, deixe explicitamente claro que é fictício.

---

# 30. Resultado esperado

Ao finalizar, quero ter um projeto Laravel 13 completamente funcional.

Deve ser possível:

1. subir o MySQL com Docker;
2. configurar o `.env`;
3. executar migrations;
4. criar o administrador;
5. acessar o login;
6. entrar no painel administrativo;
7. cadastrar uma reflexão para determinada data;
8. editar a reflexão;
9. excluir a reflexão;
10. acessar `/`;
11. visualizar automaticamente a reflexão cadastrada para o dia atual.

O projeto deve estar organizado para que futuramente seja fácil adicionar funcionalidades como:

- histórico de reflexões;
- busca;
- calendário;
- compartilhamento da reflexão;
- favoritos;
- notificações;
- API;
- aplicativo mobile.

Porém, **não implemente essas funcionalidades agora**. Deixe apenas a arquitetura suficientemente organizada para permitir sua inclusão posteriormente.

---

# 31. Regra final para o Claude

Não apenas descreva como fazer.

**Implemente o sistema diretamente no projeto.**

Sempre que possível:

- crie os arquivos;
- execute os comandos;
- instale as dependências;
- execute migrations;
- execute testes;
- corrija os problemas encontrados;
- valide o funcionamento.

Ao final, apresente um resumo contendo:

1. O que foi implementado;
2. Estrutura principal criada;
3. Comandos para executar o projeto;
4. Credenciais/instruções para acessar o administrador em ambiente de desenvolvimento;
5. Testes executados e seus resultados;
6. Qualquer pendência encontrada.

Não considere a tarefa concluída apenas porque os arquivos foram criados.

**A tarefa somente estará concluída após validar que a aplicação inicializa, conecta ao MySQL, executa as migrations, autentica o administrador e exibe corretamente a reflexão do dia.**