# Reflexões Diárias de Santa Faustina

Aplicação web em Laravel que exibe, na página inicial, uma reflexão
sorteada aleatoriamente entre as reflexões cadastradas, baseada no Diário
de Santa Faustina. Um administrador autenticado cadastra, edita, exclui e
consulta as reflexões em um painel próprio.

> **Conteúdo de exemplo:** os textos criados pelo `DailyReflectionSeeder` são
> **fictícios**, usados apenas para testar a aplicação, e estão claramente
> marcados como tal. O conteúdo real do Diário deve ser inserido pelo
> administrador através do painel.

## Stack

- PHP 8.5 / Laravel 13
- MySQL 8 (via Docker Compose)
- Blade + Tailwind CSS (integração padrão do Laravel, sem frontend framework)
- Vite
- Eloquent ORM, Migrations, Seeders, Factories
- Laravel Breeze (stack Blade) para autenticação
- `endroid/qr-code` para o QR Code de doação via Pix (SVG, sem depender de GD/Imagick)
- PHPUnit

## Requisitos

- PHP 8.4+ (testado com 8.5; é a versão usada em produção)
- Composer
- Docker + Docker Compose (para o MySQL local e para o build de produção)
- Node.js / npm

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate

# Sobe o MySQL (ver seção "Docker" abaixo)
docker compose up -d

php artisan migrate
php artisan db:seed
# ou, para migrar e popular em um único passo:
php artisan migrate --seed

npm install
npm run build   # gera os assets para produção
# ou, em desenvolvimento:
npm run dev
```

Suba a aplicação com:

```bash
php artisan serve
```

e acesse `http://127.0.0.1:8000`.

## Docker / MySQL

O `docker-compose.yml` na raiz do projeto sobe um container MySQL 8,
configurado inteiramente por variáveis do `.env` (nome do banco, usuário,
senha, senha de root e porta). Nenhuma senha real está no repositório — o
`.env.example` documenta as variáveis com valores de desenvolvimento.

```bash
docker compose up -d      # inicia o MySQL em segundo plano
docker compose down       # para o container (mantém os dados no volume)
docker compose down -v    # para e apaga também o volume de dados
```

Variáveis relevantes no `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reflexoes
DB_USERNAME=reflexoes
DB_PASSWORD=secret
DB_ROOT_PASSWORD=secret   # usada apenas pelo container, não pelo Laravel
```

> Se o Docker do seu ambiente rodar dentro do WSL, basta executar os comandos
> `docker compose ...` a partir do WSL — o Laravel (rodando no Windows) já
> consegue alcançar `127.0.0.1:3306` graças ao encaminhamento de portas do
> WSL2.

## Administrador inicial

**Em desenvolvimento local**, o `AdminUserSeeder` cria (ou atualiza) o
administrador a partir de três variáveis do `.env`:

```
ADMIN_NAME="Administrador"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=change-me-please
```

Nenhuma senha real está hardcoded no código. Defina essas variáveis no seu
`.env` **antes** de rodar `php artisan db:seed` (ou `migrate --seed`). Se
`ADMIN_PASSWORD` não estiver definida, o seeder gera uma senha aleatória e a
imprime uma única vez no console — troque-a assim que possível.

**Em produção**, não use o seeder (ele também popula reflexões de exemplo
fictícias, que não fazem sentido fora do ambiente local). Use o comando
interativo, que nunca grava a senha em variável de ambiente, log ou
histórico do shell:

```bash
php artisan admin:create
```

Ele pede nome, e-mail e senha (com confirmação) interativamente. Rodando de
novo com um e-mail que já existe, ele pergunta antes de sobrescrever a senha
— ou aceite direto com `--force`. Também aceita `--name`, `--email` e
`--password` para uso em script, mas evite `--password` em produção: o valor
fica no histórico do shell.

Login: `http://127.0.0.1:8000/login`
Painel: `http://127.0.0.1:8000/admin`

## Doação via Pix

A Home tem uma seção discreta, logo abaixo da reflexão, convidando o
visitante a contribuir voluntariamente via Pix (QR Code + "Pix Copia e
Cola"). Não há valor fixo — quem quiser contribuir escolhe o valor no
próprio aplicativo do banco.

Os dados exibidos vêm de `config/donation.php`, configurável via `.env`:

```
PIX_KEY=dc25aa47-1dbf-4562-ac1f-195223e94457
PIX_RECEIVER_NAME="Breno Mol"
PIX_BANK=Bradesco
PIX_MERCHANT_CITY="SAO PAULO"
```

A chave Pix não é um segredo de autenticação — é feita para ser
compartilhada publicamente com quem for pagar, então não há problema em
ela aparecer no `.env.example`/repositório. `PIX_MERCHANT_CITY` não foi
informado no pedido original; é um campo **obrigatório** no padrão BR Code
(EMV) do Pix, mas puramente informativo (não afeta o pagamento) — ajuste-o
livremente para a cidade real do recebedor.

**Como o Pix é gerado:**

- `app/Services/Pix/PixPayloadBuilder.php` monta o payload "Copia e Cola"
  no padrão BR Code (EMV) do Banco Central, campo a campo (TLV) mais o
  checksum CRC16, **sem depender de nenhum pacote externo** — o formato é
  simples o bastante para não justificar uma dependência só para isso. O
  campo de valor (tag `54`) é omitido de propósito, permitindo qualquer
  valor.
- `app/Services/Pix/PixQrCodeGenerator.php` usa `endroid/qr-code` (writer
  SVG, sem exigir GD/Imagick) para transformar esse payload em uma imagem.
- O botão "Copiar código Pix" usa Alpine.js (já uma dependência do projeto,
  via Breeze) e a Clipboard API do navegador — nenhuma biblioteca nova de
  JS foi adicionada.

**Validação feita (sem efetuar nenhum pagamento real):** o payload foi
conferido campo a campo contra o formato oficial, o CRC16 foi testado
contra o vetor de teste padrão do algoritmo, e o QR Code gerado foi
decodificado de volta (round-trip) para confirmar que reproduz exatamente
o mesmo texto do "Copia e Cola" — ver seção Testes.

Nenhum dado do visitante é coletado: não há CPF, cartão, senha ou
histórico de doações. O site apenas exibe os dados para o pagamento ser
feito no aplicativo do próprio banco do doador.

## Ouvir reflexão (síntese de voz)

Dentro do card da reflexão, um botão "🔊 Ouvir reflexão" lê o parágrafo e a
reflexão em voz alta usando a **Web Speech API nativa do navegador**
(`window.speechSynthesis` / `SpeechSynthesisUtterance`) — sem nenhum
serviço externo, API paga ou biblioteca de terceiros.

- `resources/js/features/text-to-speech.js` concentra toda a lógica:
  seleção de voz `pt-BR` (com fallback para qualquer voz `pt-*` e depois
  para a voz padrão do navegador, tratando `speechSynthesis.onvoiceschanged`
  para navegadores que carregam as vozes de forma assíncrona), montagem do
  texto narrado, e a máquina de estados Ouvir → Ouvindo → Pausado → Parar.
- Os dados a narrar (`paragrafo`, `descricao_paragrafo`, `reflexao`) são
  publicados pela view via `window.reflectionSpeechData = @js(...)`
  ([home/index.blade.php](resources/views/home/index.blade.php)) — nunca
  concatenados manualmente em string; `@js()` escapa o valor com segurança
  para uso em JavaScript. O texto também passa por `strip_tags()` no
  `HomeController` antes de chegar ao JS, então HTML eventualmente presente
  no banco nunca é narrado nem injetado na página.
- Todo o controle (`Ouvir`/`Pausar`/`Continuar`/`Parar`) usa `<button>`
  reais, com `aria-label` nos ícones e `aria-live="polite"` no status, para
  leitores de tela e navegação por teclado.
- Se o navegador não suportar a API, a seção inteira permanece oculta (a
  Home funciona normalmente, sem quebrar e sem mostrar um botão morto).
- Nunca inicia sozinha (sem autoplay); qualquer nova leitura cancela a
  anterior (`speechSynthesis.cancel()`) antes de começar, então não há
  enfileiramento; recarregar a página naturalmente encerra a leitura.

## Estrutura principal

```
Dockerfile                                    # imagem de produção (multi-stage)
render.yaml                                   # Render Blueprint
docker/
├── apache/                                   # vhost + porta (Render injeta $PORT)
├── php/production.ini                        # opcache e afins, só na imagem de produção
└── entrypoint.sh                             # cache de config/rotas/views + sobe o Apache

app/
├── Console/Commands/AdminCreateCommand.php   # `php artisan admin:create`
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php              # página pública
│   │   ├── HealthController.php            # GET /health (Render)
│   │   ├── ProfileController.php           # perfil do administrador
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       └── DailyReflectionController.php
│   └── Requests/Admin/
│       ├── StoreDailyReflectionRequest.php
│       └── UpdateDailyReflectionRequest.php
├── Models/DailyReflection.php
├── Services/Pix/
│   ├── PixPayloadBuilder.php                # payload BR Code (EMV), sem dependências
│   └── PixQrCodeGenerator.php                # QR Code SVG a partir do payload
└── View/Components/PublicLayout.php

config/donation.php                           # dados públicos do Pix (via .env)

database/
├── migrations/
│   ├── ..._create_daily_reflections_table.php
│   └── ..._alter_daily_reflections_table_remove_dia_add_descricao_paragrafo.php
├── factories/DailyReflectionFactory.php
└── seeders/
    ├── AdminUserSeeder.php
    └── DailyReflectionSeeder.php           # dados fictícios de exemplo

public/assets/images/
└── Maria_Faustyna_Kowalska.jpg             # retrato usado na Home

resources/js/
├── app.js
└── features/text-to-speech.js              # "Ouvir reflexão" (Web Speech API)

resources/views/
├── layouts/                                # layouts públicos e admin
├── home/index.blade.php                    # reflexão sorteada aleatoriamente
└── admin/
    ├── dashboard.blade.php
    └── reflections/{index,create,edit,show,_form}.blade.php

routes/
├── web.php                                 # "/" pública + grupo "/admin" autenticado
└── auth.php                                # login/logout/senha (sem autocadastro)
```

A tabela `daily_reflections` tem hoje: `id`, `paragrafo` (inteiro),
`descricao_paragrafo` (texto do parágrafo do Diário), `reflexao` (texto da
reflexão), `created_at`, `updated_at`, `deleted_at`. A coluna `dia` que
existia na versão anterior foi removida — não há mais o conceito de
"reflexão do dia".

## Regras de negócio importantes

- **Reflexão sorteada aleatoriamente:** a página pública não depende de
  data. A cada acesso, `HomeController` seleciona uma reflexão ativa com
  `DailyReflection::query()->inRandomOrder()->first()`. Isso é suficiente
  para o volume de dados deste projeto — `ORDER BY RAND()` só deixaria de
  ser adequado com um número muito grande de linhas (dezenas de milhares+),
  o que não é o caso aqui.
- **Soft delete:** reflexões excluídas usam `deleted_at`, nunca são
  sorteadas na página pública e não aparecem na listagem administrativa
  padrão.
- **Sem autocadastro:** o administrador é criado pelo `AdminUserSeeder`
  (local) ou por `php artisan admin:create` (produção); a rota `/register`
  do Breeze foi removida.

## Testes

```bash
php artisan test
```

Os testes usam SQLite em memória (configurado no `phpunit.xml`) e cobrem:

- página pública (200, reflexão sorteada é exibida, seleção não depende de
  data, reflexões excluídas não são sorteadas, mensagem amigável quando não
  há nenhuma reflexão cadastrada);
- proteção da área administrativa (guest é redirecionado ao login);
- CRUD completo de reflexões (criar, editar, excluir/soft delete),
  incluindo o campo `descricao_paragrafo`;
- validações (parágrafo/descrição do parágrafo/reflexão obrigatórios);
- schema da migration de alteração (remove `dia`, adiciona
  `descricao_paragrafo`, e pode ser revertida com `migrate:rollback`);
- autenticação e perfil do administrador (login, logout, atualização de
  perfil/senha);
- payload Pix (`PixPayloadBuilder`, teste unitário): CRC16 contra o vetor
  de teste padrão, chave Pix e GUI oficial presentes, cabeçalho EMV
  correto, ausência do campo de valor fixo, sanitização de acentos;
- seção de doação na Home: presença do QR Code (SVG) e do código Pix
  Copia e Cola, sem valores fixos sugeridos;
- botão "Ouvir reflexão": aparece só quando há reflexão, dados de fala
  publicados corretamente, HTML removido do texto narrado;
- `GET /health` responde 200 com `{"status":"ok"}`;
- `php artisan admin:create`: cria e atualiza administrador (interativo e
  via opções), pede confirmação antes de sobrescrever um e-mail existente
  (exceto com `--force`), rejeita senhas que não conferem na confirmação, e
  nunca imprime a senha na saída do comando.

O JavaScript de síntese de voz (`text-to-speech.js`) não tem testes
automatizados em Node — o projeto não usa nenhum test runner de JS — mas
foi verificado manualmente em navegador real (Chromium) cobrindo: estado
inicial, Ouvir → Ouvindo/Pausar/Parar, Pausar → Continuar (retoma do ponto
em que parou, não reinicia), finalização automática (`onend`), tratamento
de erro (`onerror`), navegador sem suporte (degrada sem quebrar a Home),
ausência de fila em cliques repetidos, texto com acentos/aspas/HTML
misturado, teclado (foco e ativação por Enter) e ausência de scroll
horizontal no mobile.

## Deploy no Render

O projeto está preparado para build via Docker no Render, a partir do
`Dockerfile` (multi-stage: Composer → npm/Vite → imagem final com Apache +
PHP 8.5) e do `render.yaml` (Render Blueprint). Esta seção documenta o
passo a passo do **primeiro** deploy.

> **Antes de tudo:** o Render não oferece um serviço de MySQL gerenciado
> nativo — só PostgreSQL e Redis/Key Value. Este projeto mantém MySQL (por
> pedido explícito, sem migrar de banco), então você precisa de um MySQL
> externo antes de começar. Opções comuns: um provedor de MySQL gerenciado
> (PlanetScale, Aiven, Railway, AWS RDS etc.) ou seu próprio servidor MySQL
> acessível pela internet. Isso é uma decisão de infraestrutura que só você
> pode tomar — o projeto funciona com qualquer host MySQL, desde que as
> variáveis `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD`
> apontem para ele corretamente.

### 1. GitHub

Suba o projeto para um repositório no GitHub (`.env` fica de fora
automaticamente — está no `.gitignore`):

```bash
git remote add origin <url-do-seu-repositorio>
git push -u origin master
```

### 2. Render

No [dashboard do Render](https://dashboard.render.com):

- **Com Blueprint (recomendado):** New → Blueprint → selecione o
  repositório. O Render lê o `render.yaml` da raiz e propõe criar o Web
  Service já configurado (build via Docker, health check em `/health`,
  plano gratuito). Revise e confirme.
- **Manual:** New → Web Service → selecione o repositório → Environment:
  **Docker** (o Render detecta o `Dockerfile` na raiz automaticamente).

O Render valida o `render.yaml` ao conectar o Blueprint; a sintaxe de
serviços/variáveis pode evoluir entre versões do Render, então se algum
campo for rejeitado, o próprio Render aponta qual — ajuste conforme
indicado.

### 3. Configurar Environment Variables

No painel do serviço, em **Environment**, preencha as variáveis marcadas
como obrigatórias no `render.yaml` (não têm valor no arquivo de propósito —
nenhum dado sensível fica versionado):

```
APP_KEY=              # gere com: php artisan key:generate --show
APP_URL=               # ex.: https://reflexoes-faustina.onrender.com

DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

As demais (`APP_ENV=production`, `APP_DEBUG=false`, `LOG_CHANNEL=stderr`,
`DB_CONNECTION=mysql`, `DB_PORT=3306`, `CACHE_STORE=database`,
`SESSION_DRIVER=database`, `QUEUE_CONNECTION=sync` etc.) já vêm definidas
pelo `render.yaml`.

**`APP_KEY`:** gere uma vez localmente e cole no Render. Nunca rode
`php artisan key:generate` como parte do deploy — isso invalidaria sessões e
qualquer dado criptografado a cada novo deploy.

```bash
php artisan key:generate --show
```

### 4. Configurar MySQL

Preencha `DB_HOST`, `DB_PORT` (normalmente `3306`), `DB_DATABASE`,
`DB_USERNAME` e `DB_PASSWORD` com os dados do MySQL externo escolhido (ver
aviso no topo desta seção). Se o provedor exigir SSL, verifique se ele
expõe as variáveis/CA necessárias — a conexão MySQL da aplicação já lê
`MYSQL_ATTR_SSL_CA` quando definida (`config/database.php`).

### 5. Deploy

Com as variáveis configuradas, o Render builda a imagem Docker (Composer →
npm/Vite → imagem final) e inicia o container. Deploys seguintes acontecem
automaticamente a cada `git push` na branch configurada
(`autoDeploy: true`):

```text
git push → Render builda a imagem → Pre-Deploy: migrate --force → novo container assume o tráfego
```

### 6. Migration

O `render.yaml` já configura um **Pre-Deploy Command**
(`php artisan migrate --force`), que roda antes do novo container assumir o
tráfego — evita o cenário de aplicação nova rodando contra um banco com
schema desatualizado. Se o seu plano do Render não suportar Pre-Deploy
Command, rode manualmente pelo Shell do serviço após o deploy:

```bash
php artisan migrate --force
```

Seeders **não** rodam automaticamente em produção (nem devem — ver
[Administrador inicial](#administrador-inicial)). Se algum dia precisar
popular dados manualmente, faça isso deliberadamente pelo Shell, nunca como
parte automática do deploy.

### 7. Criar administrador

Pelo Shell do serviço no Render:

```bash
php artisan admin:create
```

Responda aos prompts (nome, e-mail, senha). Ver
[Administrador inicial](#administrador-inicial) para detalhes e opções.

### 8. Verificar

```text
https://<nome-do-servico>.onrender.com/health   → {"status":"ok"}
https://<nome-do-servico>.onrender.com/          → Home com a reflexão sorteada
```

Confira também: login administrativo (`/login`), CRUD de reflexões, o botão
"Ouvir reflexão" e o QR Code Pix — a lógica é idêntica à local, então se
tudo passou nos testes automatizados e no teste manual local (ver seção
seguinte), o comportamento em produção deve ser o mesmo.

### Limitações do plano gratuito

O `render.yaml` usa `plan: free`. Antes de contar com isso para produção
real, esteja ciente de que o plano gratuito do Render (na formatação atual
deles, sujeita a mudar — confirme no dashboard):

- **Suspende o serviço por inatividade** e o próximo acesso paga um tempo de
  inicialização (cold start) enquanto o container sobe de novo. Este
  projeto **não** cria nenhum mecanismo artificial (cron, ping externo etc.)
  para manter o serviço acordado — isso violaria os termos de uso de planos
  gratuitos na maioria das plataformas e só adiaria o problema real, que é
  precisar de um plano pago para disponibilidade constante.
- Tem limites de CPU/RAM mais apertados que os planos pagos.
- Não inclui banco de dados MySQL (ver aviso no topo da seção).
- Não é adequado para *background workers* de longa duração — este projeto
  não precisa de nenhum (`QUEUE_CONNECTION=sync`), mas vale saber caso o
  projeto cresça nessa direção no futuro.

### Armazenamento não é persistente

O filesystem do container **não sobrevive** a um novo deploy. Hoje a
aplicação não salva nenhum arquivo do usuário em disco (sem upload de
imagens, sem `storage:link` em uso — só a imagem estática de
`public/assets/`, que já vai dentro da própria imagem Docker). Se isso
mudar no futuro (por exemplo, upload de imagens pelas reflexões), a
aplicação vai precisar de um storage externo (S3, Cloudflare R2 ou
similar) configurado via `config/filesystems.php` (o disco `s3` já existe
lá, pronto para configurar via variáveis `AWS_*`) — não implementado agora
porque não há necessidade atual.

### Logs

Em produção, defina `LOG_CHANNEL=stderr` (já vem assim pelo `render.yaml`):
é o único canal cujo conteúdo aparece no painel de logs do Render, que
captura stdout/stderr do container, não arquivos dentro dele
(`storage/logs/laravel.log` continua existindo como rede de segurança para
o canal `emergency`, mas não é o que você deve acompanhar no dia a dia).

### HTTPS, proxy e domínio próprio

O Render cuida do certificado HTTPS automaticamente — nada a fazer aqui. A
aplicação já está configurada para reconhecer corretamente que está atrás
de HTTPS quando o Render repassa a requisição internamente por HTTP
(`bootstrap/app.php` confia no proxy do Render via `trustProxies`), o que
faz `url()`/`asset()`/redirects e o cookie de sessão `secure` funcionarem
corretamente sem configuração extra.

O domínio inicial é `https://<nome-do-serviço>.onrender.com`. Para trocar
por um domínio próprio mais tarde, configure-o no Render (Custom Domains) e
atualize apenas a variável `APP_URL` — nenhuma mudança de código é
necessária.

### E-mail (esqueci minha senha)

O fluxo "esqueci minha senha" (herdado do Breeze) depende de
`MAIL_MAILER`. Sem um provedor de e-mail configurado, `MAIL_MAILER=log`
(padrão) só grava o e-mail no log, sem enviá-lo de verdade — então esse
fluxo não é utilizável em produção enquanto isso não for configurado.
Como o projeto tem um único administrador, a forma mais simples de resetar
uma senha esquecida é `php artisan admin:create --force` pelo Shell do
Render (não exige e-mail nenhum). Configurar um provedor de e-mail real
(Resend, Mailgun, SES etc.) fica como decisão futura, fora do escopo desta
preparação — não foi presumido nenhum provedor.

## Segurança

- CSRF em todos os formulários (`@csrf`).
- Rotas administrativas protegidas por middleware `auth`.
- `$fillable` definido nos models (sem mass assignment de campos sensíveis).
- Views Blade usam `{{ }}` (escaping automático); o único uso de `{!! !!}`
  é o SVG do QR Code, gerado no servidor a partir da configuração do Pix
  (não é conteúdo enviado por usuário).
- Credenciais não ficam no código-fonte; `.env` está no `.gitignore`.
- `GET /health` é público e não autenticado de propósito (é assim que o
  Render verifica se o serviço está no ar) — responde só `{"status":"ok"}`,
  sem consultar o banco nem expor nenhuma informação da aplicação.
- A aplicação confia no proxy reverso do Render (`trustProxies` em
  `bootstrap/app.php`) para reconhecer HTTPS corretamente; o container em si
  nunca fica exposto diretamente à internet, só através do proxy do Render.
- **Checklist antes de produção:** ver seção
  [Deploy no Render](#deploy-no-render) — `APP_DEBUG=false`, `APP_KEY`
  própria, credenciais fortes e HTTPS já fazem parte do que está documentado
  ali.

## Próximos passos (fora do escopo atual, intencionalmente)

A arquitetura (controllers enxutos, Form Requests, model único bem
definido) foi mantida simples de propósito, mas comporta extensões futuras
sem retrabalho estrutural: histórico/calendário de reflexões, busca,
compartilhamento, favoritos, notificações, API e aplicativo mobile.
