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

- PHP 8.3+ (testado com 8.5)
- Composer
- Docker + Docker Compose (para o MySQL)
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

O `AdminUserSeeder` cria (ou atualiza) o administrador a partir de três
variáveis do `.env`:

```
ADMIN_NAME="Administrador"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=change-me-please
```

Nenhuma senha real está hardcoded no código. Em ambiente local, defina essas
variáveis no seu `.env` **antes** de rodar `php artisan db:seed` (ou
`migrate --seed`). Se `ADMIN_PASSWORD` não estiver definida, o seeder gera
uma senha aleatória e a imprime uma única vez no console — troque-a assim
que possível.

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
app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php              # página pública
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
- **Sem autocadastro:** o único administrador é criado pelo
  `AdminUserSeeder`; a rota `/register` do Breeze foi removida.

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
  publicados corretamente, HTML removido do texto narrado.

O JavaScript de síntese de voz (`text-to-speech.js`) não tem testes
automatizados em Node — o projeto não usa nenhum test runner de JS — mas
foi verificado manualmente em navegador real (Chromium) cobrindo: estado
inicial, Ouvir → Ouvindo/Pausar/Parar, Pausar → Continuar (retoma do ponto
em que parou, não reinicia), finalização automática (`onend`), tratamento
de erro (`onerror`), navegador sem suporte (degrada sem quebrar a Home),
ausência de fila em cliques repetidos, texto com acentos/aspas/HTML
misturado, teclado (foco e ativação por Enter) e ausência de scroll
horizontal no mobile.

## Segurança

- CSRF em todos os formulários (`@csrf`).
- Rotas administrativas protegidas por middleware `auth`.
- `$fillable` definido nos models (sem mass assignment de campos sensíveis).
- Views Blade usam `{{ }}` (escaping automático); o único uso de `{!! !!}`
  é o SVG do QR Code, gerado no servidor a partir da configuração do Pix
  (não é conteúdo enviado por usuário).
- Credenciais não ficam no código-fonte; `.env` está no `.gitignore`.
- **Antes de ir para produção:** defina `APP_DEBUG=false`, gere uma
  `APP_KEY` própria, use senhas fortes para `DB_PASSWORD`/`ADMIN_PASSWORD` e
  sirva a aplicação sob HTTPS.

## Próximos passos (fora do escopo atual, intencionalmente)

A arquitetura (controllers enxutos, Form Requests, model único bem
definido) foi mantida simples de propósito, mas comporta extensões futuras
sem retrabalho estrutural: histórico/calendário de reflexões, busca,
compartilhamento, favoritos, notificações, API e aplicativo mobile.
