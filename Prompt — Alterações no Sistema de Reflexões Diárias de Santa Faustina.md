# Prompt — Alterações no Sistema de Reflexões Diárias de Santa Faustina

## Contexto

O sistema **Reflexões Diárias de Santa Faustina** já foi desenvolvido com base no prompt anterior.

Agora precisamos realizar algumas alterações importantes na estrutura do banco de dados, no comportamento da página inicial e no visual da Home.

**Não recrie o projeto do zero.**

Primeiro analise cuidadosamente o código já existente e faça as alterações necessárias preservando o que já está funcionando.

---

# 1. Alteração principal — Remover o conceito de "dia"

A regra do sistema mudou.

Anteriormente cada reflexão estava vinculada a uma data através da coluna `dia`.

**Isso não será mais utilizado.**

Todas as reflexões/parágrafos cadastrados estarão disponíveis para sorteio.

Ao acessar a página inicial:

1. O sistema deverá consultar os parágrafos disponíveis no banco;
2. Selecionar **aleatoriamente um dos parágrafos**;
3. Exibir esse parágrafo na Home.

Portanto, não deve mais existir a lógica:

```text
"buscar a reflexão correspondente ao dia atual"
```

A lógica deverá ser:

```text
"selecionar aleatoriamente uma reflexão/parágrafo disponível"
```

---

# 2. Banco de dados

A tabela atualmente possui uma estrutura semelhante a:

```text
id
paragrafo
reflexao
dia
created_at
updated_at
deleted_at
```

Precisamos alterar essa estrutura.

A coluna:

```text
dia
```

deve ser **removida**.

Adicionar uma nova coluna:

```text
descricao_paragrafo
```

Essa coluna deverá armazenar o **texto do parágrafo do Diário de Santa Faustina**.

A estrutura esperada será aproximadamente:

```text
id
paragrafo
descricao_paragrafo
reflexao
created_at
updated_at
deleted_at
```

Analise os tipos de dados adequados.

Sugestão:

```text
paragrafo              -> integer
descricao_paragrafo    -> text
reflexao               -> text
```

Como os textos podem ser relativamente grandes, utilize um tipo adequado para armazená-los.

---

# 3. Migration

Não altere manualmente a migration antiga se ela já tiver sido executada.

Crie uma **nova migration de alteração da tabela**.

Essa migration deverá:

### Adicionar:

```text
descricao_paragrafo
```

### Remover:

```text
dia
```

A migration também deverá possuir um método `down()` capaz de reverter corretamente as alterações.

Antes de criar a migration, analise a migration atual e a estrutura real do banco.

---

# 4. Model

Atualize o model:

```text
DailyReflection
```

Remova qualquer referência ao campo:

```text
dia
```

Adicione:

```text
descricao_paragrafo
```

ao `$fillable`, caso esteja sendo utilizado.

Revise também:

- casts;
- scopes;
- métodos;
- relacionamentos;
- regras relacionadas à data.

Não deve permanecer nenhuma lógica relacionada à "reflexão do dia".

---

# 5. Nova lógica da página inicial

A página inicial deverá selecionar aleatoriamente uma reflexão cadastrada.

A consulta deve considerar apenas registros ativos, ou seja, registros que não tenham sido excluídos via Soft Delete.

A lógica conceitual deve ser:

```text
buscar uma reflexão aleatória
↓
exibir parágrafo
↓
exibir descrição do parágrafo
↓
exibir reflexão
```

Não utilize a data atual para escolher a reflexão.

---

# 6. Importante sobre aleatoriedade

Analise a melhor maneira de fazer a seleção aleatória utilizando Eloquent/Laravel e MySQL.

Para este projeto, uma solução simples como:

```php
DailyReflection::inRandomOrder()->first();
```

pode ser suficiente.

Entretanto, avalie se existe alguma consideração de performance relevante.

Não crie uma solução complexa sem necessidade.

---

# 7. Alteração do formulário administrativo

O formulário de cadastro/edição deverá ser atualizado.

Remover completamente:

```text
Dia
```

Adicionar:

```text
Número do parágrafo
Descrição do parágrafo
Reflexão
```

Exemplo:

```text
Número do parágrafo
[ 123 ]

Descrição do parágrafo
[ texto completo do parágrafo do Diário de Santa Faustina ]

Reflexão
[ reflexão associada ao parágrafo ]
```

Todos os campos devem possuir validação adequada.

---

# 8. Listagem administrativa

Atualize a listagem das reflexões.

Atualmente provavelmente existe uma coluna relacionada à data.

Remova essa coluna.

A listagem deverá apresentar algo semelhante a:

| Parágrafo | Descrição do parágrafo | Reflexão | Criado em | Ações |
|---|---|---|---|---|

Como `descricao_paragrafo` e `reflexao` podem ser textos grandes, não exiba necessariamente o texto completo na listagem.

Utilize um resumo/truncamento visual adequado.

---

# 9. Página de visualização

Atualize a página de detalhes da reflexão para apresentar claramente:

```text
Parágrafo 123

[texto completo do parágrafo]

Reflexão

[texto completo da reflexão]
```

A apresentação deve facilitar a leitura.

---

# 10. Nova Home — redesign completo

A página inicial atual está muito simples.

Precisamos realizar um **redesign completo da Home**.

Foi fornecida uma imagem de **Santa Faustina** para ser utilizada na página inicial.

### IMPORTANTE

Utilize a imagem fornecida como elemento visual principal da Home.

Primeiro localize a imagem disponibilizada no projeto/conversa e verifique a melhor forma de incorporá-la.

Não substitua a imagem por outra imagem aleatória da internet.

---

# 11. Direção visual da Home

A Home deve transmitir:

- espiritualidade;
- serenidade;
- elegância;
- simplicidade;
- leitura confortável;
- aspecto moderno;
- sensação de acolhimento.

Evite uma aparência genérica de sistema administrativo.

A Home deve parecer uma página pública dedicada às reflexões de Santa Faustina.

---

# 12. Sugestão de composição visual

Utilize a criatividade para criar uma composição bonita.

Uma possibilidade:

```text
┌─────────────────────────────────────────────┐
│                                             │
│              SANTA FAUSTINA                 │
│                                             │
│        [imagem de Santa Faustina]           │
│                                             │
│       Reflexões de Santa Faustina           │
│                                             │
│          Diário de Santa Faustina           │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │                                       │  │
│  │          Parágrafo 123                │  │
│  │                                       │  │
│  │  "Texto do parágrafo..."              │  │
│  │                                       │  │
│  │  Reflexão                             │  │
│  │                                       │  │
│  │  "Texto da reflexão..."               │  │
│  │                                       │  │
│  └───────────────────────────────────────┘  │
│                                             │
└─────────────────────────────────────────────┘
```

Isso é apenas uma referência.

Você pode criar uma composição melhor, desde que mantenha o foco no conteúdo.

---

# 13. Imagem de Santa Faustina

A imagem deve ser integrada de maneira elegante.

Avalie:

- tamanho;
- enquadramento;
- bordas;
- sombra;
- posicionamento;
- responsividade;
- relação entre a imagem e o texto.

Evite simplesmente colocar a imagem no topo da página sem composição visual.

Pode ser interessante utilizar:

- uma seção Hero;
- imagem lateral em desktop;
- imagem acima do conteúdo em mobile;
- card para a reflexão;
- elementos decorativos discretos.

---

# 14. Tipografia

A Home deve utilizar uma tipografia confortável para leitura de textos religiosos.

Pode utilizar uma combinação de:

- fonte serifada para os textos;
- fonte sans-serif para menus e elementos de interface.

Caso utilize fontes externas, avalie se isso realmente é necessário.

Não deixe a página excessivamente carregada.

---

# 15. Cores

Utilize uma paleta visual coerente com o contexto.

Sugestão de direção:

- tons claros;
- branco;
- bege/off-white;
- dourado discreto;
- tons suaves;
- marrom/cinza para textos.

Não é obrigatório seguir exatamente essas cores.

O objetivo é criar uma identidade visual elegante e espiritual.

Evite cores excessivamente saturadas.

---

# 16. Responsividade

A Home deverá funcionar muito bem em:

- desktop;
- notebook;
- tablet;
- celular.

Especial atenção deve ser dada à imagem de Santa Faustina.

No mobile:

- a imagem não pode ficar desproporcional;
- o texto deve permanecer confortável;
- o card da reflexão deve ocupar adequadamente a tela;
- não deve haver scroll horizontal.

---

# 17. Experiência do usuário

Ao entrar na Home, o usuário deve imediatamente entender:

1. Que o site é sobre Santa Faustina;
2. Que existe uma reflexão disponível;
3. Qual é o parágrafo apresentado;
4. Qual é o texto do parágrafo;
5. Qual é a reflexão.

A informação principal não deve ficar escondida.

---

# 18. Nova estrutura do conteúdo

A reflexão exibida deverá possuir três informações principais:

### Parágrafo

Exemplo:

```text
Parágrafo 123
```

### Descrição do parágrafo

O texto correspondente ao parágrafo do Diário.

### Reflexão

A reflexão cadastrada pelo administrador.

Visualmente, deixe claro que:

```text
Descrição do parágrafo
```

e

```text
Reflexão
```

são conteúdos diferentes.

---

# 19. Caso não existam registros

Se não houver nenhuma reflexão cadastrada no banco, a Home deverá continuar funcionando normalmente.

Apresente uma mensagem amigável, por exemplo:

```text
Ainda não há reflexões cadastradas.
```

Não permita que a aplicação gere erro por causa de:

```php
null
```

---

# 20. Remover referências antigas

Faça uma busca completa no projeto procurando referências ao campo:

```text
dia
```

e à antiga lógica de reflexão diária por data.

Verifique:

- Models;
- Controllers;
- Requests;
- Views;
- Routes;
- migrations;
- factories;
- seeders;
- testes;
- JavaScript;
- documentação;
- README.

Remova ou atualize tudo que estiver relacionado à antiga regra.

---

# 21. Seeders

Atualize os seeders para utilizar:

```text
paragrafo
descricao_paragrafo
reflexao
```

e não utilizar mais:

```text
dia
```

Os dados de exemplo devem deixar claro que são fictícios caso não sejam textos reais.

Não invente textos e atribua-os a Santa Faustina.

---

# 22. Testes

Atualize os testes existentes e crie novos quando necessário.

Os testes devem validar:

### Home

- Home retorna HTTP 200;
- uma reflexão cadastrada pode ser exibida;
- a seleção não depende da data;
- registros com Soft Delete não são selecionados;
- Home funciona quando não existem reflexões.

### CRUD

- cadastro funciona;
- edição funciona;
- exclusão funciona;
- `descricao_paragrafo` é salvo corretamente;
- validações funcionam.

### Banco

Verifique se a migration:

- remove `dia`;
- adiciona `descricao_paragrafo`;
- pode ser revertida.

---

# 23. Revisão do código

Depois de implementar as alterações, faça uma revisão completa.

Procure por:

```text
dia
reflection day
today
Carbon::today()
whereDate
```

ou qualquer outra lógica que ainda esteja tentando encontrar uma reflexão baseada na data.

A regra agora é exclusivamente:

```text
REFLEXÃO ALEATÓRIA
```

---

# 24. Não alterar desnecessariamente o painel administrativo

O redesign solicitado é principalmente para a **página pública inicial**.

O painel administrativo pode continuar utilizando uma interface simples e funcional.

Faça apenas as alterações necessárias para acomodar:

```text
paragrafo
descricao_paragrafo
reflexao
```

Não perca tempo redesenhando todo o painel caso ele já esteja funcionando adequadamente.

---

# 25. Validação final

Após concluir:

1. Execute as migrations;
2. Execute os seeders;
3. Execute os testes;
4. Inicie o Laravel;
5. Acesse a Home;
6. Verifique a imagem de Santa Faustina;
7. Verifique o layout em desktop;
8. Verifique o layout em mobile;
9. Cadastre uma nova reflexão;
10. Verifique se ela pode aparecer aleatoriamente na Home;
11. Edite a reflexão;
12. Exclua a reflexão;
13. Verifique se uma reflexão excluída não aparece mais;
14. Verifique se não existe mais nenhuma dependência da coluna `dia`.

---

# 26. Resultado esperado

Ao final desta alteração, o sistema deverá funcionar com o seguinte fluxo:

```text
ADMIN
   │
   ├── cadastra parágrafo
   ├── cadastra descrição do parágrafo
   └── cadastra reflexão
              │
              ▼
          BANCO MYSQL
              │
              ▼
         USUÁRIO ACESSA /
              │
              ▼
     Laravel seleciona aleatoriamente
       uma reflexão disponível
              │
              ▼
        HOME DO SISTEMA
              │
              ├── Santa Faustina
              ├── Número do parágrafo
              ├── Descrição do parágrafo
              └── Reflexão
```

A principal mudança conceitual é:

**Antes:**

```text
Data atual → buscar reflexão daquela data
```

**Agora:**

```text
Acesso à Home → selecionar aleatoriamente uma reflexão
```

---

# 27. Importante

Não apenas explique o que precisa ser alterado.

**Faça as alterações diretamente no projeto.**

Antes de modificar, analise a implementação atual para evitar quebrar funcionalidades existentes.

Ao final, informe:

- arquivos alterados;
- migrations criadas;
- alterações no banco;
- alterações no CRUD;
- alterações realizadas na Home;
- como a seleção aleatória foi implementada;
- testes executados;
- resultado dos testes;
- eventuais pendências.