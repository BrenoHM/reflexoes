# Prompt — Adicionar Sistema de Doação via Pix

## Contexto

O sistema **Reflexões Diárias de Santa Faustina** já está funcionando.

Quero adicionar uma nova funcionalidade à página pública para permitir que os visitantes contribuam voluntariamente com o projeto através de **Pix**.

A intenção é ajudar a manter o projeto, hospedagem, domínio e demais custos relacionados à aplicação.

A doação deve ser apresentada de maneira **elegante, discreta e acolhedora**, sem transformar a Home em uma página de cobrança.

---

# 1. Dados do Pix

Utilize os seguintes dados:

**Chave Pix:**

```text
dc25aa47-1dbf-4562-ac1f-195223e94457
```

**Recebedor:**

```text
Breno Mol
```

**Banco:**

```text
Bradesco
```

Não altere a chave Pix.

---

# 2. Objetivo da funcionalidade

Adicionar à página inicial uma seção convidando o visitante, caso deseje, a contribuir voluntariamente com o projeto.

A seção deve permitir duas formas de pagamento:

### Opção 1 — QR Code

O visitante poderá escanear o QR Code utilizando o aplicativo do banco.

### Opção 2 — Pix Copia e Cola

O visitante poderá clicar em um botão para copiar o código Pix e utilizá-lo no aplicativo bancário.

A experiência deve ser extremamente simples.

---

# 3. Posicionamento na Home

Analise o layout atual da Home antes de implementar.

A seção de doação deve ficar em uma posição de fácil visualização, mas sem competir com a reflexão.

Minha sugestão inicial é colocar a seção:

```text
Reflexão
   ↓
Seção de doação
   ↓
Rodapé
```

Porém, avalie o layout atual e escolha a posição que proporcione a melhor experiência.

Não quero um popup invasivo.

Não quero:

- modal automático;
- anúncio;
- banner piscando;
- redirecionamento;
- mensagem insistente;
- solicitação de doação antes da reflexão.

A pessoa deve primeiro consumir o conteúdo principal e depois encontrar naturalmente a opção de colaborar.

---

# 4. Texto da seção

Crie uma mensagem acolhedora.

Algo semelhante a:

```text
Ajude a manter este projeto

Se esta reflexão tocou seu coração e você deseja contribuir
com a continuidade deste projeto, você pode fazer uma doação
de qualquer valor através do Pix.

Toda contribuição é bem-vinda e ajuda a manter este espaço.
```

Você pode melhorar o texto para combinar com a identidade visual da Home.

Não utilize linguagem agressiva ou comercial.

Evite frases como:

- "Doe agora!"
- "Você precisa doar!"
- "Não deixe de contribuir!"
- "Ajude urgentemente!"

A abordagem deve ser voluntária e respeitosa.

---

# 5. QR Code Pix

Crie um QR Code Pix funcional.

IMPORTANTE:

Não gere simplesmente um QR Code contendo a chave:

```text
dc25aa47-1dbf-4562-ac1f-195223e94457
```

O QR Code deve utilizar o padrão oficial de **Pix Copia e Cola / BR Code (EMV)**, permitindo que o aplicativo bancário reconheça a cobrança como Pix.

Como não haverá valor fixo, o QR Code deverá permitir uma contribuição de **qualquer valor**.

Utilize:

```text
Recebedor: Breno Mol
Banco: Bradesco
Chave Pix: dc25aa47-1dbf-4562-ac1f-195223e94457
```

Analise a forma mais adequada de gerar o payload Pix no Laravel.

Se for necessário utilizar uma biblioteca PHP confiável para geração do Pix BR Code, avalie uma biblioteca compatível com:

- PHP 8.5
- Laravel 13

Não instale uma biblioteca sem antes verificar se ela é compatível com a stack atual.

---

# 6. Pix Copia e Cola

Além do QR Code, disponibilize o código Pix Copia e Cola.

A interface pode ser semelhante a:

```text
Código Pix

[ código Pix Copia e Cola........................ ]

[ 📋 Copiar código Pix ]
```

Ao clicar no botão:

1. Copiar o código para o clipboard;
2. Alterar temporariamente o texto do botão para algo como:

```text
✓ Pix copiado!
```

3. Depois de alguns segundos, retornar para:

```text
Copiar código Pix
```

Utilize JavaScript moderno e simples.

Não é necessário adicionar uma biblioteca JavaScript apenas para isso.

Utilize a Clipboard API quando disponível.

---

# 7. QR Code

O QR Code deve ter boa qualidade visual.

Requisitos:

- tamanho adequado para leitura;
- fundo com bom contraste;
- margem adequada;
- não distorcer a proporção;
- funcionar em dispositivos móveis;
- funcionar em desktop;
- não ficar excessivamente pequeno.

Abaixo ou ao lado do QR Code, exibir:

```text
Escaneie o QR Code
com o aplicativo do seu banco
```

---

# 8. Layout responsivo

No desktop, pode utilizar uma composição semelhante a:

```text
┌───────────────────────────────────────────────┐
│                                               │
│             Ajude este projeto                │
│                                               │
│  ┌──────────────┐   Faça sua contribuição     │
│  │              │                            │
│  │   QR CODE    │   Pix Copia e Cola          │
│  │              │   [ código ]                │
│  │              │   [ Copiar Pix ]             │
│  └──────────────┘                            │
│                                               │
└───────────────────────────────────────────────┘
```

No celular, organize verticalmente:

```text
Ajude este projeto

Mensagem

[ QR CODE ]

Escaneie com seu banco

Código Pix

[ código ]

[ Copiar Pix ]

Recebedor: Breno Mol
```

O layout deve se adaptar naturalmente.

---

# 9. Identidade visual

A seção de doação deve seguir a identidade visual já criada para o projeto.

Não crie uma seção visualmente desconectada da Home.

Utilize:

- mesmas fontes;
- mesma paleta;
- mesmos estilos de borda;
- mesmos arredondamentos;
- mesma linguagem visual.

A seção deve parecer parte natural do site.

---

# 10. Incentivo à contribuição

Gostaria que você utilizasse sua criatividade para tornar a contribuição convidativa sem ser invasiva.

Pode incluir uma pequena frase como:

> "Se este espaço lhe fez bem, sua contribuição ajuda a mantê-lo vivo."

Ou algo semelhante.

Também pode utilizar uma mensagem de agradecimento:

> "Obrigado por ajudar a manter este espaço de fé e reflexão."

Escolha a melhor abordagem visual e textual.

---

# 11. Não utilizar valor fixo

Não deve existir um valor predefinido de doação.

A pessoa deverá escolher livremente o valor dentro do aplicativo do banco.

Não criar:

```text
R$ 5
R$ 10
R$ 20
R$ 50
```

nem botões de valores.

A proposta é:

**"Doe qualquer valor que desejar."**

---

# 12. Configuração

Não espalhe a chave Pix diretamente pelo código em vários arquivos.

Centralize a configuração.

Por exemplo, avalie criar:

```text
config/donation.php
```

contendo os dados necessários:

```php
return [
    'pix_key' => '...',
    'receiver_name' => '...',
    'bank' => '...',
];
```

Ou outra solução que considere mais adequada para Laravel.

A chave não é um segredo de autenticação, portanto não há necessidade de tratá-la como senha.

Porém, deve existir apenas uma fonte de configuração para evitar duplicação.

---

# 13. Variáveis de ambiente

Avalie se é melhor utilizar `.env` para os dados de configuração do Pix.

Caso considere apropriado, utilize algo semelhante a:

```text
PIX_KEY=
PIX_RECEIVER_NAME=
PIX_BANK=
```

e documente no `.env.example`.

Não coloque credenciais bancárias, senhas ou tokens no código.

A chave Pix fornecida é pública e pode ser exibida aos visitantes.

---

# 14. Teste do Pix

Após implementar, valide cuidadosamente o payload Pix.

Não considere a tarefa concluída apenas porque o QR Code foi gerado visualmente.

É necessário verificar se:

1. O QR Code é válido;
2. O aplicativo bancário consegue reconhecê-lo;
3. O recebedor aparece corretamente;
4. A chave Pix está correta;
5. O usuário pode escolher o valor;
6. O código Copia e Cola corresponde ao QR Code;
7. O botão de copiar funciona.

Não realize uma transferência real.

A validação deve ser feita sem efetuar pagamento.

---

# 15. Segurança

A funcionalidade de doação não deve:

- armazenar dados bancários do visitante;
- pedir CPF;
- pedir senha bancária;
- pedir dados de cartão;
- processar pagamentos diretamente;
- armazenar informações financeiras dos usuários.

O site apenas fornecerá os dados necessários para que o visitante realize o Pix através do aplicativo de seu banco.

---

# 16. Acessibilidade

A seção deve possuir:

- textos legíveis;
- contraste adequado;
- `alt` apropriado para o QR Code;
- botões acessíveis;
- foco visível;
- navegação por teclado;
- feedback visual ao copiar.

O QR Code pode ter um `alt` semelhante a:

```text
QR Code para contribuição via Pix
```

---

# 17. Página pública somente

Não é necessário criar uma funcionalidade administrativa para controlar as doações.

A seção será fixa na Home.

Não implementar:

- histórico de doações;
- controle de pagamentos;
- integração com gateway;
- webhook;
- confirmação automática;
- painel financeiro.

O objetivo agora é simplesmente facilitar a contribuição voluntária via Pix.

---

# 18. Testes

Adicione testes quando fizer sentido.

Verifique principalmente:

- Home continua retornando HTTP 200;
- dados do Pix são carregados corretamente;
- QR Code é gerado;
- payload Pix contém a chave correta;
- botão de copiar está presente;
- página continua responsiva.

Se a geração do QR Code puder ser testada de forma automatizada, crie um teste apropriado.

---

# 19. Revisão da Home

Ao implementar a seção de doação, revise a Home inteira.

O resultado final deve manter uma hierarquia clara:

```text
Santa Faustina
      ↓
Título do projeto
      ↓
Reflexão
      ↓
Convite para contribuir
      ↓
QR Code + Pix Copia e Cola
      ↓
Rodapé
```

A reflexão continua sendo o **elemento principal da página**.

A doação é um convite secundário.

---

# 20. Resultado esperado

Ao finalizar, quero conseguir acessar a Home e encontrar uma seção semelhante a:

```text
────────────────────────────────────────────

          Ajude a manter este projeto

 Se esta reflexão tocou seu coração e você
 deseja contribuir com a continuidade deste
 espaço, você pode fazer uma doação de
 qualquer valor através do Pix.

                 ┌───────────┐
                 │           │
                 │  QR CODE  │
                 │           │
                 └───────────┘

          Escaneie com seu banco

             Pix Copia e Cola

     [ código Pix Copia e Cola ]

              [ Copiar Pix ]

             Recebedor:
              Breno Mol

        Obrigado por sua contribuição!

────────────────────────────────────────────
```

O layout real deve ser mais elegante e integrado ao design da Home.

---

# 21. Regra final

Não apenas descreva as alterações.

**Implemente diretamente no projeto existente.**

Antes de modificar:

1. Analise a Home atual;
2. Analise como o Laravel está estruturado;
3. Verifique as dependências existentes;
4. Verifique como o CSS/Tailwind está organizado;
5. Identifique o melhor local para a seção.

Depois:

1. Implemente a geração do Pix;
2. Crie o QR Code;
3. Adicione o Pix Copia e Cola;
4. Implemente o botão de copiar;
5. Faça o redesign necessário;
6. Teste;
7. Revise o código.

Ao final, informe:

- arquivos criados;
- arquivos alterados;
- dependências adicionadas;
- como o QR Code Pix foi gerado;
- como o Pix Copia e Cola foi implementado;
- testes realizados;
- resultado dos testes;
- eventuais pendências.