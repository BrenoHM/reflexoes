# Prompt — Preparar projeto Laravel 13 para Deploy no Render

## Contexto

O projeto **Reflexões Diárias de Santa Faustina** já está desenvolvido em:

- PHP 8.5
- Laravel 13
- MySQL
- Blade
- Tailwind CSS
- Vite
- JavaScript
- Docker/Docker Compose para desenvolvimento local

Agora quero preparar o projeto para ser publicado em produção utilizando o **Render**.

O código será armazenado em um repositório GitHub e o Render deverá fazer o deploy automaticamente a partir desse repositório.

A arquitetura desejada inicialmente é:

```text
GitHub
   │
   ▼
Render
   │
   ▼
Laravel 13
PHP 8.5
   │
   ▼
MySQL
```

O objetivo deste trabalho é **preparar o projeto para produção**, mas não necessariamente realizar o deploy no Render ainda.

---

# 1. Regra principal

Antes de alterar qualquer coisa:

1. Analise completamente o projeto atual;
2. Identifique a estrutura do Laravel;
3. Verifique a versão do Laravel;
4. Verifique a versão do PHP;
5. Verifique as dependências do Composer;
6. Verifique as dependências NPM;
7. Analise o Docker atual;
8. Analise o `.env.example`;
9. Analise as migrations;
10. Analise como o frontend é compilado;
11. Analise se existem arquivos ou configurações específicas para desenvolvimento.

**Não recrie o projeto.**

Faça somente as alterações necessárias para torná-lo adequado para produção no Render.

---

# 2. Objetivo do ambiente

O projeto deverá funcionar em:

## Desenvolvimento

```text
Docker Compose
    ↓
Laravel
    ↓
MySQL
```

## Produção

```text
GitHub
    ↓
Render
    ↓
Laravel
    ↓
MySQL
```

Não quero perder a facilidade de desenvolvimento local.

O `docker-compose.yml` utilizado localmente deve continuar funcionando.

---

# 3. PHP

A aplicação deve utilizar:

```text
PHP 8.5
```

Garanta que o ambiente de produção utilize PHP 8.5.

Analise:

```text
composer.json
```

e verifique se existe alguma restrição de versão do PHP incompatível.

Caso seja necessário ajustar:

```json
"require": {
    "php": "^8.5"
}
```

faça isso somente se for compatível com o projeto atual.

Não altere versões de dependências sem necessidade.

---

# 4. Laravel

Confirme que o projeto está utilizando:

```text
Laravel 13
```

Não faça downgrade.

Não atualize para outra versão apenas por atualizar.

O objetivo é manter Laravel 13 + PHP 8.5.

---

# 5. Docker para produção

Crie ou adapte um:

```text
Dockerfile
```

adequado para executar o Laravel no Render.

A imagem deverá:

- utilizar PHP 8.5;
- possuir as extensões PHP necessárias;
- instalar Composer;
- instalar dependências PHP;
- instalar Node/NPM somente se necessário para o build;
- compilar os assets;
- configurar o ambiente para produção;
- executar a aplicação corretamente.

Analise quais extensões PHP o projeto realmente utiliza.

Não instale extensões desnecessárias.

---

# 6. Extensões PHP

Analise:

```text
composer.json
composer.lock
```

e o código da aplicação para identificar as extensões necessárias.

No mínimo considere verificar:

```text
pdo
pdo_mysql
mbstring
openssl
tokenizer
xml
ctype
json
fileinfo
```

e qualquer outra extensão exigida pelas dependências atuais.

A extensão necessária para MySQL deve estar habilitada.

---

# 7. Composer

No ambiente de produção, utilize:

```bash
composer install --no-dev --optimize-autoloader
```

ou uma estratégia equivalente adequada ao Laravel.

Não instale dependências de desenvolvimento desnecessariamente em produção.

Não utilize:

```text
composer update
```

durante o deploy.

O `composer.lock` deve ser respeitado.

---

# 8. NPM / Vite / Tailwind

Analise o projeto atual.

Caso o frontend utilize Vite/Tailwind, o processo de build deverá executar algo equivalente a:

```bash
npm ci
npm run build
```

Os assets de produção devem ser gerados corretamente.

Não utilizar:

```bash
npm install
```

no build de produção se `npm ci` puder ser utilizado.

Respeitar:

```text
package-lock.json
```

ou o gerenciador de pacotes atualmente utilizado pelo projeto.

Não alterar o gerenciador de pacotes sem necessidade.

---

# 9. Dockerfile — multi-stage

Avalie a utilização de um Dockerfile multi-stage.

Uma estrutura possível:

```text
Stage 1
Node
    ↓
npm ci
    ↓
npm run build

Stage 2
PHP 8.5
    ↓
Composer
    ↓
composer install --no-dev
    ↓
copiar assets compilados
    ↓
Laravel
```

O objetivo é evitar levar Node/NPM desnecessariamente para a imagem final.

Caso considere uma abordagem mais simples e adequada ao Render, pode utilizá-la.

Priorize:

- simplicidade;
- estabilidade;
- imagem razoavelmente pequena;
- facilidade de manutenção.

---

# 10. Servidor HTTP

O Render precisa conseguir iniciar corretamente a aplicação Laravel.

Configure um servidor HTTP adequado.

Pode utilizar:

- Apache;
- Nginx + PHP-FPM;
- FrankenPHP;
- outra solução compatível e estável.

Escolha a opção que considere mais adequada para:

```text
Laravel 13
PHP 8.5
Render
```

Não faça uma configuração excessivamente complexa.

O servidor deverá expor corretamente:

```text
public/
```

como document root.

Nunca exponha a raiz inteira do projeto Laravel diretamente.

---

# 11. Porta do Render

O Render fornece a porta através da variável:

```text
PORT
```

A aplicação deve respeitar essa variável.

Não assumir uma porta fixa como:

```text
8000
```

ou:

```text
8080
```

sem considerar a configuração fornecida pelo Render.

O container deve escutar em:

```text
0.0.0.0:$PORT
```

quando aplicável à solução escolhida.

---

# 12. Health Check

Crie uma rota simples para health check.

Sugestão:

```text
GET /health
```

Ela deve retornar:

```text
HTTP 200
```

e uma resposta simples, por exemplo:

```json
{
    "status": "ok"
}
```

Não faça consultas pesadas ao banco nesse endpoint.

O objetivo é permitir que o Render verifique se a aplicação está respondendo.

Se considerar importante verificar o banco, faça isso somente se não introduzir problemas no health check.

---

# 13. Variáveis de ambiente

Revise completamente o `.env.example`.

Não coloque:

- senhas;
- tokens;
- chaves privadas;
- credenciais reais.

A produção deverá utilizar variáveis configuradas no Render.

Garanta suporte para:

```text
APP_NAME
APP_ENV
APP_KEY
APP_DEBUG
APP_URL

LOG_CHANNEL
LOG_LEVEL

DB_CONNECTION
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD

CACHE_STORE
SESSION_DRIVER
QUEUE_CONNECTION
```

e quaisquer outras realmente utilizadas pelo projeto.

---

# 14. APP\_ENV

A produção deverá utilizar:

```text
APP_ENV=production
```

---

# 15. APP\_DEBUG

A produção deverá utilizar:

```text
APP_DEBUG=false
```

Nunca deixar:

```text
APP_DEBUG=true
```

em produção.

---

# 16. APP\_KEY

A aplicação deverá utilizar:

```text
APP_KEY
```

fornecida através das variáveis de ambiente do Render.

**Não gerar uma nova chave a cada deploy.**

Não executar automaticamente:

```bash
php artisan key:generate
```

durante o deploy.

A chave deverá ser criada uma vez e armazenada de forma persistente nas Environment Variables do Render.

---

# 17. APP\_URL

A aplicação deverá aceitar:

```text
APP_URL
```

como variável de ambiente.

Inicialmente poderá ser a URL fornecida pelo Render.

Posteriormente deverá ser possível alterar para o domínio próprio do projeto sem alterar o código.

---

# 18. Banco de dados

O projeto utiliza:

```text
MySQL
```

Mantenha MySQL.

Não migre para PostgreSQL apenas por facilidade do Render sem antes analisar todas as consequências.

A aplicação deverá utilizar as seguintes variáveis:

```text
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Não coloque valores reais no código.

---

# 19. Atenção ao banco do Render

Analise a infraestrutura atual do Render e a disponibilidade de MySQL.

Não assuma que o Render fornece um MySQL gerenciado gratuito permanente.

O projeto deve estar preparado para receber as credenciais de um MySQL externo caso necessário.

A aplicação deve funcionar independentemente de onde o MySQL esteja hospedado, desde que as variáveis:

```text
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

estejam corretamente configuradas.

Não crie dependência do banco estar dentro do mesmo container da aplicação.

---

# 20. Não colocar MySQL no mesmo container

**Não execute MySQL dentro do mesmo container do Laravel.**

Em produção:

```text
Container Laravel
        │
        ▼
Servidor MySQL separado
```

O `docker-compose.yml` continuará podendo utilizar um container MySQL para desenvolvimento local.

---

# 21. Migrations

O deploy deverá permitir executar:

```bash
php artisan migrate --force
```

em produção.

Porém, não execute migrations automaticamente sem avaliar o melhor momento no fluxo do Render.

Crie uma estratégia clara.

Uma possibilidade é utilizar um:

```text
Pre-Deploy Command
```

do Render:

```bash
php artisan migrate --force
```

Analise se essa é a melhor solução.

O importante é evitar:

```text
deploy da aplicação
+
banco incompatível
```

por migrations não executadas.

---

# 22. Seeders

**Não executar automaticamente seeders de dados de exemplo em produção.**

Especialmente:

```bash
php artisan db:seed
```

não deve fazer parte do deploy de produção por padrão.

Os seeders podem ser utilizados localmente.

---

# 23. Usuário administrador

Analise como o administrador inicial está sendo criado atualmente.

Não deixe uma senha administrativa real hardcoded.

A produção deverá possuir uma forma segura de criar o primeiro administrador.

Pode ser através de:

- comando Artisan específico;
- seeder controlado;
- execução manual via Shell;
- outra estratégia segura.

Prefira criar um comando Artisan, se isso deixar a operação mais simples.

Por exemplo:

```bash
php artisan admin:create
```

O comando pode solicitar:

```text
Nome
Email
Senha
```

interativamente.

Não imprimir a senha em logs.

---

# 24. Storage

Analise a utilização de:

```text
storage/
```

pela aplicação.

Execute o que for necessário para que:

```text
storage/app
storage/framework
storage/logs
```

tenham as permissões adequadas.

Caso a aplicação utilize:

```text
public/storage
```

configure corretamente:

```bash
php artisan storage:link
```

Avalie se o `storage:link` deve ser executado durante o build ou no startup.

Não criar links simbólicos quebrados.

---

# 25. Arquivos persistentes

Analise se o sistema atualmente salva arquivos localmente.

Se existirem uploads ou arquivos que precisam sobreviver a um novo deploy/container, documente isso.

Lembre-se:

**o filesystem local do container não deve ser considerado armazenamento permanente.**

Se o projeto futuramente passar a armazenar arquivos importantes, a arquitetura deverá utilizar algo como:

```text
AWS S3
Cloudflare R2
ou outro storage persistente
```

Não implementar essa infraestrutura agora se o sistema não precisar.

Apenas deixe a questão documentada.

---

# 26. Logs

Configure o Laravel para escrever logs de forma adequada ao ambiente de container.

Preferencialmente os logs deverão estar disponíveis no stdout/stderr para que possam ser visualizados no painel do Render.

Analise a configuração atual:

```text
LOG_CHANNEL
```

e adapte se necessário.

Não depender exclusivamente de arquivos locais em:

```text
storage/logs
```

para diagnosticar problemas em produção.

---

# 27. Cache

Analise a configuração de cache atual.

Para uma primeira implantação simples, utilize uma solução que não exija infraestrutura adicional.

Não introduza Redis apenas por precaução.

Se:

```text
CACHE_STORE=database
```

for adequado ao projeto, avalie sua utilização.

Se:

```text
CACHE_STORE=file
```

for suficiente para esse projeto, pode mantê-lo.

Escolha a solução mais simples e compatível com o ambiente do Render.

---

# 28. Sessões

Analise:

```text
SESSION_DRIVER
```

e escolha uma configuração adequada.

Como existe autenticação administrativa, as sessões precisam funcionar corretamente entre requisições.

Não utilizar uma solução que dependa de arquivos temporários caso isso possa causar problemas no ambiente do Render.

Se necessário, utilizar banco de dados para sessões.

---

# 29. Queue

Verifique se o projeto utiliza filas.

Caso não utilize:

```text
QUEUE_CONNECTION=sync
```

ou outra configuração simples apropriada.

Não adicionar Redis/Bull/etc. se não houver necessidade.

---

# 30. Configuração do Render

Crie, se apropriado, um arquivo:

```text
render.yaml
```

utilizando **Render Blueprint**.

O objetivo é deixar a configuração de infraestrutura versionada.

Entretanto, não coloque nesse arquivo:

- senhas;
- APP\_KEY;
- credenciais do banco;
- dados sensíveis.

Utilize referências a Environment Groups ou variáveis de ambiente apropriadas quando fizer sentido.

---

# 31. Render Web Service

O serviço principal deverá ser um:

```text
Web Service
```

O serviço deverá:

- construir a imagem;
- iniciar o Laravel;
- disponibilizar HTTP;
- responder ao health check.

Defina:

```text
healthCheckPath: /health
```

caso o formato do `render.yaml` utilizado suporte essa configuração.

---

# 32. Plano do Render

Não assumir um plano pago.

Prepare inicialmente o projeto para utilizar o plano gratuito quando tecnicamente possível.

Porém, documente claramente quaisquer limitações do plano gratuito, especialmente:

- suspensão por inatividade;
- tempo de inicialização;
- limites de recursos;
- banco de dados;
- armazenamento;
- limitações de background workers.

Não implementar soluções artificiais para manter o serviço acordado.

Não criar cron jobs apenas para impedir o sleep do Render.

---

# 33. Domínio

O domínio personalizado será configurado posteriormente.

Não coloque o domínio definitivo diretamente no código.

O sistema deverá funcionar inicialmente com:

```text
https://<nome-do-servico>.onrender.com
```

e posteriormente com um domínio próprio.

Garanta que:

```text
APP_URL
```

seja suficiente para configurar a URL da aplicação.

---

# 34. HTTPS

Não criar certificados SSL manualmente.

O Render deverá cuidar do HTTPS.

A aplicação deve estar preparada para funcionar corretamente atrás de HTTPS.

Verifique:

- URLs geradas pelo Laravel;
- assets Vite;
- redirects;
- cookies;
- sessão;
- CSRF.

---

# 35. Proxy / HTTPS

Analise se o Laravel precisa de configuração relacionada a proxies confiáveis para reconhecer corretamente:

```text
https
```

quando estiver atrás da infraestrutura do Render.

Não adicionar configurações de proxy sem necessidade.

Se necessário, implemente de acordo com as práticas recomendadas do Laravel 13.

---

# 36. Assets

Garanta que:

```text
npm run build
```

gere corretamente:

```text
public/build
```

A Home deverá carregar:

- CSS;
- JavaScript;
- imagens;
- fontes;
- ícones.

Verifique especialmente as imagens utilizadas na Home de Santa Faustina.

---

# 37. Favicon e assets públicos

Verifique se os assets existentes estão corretamente referenciados.

Evite caminhos absolutos específicos do ambiente local.

Não utilizar:

```text
C:\projetos\...
```

ou:

```text
http://localhost
```

no código.

---

# 38. URL e assets

Verifique a utilização de:

```php
asset()
route()
url()
```

e do Vite.

Os assets devem funcionar tanto:

```text
localhost
```

quanto:

```text
onrender.com
```

e futuramente:

```text
dominio.com.br
```

---

# 39. Composer scripts

Analise os scripts do:

```text
composer.json
```

e certifique-se de que nenhum script execute ações perigosas durante produção.

Não executar automaticamente:

```text
migrate:fresh
db:wipe
db:seed
key:generate
```

durante o deploy.

---

# 40. NPM scripts

Analise:

```text
package.json
```

e garanta que exista:

```bash
npm run build
```

funcionando corretamente.

---

# 41. Docker Compose local

Não remover o:

```text
docker-compose.yml
```

atual.

Ele deverá continuar servindo para desenvolvimento.

Se precisar fazer alterações:

- mantenha o MySQL local;
- mantenha volumes;
- mantenha variáveis;
- não misture configurações de produção no ambiente local sem necessidade.

---

# 42. Arquivos de configuração esperados

Ao final, avalie se a seguinte estrutura faz sentido:

```text
/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
│
├── Dockerfile
├── docker-compose.yml
├── render.yaml
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── vite.config.js
├── .env.example
├── .gitignore
└── README.md
```

Não crie arquivos que não sejam necessários.

---

# 43. Gitignore

Revise o:

```text
.gitignore
```

Garanta que não sejam enviados para o Git:

```text
.env
/vendor
/node_modules
/storage/logs
```

e demais arquivos temporários ou sensíveis.

O arquivo:

```text
.env.example
```

deve permanecer versionado.

---

# 44. Segurança antes do deploy

Faça uma auditoria procurando:

```text
password=
senha=
APP_KEY=
DB_PASSWORD=
API_KEY=
SECRET=
TOKEN=
```

Não deixar credenciais reais no repositório.

Procure também por:

```text
localhost
127.0.0.1
0.0.0.0
```

e verifique se algum deles está sendo utilizado incorretamente em produção.

---

# 45. Comandos Artisan

Avalie a possibilidade de criar comandos úteis para produção.

Por exemplo:

```text
php artisan admin:create
```

Somente implemente se realmente for útil.

Não criar comandos desnecessários.

---

# 46. Otimização Laravel

O ambiente de produção deverá utilizar as otimizações apropriadas.

Avalie executar durante o build:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Somente faça isso se forem compatíveis com a configuração atual.

Tenha cuidado com variáveis de ambiente e configurações dinâmicas.

Não cachear algo que impeça alterações das Environment Variables do Render de funcionarem corretamente.

---

# 47. Startup

Defina claramente como o container será iniciado.

O processo principal deverá permanecer em foreground.

Não utilizar:

```bash
service nginx start
```

ou outros comandos que iniciem processos em background e terminem o container.

O processo principal deverá permanecer ativo para que o Render considere o serviço saudável.

---

# 48. Banco e migrations no deploy

Defina claramente no README:

### Primeiro deploy

1. Criar banco MySQL;
2. Configurar Environment Variables;
3. Fazer deploy;
4. Executar migrations;
5. Criar administrador.

### Deploys seguintes

```text
Git push
   ↓
Render build
   ↓
Deploy
   ↓
Migration
```

Documente a estratégia escolhida.

---

# 49. README de produção

Atualize o `README.md`.

Criar uma seção:

```text
## Deploy no Render
```

Explicando passo a passo:

### 1. GitHub

Como colocar o projeto em um repositório.

### 2. Render

Como criar:

```text
New → Web Service
```

ou utilizar Blueprint caso o `render.yaml` esteja sendo utilizado.

### 3. Configurar Environment Variables

Listar todas as variáveis necessárias.

Não colocar valores reais.

### 4. Configurar MySQL

Explicar onde informar:

```text
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

### 5. Deploy

Explicar o processo.

### 6. Migration

Explicar como executar:

```bash
php artisan migrate --force
```

### 7. Criar administrador

Explicar como criar o usuário inicial.

### 8. Verificar

Abrir:

```text
/health
```

e:

```text
/
```

---

# 50. Teste local de produção

Antes de considerar a tarefa concluída, faça um teste local simulando produção.

Idealmente:

```bash
docker build -t reflexoes-faustina .
```

e depois execute o container.

Verifique:

- Laravel inicia;
- PHP 8.5 funciona;
- assets carregam;
- Home abre;
- `/health` retorna 200;
- conexão com MySQL funciona;
- login administrativo funciona;
- CRUD funciona;
- reflexão aleatória funciona;
- botão "Ouvir" continua funcionando;
- QR Code Pix continua funcionando.

---

# 51. Teste do build

Execute:

```bash
composer install --no-dev --optimize-autoloader
```

e:

```bash
npm ci
npm run build
```

Corrija todos os erros encontrados.

Não considerar o projeto pronto enquanto existirem erros de build.

---

# 52. Testes automatizados

Execute:

```bash
php artisan test
```

ou o comando equivalente utilizado pelo projeto.

Todos os testes existentes devem passar.

Se algum teste falhar devido às alterações de produção, corrija a causa.

Não simplesmente desabilite o teste.

---

# 53. Checklist final

Antes de finalizar, verifique:

- [ ] PHP 8.5
- [ ] Laravel 13
- [ ] Composer funcionando
- [ ] NPM funcionando
- [ ] Vite funcionando
- [ ] Tailwind compilando
- [ ] Docker build funcionando
- [ ] MySQL funcionando localmente
- [ ] migrations funcionando
- [ ] `/health` funcionando
- [ ] Home funcionando
- [ ] autenticação funcionando
- [ ] CRUD funcionando
- [ ] reflexão aleatória funcionando
- [ ] botão Ouvir funcionando
- [ ] Pix funcionando
- [ ] imagens funcionando
- [ ] `.env` não versionado
- [ ] `.env.example` atualizado
- [ ] APP\_DEBUG=false em produção
- [ ] APP\_KEY configurada via Environment Variable
- [ ] logs adequados
- [ ] storage adequado
- [ ] assets compilados
- [ ] Render configurável
- [ ] README atualizado
- [ ] testes passando

---

# 54. O que NÃO fazer

Não:

- migrar MySQL para PostgreSQL sem necessidade;
- adicionar Redis sem necessidade;
- adicionar serviços pagos;
- adicionar workers sem necessidade;
- criar infraestrutura complexa;
- alterar funcionalidades existentes sem motivo;
- remover Docker local;
- colocar credenciais no Git;
- executar `migrate:fresh` em produção;
- executar seeders automaticamente em produção;
- executar `key:generate` durante cada deploy;
- criar mecanismo para impedir o sleep do Render;
- colocar MySQL dentro do mesmo container da aplicação;
- considerar armazenamento local do container como persistente.

---

# 55. Resultado esperado

Ao finalizar, quero que o projeto esteja **pronto para ser conectado ao Render**.

O fluxo esperado deverá ser:

```text
Desenvolvimento
      │
      ▼
Git commit
      │
      ▼
GitHub
      │
      ▼
Render
      │
      ├── Build Docker
      │
      ├── composer install
      │
      ├── npm build
      │
      └── iniciar Laravel
              │
              ▼
          /health
              │
              ▼
          Aplicação
              │
              ▼
            MySQL
```

---

# 56. Entrega final

Ao terminar o trabalho, não apenas diga que está pronto.

Apresente:

## Arquivos criados

Liste todos.

## Arquivos modificados

Liste todos.

## Dependências adicionadas

Informe quais foram adicionadas e por quê.

## Configuração do Render

Explique:

- tipo de serviço;
- Docker;
- build;
- start;
- health check;
- variáveis de ambiente;
- migrations.

## Banco

Explique como configurar o MySQL.

## Deploy

Explique passo a passo como fazer o primeiro deploy.

## Administrador

Explique como criar o primeiro usuário administrador.

## Testes

Informe exatamente quais testes foram executados e seus resultados.

## Pendências

Informe qualquer coisa que ainda precise ser configurada manualmente.

---

# REGRA FINAL

**Não considere a tarefa concluída apenas porque ****`Dockerfile`**** e ****`render.yaml`**** foram criados.**

A tarefa somente estará concluída depois de:

1. analisar o projeto existente;
2. preparar o Dockerfile;
3. preparar o ambiente de produção;
4. validar o build;
5. validar o Laravel;
6. validar os assets;
7. validar a conexão com MySQL;
8. validar migrations;
9. validar autenticação;
10. executar os testes;
11. revisar segurança;
12. atualizar o README;
13. deixar claramente documentado o processo de deploy no Render.

Se alguma decisão de infraestrutura depender de uma informação que não está disponível no projeto, **não invente**. Documente a decisão necessária e explique exatamente o que precisa ser configurado manualmente.
