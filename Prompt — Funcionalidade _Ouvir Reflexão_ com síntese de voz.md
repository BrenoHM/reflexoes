# Prompt — Funcionalidade "Ouvir Reflexão" com síntese de voz

## Contexto

O sistema **Reflexões Diárias de Santa Faustina** já está em desenvolvimento utilizando:

- PHP 8.5
- Laravel 13
- MySQL
- Blade
- Tailwind CSS
- Vite
- JavaScript

A página inicial já apresenta uma reflexão/parágrafo de Santa Faustina selecionado aleatoriamente do banco de dados.

Agora quero implementar uma nova funcionalidade:

# 🔊 Ouvir

O usuário deverá poder clicar em um botão **"Ouvir"** para que o navegador leia em voz alta o conteúdo apresentado na Home.

A ideia é semelhante à funcionalidade de leitura existente no site de referência:

```text
https://yahyom.me/p/yahyom
```

Porém, **não copie o código ou o design do site de referência**.

Apenas utilize-o como referência conceitual para a experiência de usuário.

---

# 1. Tecnologia a utilizar

Utilize preferencialmente a **Web Speech API nativa do navegador**, especificamente:

```javascript
window.speechSynthesis
```

e:

```javascript
SpeechSynthesisUtterance
```

**Não adicione uma biblioteca externa de Text-to-Speech se isso não for necessário.**

A funcionalidade deve funcionar utilizando os recursos nativos do navegador.

Não utilizar:

- API paga;
- OpenAI API;
- Google Cloud Text-to-Speech;
- AWS Polly;
- Azure Speech;
- ResponsiveVoice;
- serviços externos;
- servidor próprio para gerar MP3.

O objetivo é implementar a funcionalidade **gratuitamente e sem custo adicional de infraestrutura**.

---

# 2. Objetivo

Ao acessar a Home, o usuário verá o conteúdo normalmente.

Abaixo ou em posição visualmente adequada ao conteúdo, deverá existir um botão:

```text
🔊 Ouvir
```

Ao clicar, o navegador deverá começar a narrar:

1. O número do parágrafo;
2. A descrição do parágrafo;
3. A reflexão.

---

# 3. Conteúdo que deverá ser narrado

A Home atualmente possui os seguintes dados:

```text
paragrafo
descricao_paragrafo
reflexao
```

Monte o texto que será enviado ao `SpeechSynthesisUtterance` de maneira natural.

Exemplo:

```text
Diário de Santa Faustina.

Parágrafo 123.

[descrição do parágrafo]

Reflexão.

[texto da reflexão]
```

Não leia HTML.

Não envie tags HTML para o sintetizador.

O texto deve ser obtido de forma segura a partir dos dados exibidos na página.

---

# 4. Idioma

A voz deverá ser configurada preferencialmente para:

```text
pt-BR
```

Utilize:

```javascript
utterance.lang = 'pt-BR';
```

Antes de iniciar a leitura, procure entre as vozes disponíveis uma voz brasileira/português do Brasil.

Utilize:

```javascript
speechSynthesis.getVoices()
```

e procure preferencialmente:

```text
pt-BR
```

Caso não exista uma voz `pt-BR`, procure uma voz em português compatível.

Caso nenhuma voz em português esteja disponível, utilize a voz padrão do navegador como fallback.

---

# 5. Seleção da voz

Crie uma função responsável por encontrar a melhor voz disponível.

Exemplo conceitual:

```javascript
function getPortugueseVoice() {
    // procurar pt-BR
    // depois procurar pt
    // depois utilizar voz padrão
}
```

Não fixe o nome de uma voz específica.

Isso é importante porque os nomes das vozes podem variar de acordo com:

- Windows;
- Android;
- iOS;
- Chrome;
- Edge;
- Safari;
- outros ambientes.

O código deve ser compatível com diferentes dispositivos.

---

# 6. Problema do getVoices()

Considere que em alguns navegadores:

```javascript
speechSynthesis.getVoices()
```

pode inicialmente retornar uma lista vazia.

Implemente corretamente o evento:

```javascript
speechSynthesis.onvoiceschanged
```

quando necessário.

A seleção da voz deve funcionar mesmo quando as vozes são carregadas de forma assíncrona.

---

# 7. Botão inicial

Inicialmente deverá aparecer:

```text
🔊 Ouvir
```

O botão deve ter aparência integrada ao design atual da Home.

Não criar um botão visualmente genérico.

Ele deve seguir:

- cores;
- bordas;
- tipografia;
- espaçamento;
- arredondamento;
- identidade visual

já utilizados na página.

---

# 8. Quando começar a reprodução

Ao clicar em:

```text
🔊 Ouvir
```

deverá:

1. Verificar se existe suporte à Web Speech API;
2. Cancelar qualquer leitura anterior;
3. Criar um novo `SpeechSynthesisUtterance`;
4. Definir idioma `pt-BR`;
5. Selecionar a melhor voz disponível;
6. Iniciar a reprodução;
7. Atualizar o estado visual do botão.

Durante a reprodução, o botão deverá indicar claramente que o sistema está lendo.

Por exemplo:

```text
🔊 Ouvindo...
```

ou:

```text
⏸ Pausar
```

---

# 9. Controles de reprodução

Quero que o usuário tenha controle sobre a leitura.

Durante a reprodução, disponibilize:

```text
⏸ Pausar
⏹ Parar
```

Comportamento:

### Pausar

Ao clicar:

```text
⏸ Pausar
```

a leitura deve ser pausada através de:

```javascript
speechSynthesis.pause()
```

O botão deverá mudar para:

```text
▶ Continuar
```

---

# 10. Continuar

Quando a leitura estiver pausada, mostrar:

```text
▶ Continuar
```

Ao clicar:

```javascript
speechSynthesis.resume()
```

A leitura deverá continuar de onde parou.

Não reiniciar a leitura desde o começo.

---

# 11. Parar

Adicionar:

```text
⏹ Parar
```

Ao clicar:

```javascript
speechSynthesis.cancel()
```

A leitura deverá ser interrompida.

Após parar:

```text
🔊 Ouvir
```

deve voltar a aparecer.

---

# 12. Quando terminar automaticamente

Utilize o evento:

```javascript
utterance.onend
```

Quando a leitura terminar naturalmente:

1. Restaurar o estado inicial;
2. Remover o estado "Ouvindo";
3. Mostrar novamente:

```text
🔊 Ouvir
```

Não deixar o botão preso em estado de reprodução.

---

# 13. Tratamento de erros

Implemente:

```javascript
utterance.onerror
```

Caso ocorra um erro:

- não quebrar a página;
- restaurar o botão;
- informar discretamente que não foi possível reproduzir a leitura.

Exemplo:

```text
Não foi possível iniciar a leitura neste dispositivo.
```

Evite mostrar mensagens técnicas para o usuário.

---

# 14. Verificação de suporte

Antes de disponibilizar a funcionalidade, verifique:

```javascript
' speechSynthesis ' in window
```

ou uma verificação equivalente corretamente implementada.

Caso o navegador não ofereça suporte:

- não deixar um botão que não funciona;
- ocultar o botão de leitura ou apresentar uma mensagem discreta.

A Home deve continuar funcionando normalmente.

A funcionalidade de áudio é complementar.

---

# 15. Não utilizar autoplay

**Nunca iniciar a leitura automaticamente.**

A leitura somente deve começar após uma ação explícita do usuário:

```text
clique no botão "Ouvir"
```

Isso é importante tanto para a experiência do usuário quanto para as políticas dos navegadores.

---

# 16. Controle de múltiplas execuções

Evite que múltiplas falas sejam colocadas na fila.

Por exemplo:

O usuário clica:

```text
Ouvir
```

e rapidamente clica novamente.

Não queremos:

```text
fala 1
fala 2
fala 3
```

enfileiradas.

Antes de iniciar uma nova leitura:

```javascript
speechSynthesis.cancel();
```

e então iniciar apenas a nova fala.

---

# 17. Troca de reflexão

Como a Home seleciona aleatoriamente uma reflexão quando é carregada, considere o seguinte cenário:

```text
Usuário acessa Home
        ↓
Reflexão A
        ↓
Usuário começa a ouvir
        ↓
Página é recarregada
        ↓
Reflexão B
```

Ao recarregar a página, a leitura anterior naturalmente deverá ser encerrada.

Não armazenar a fala no servidor.

---

# 18. Texto acessível ao JavaScript

Evite colocar o conteúdo diretamente dentro de strings JavaScript geradas manualmente.

Utilize uma abordagem segura.

Por exemplo, disponibilize os dados através de atributos `data-*`, JSON seguro ou outra abordagem adequada ao Blade/Laravel.

Tenha cuidado especial com:

- aspas;
- acentos;
- quebras de linha;
- caracteres especiais;
- HTML;
- conteúdo proveniente do banco.

O conteúdo do banco não deve permitir injeção de JavaScript.

---

# 19. Não narrar HTML

O conteúdo poderá conter formatação ou caracteres especiais.

Antes de enviar o texto para:

```javascript
SpeechSynthesisUtterance
```

garanta que o texto seja apropriado para leitura.

Se necessário, remova tags HTML.

O sintetizador deve receber somente texto.

---

# 20. Estrutura da leitura

A narração deve soar natural.

Evite simplesmente concatenar:

```text
123 texto texto reflexão texto
```

Prefira:

```text
Diário de Santa Faustina.

Parágrafo 123.

[descrição do parágrafo]

Reflexão.

[reflexão]
```

As pausas naturais decorrentes da pontuação devem ajudar na compreensão.

---

# 21. Configuração de velocidade

Utilize uma velocidade confortável para leitura espiritual/reflexiva.

A propriedade:

```javascript
utterance.rate
```

pode ser configurada.

Sugestão inicial:

```javascript
utterance.rate = 0.9;
```

Mas avalie o resultado real no navegador.

A voz não deve ser excessivamente rápida.

Utilize uma velocidade que favoreça a compreensão.

---

# 22. Pitch e volume

Utilize valores naturais.

Por exemplo:

```javascript
utterance.pitch = 1;
utterance.volume = 1;
```

Não aplique efeitos exagerados.

A prioridade é uma leitura clara e confortável.

---

# 23. Interface sugerida

A interface pode ficar abaixo da reflexão:

```text
┌─────────────────────────────────────────────┐
│                                             │
│             Parágrafo 123                   │
│                                             │
│     [Descrição do parágrafo...]             │
│                                             │
│     ───────────────────────────────         │
│                                             │
│                Reflexão                     │
│                                             │
│     [Texto da reflexão...]                  │
│                                             │
│                                             │
│             🔊 Ouvir reflexão                │
│                                             │
└─────────────────────────────────────────────┘
```

Ao iniciar:

```text
┌─────────────────────────────────────────────┐
│                                             │
│             Parágrafo 123                   │
│                                             │
│     [Descrição do parágrafo...]             │
│                                             │
│                Reflexão                     │
│                                             │
│     [Texto da reflexão...]                  │
│                                             │
│                                             │
│       🔊 Ouvindo...                         │
│                                             │
│       ⏸ Pausar     ⏹ Parar                 │
│                                             │
└─────────────────────────────────────────────┘
```

Quando pausado:

```text
🔊 Leitura pausada

▶ Continuar     ⏹ Parar
```

---

# 24. Design

O botão deve combinar com a identidade visual do projeto.

Como o site possui uma proposta religiosa e contemplativa, evite:

- cores muito fortes;
- animações exageradas;
- efeitos chamativos;
- barras de áudio complexas;
- visual semelhante a um player de música.

A funcionalidade deve parecer uma **leitura guiada da reflexão**.

Pode utilizar uma pequena animação enquanto estiver lendo, desde que seja discreta.

Exemplo:

```text
🔊  Ouvindo...
```

com uma pequena animação de pulsação no ícone.

---

# 25. Mobile

A funcionalidade deve funcionar muito bem no celular.

No mobile:

- botão grande o suficiente para toque;
- controles não podem ficar apertados;
- texto não pode transbordar;
- não pode haver scroll horizontal;
- os controles podem ficar em uma linha ou empilhados conforme o espaço.

Teste principalmente em viewport mobile.

---

# 26. Acessibilidade

Os controles devem ser acessíveis.

Utilize:

- elementos `<button>` reais;
- `aria-label` quando necessário;
- estados claros;
- foco por teclado;
- contraste adequado.

Não utilize apenas:

```html
<div onclick="...">
```

para os controles.

---

# 27. Organização do JavaScript

Não coloque todo o código diretamente em:

```text
resources/views/home/index.blade.php
```

Se o projeto já possui uma estrutura JavaScript adequada, crie um módulo específico.

Por exemplo:

```text
resources/js/
└── speech.js
```

ou:

```text
resources/js/
└── features/
    └── text-to-speech.js
```

Escolha a estrutura que melhor se encaixa no projeto existente.

Evite criar arquivos desnecessários.

---

# 28. Não adicionar biblioteca externa

Antes de instalar qualquer dependência, confirme se ela realmente é necessária.

A preferência é:

```text
JavaScript
   ↓
Web Speech API
   ↓
speechSynthesis
```

Sem npm package adicional.

Não instalar:

```text
ResponsiveVoice
Howler
Tone.js
```

ou outras bibliotecas apenas para resolver algo que a API nativa já oferece.

---

# 29. Considerações sobre navegadores

A implementação deve considerar diferenças entre:

- Chrome;
- Edge;
- Safari;
- Firefox;
- Android;
- iOS;
- Windows.

Não assumir que todas as plataformas possuem exatamente as mesmas vozes.

A seleção deve ser dinâmica.

A funcionalidade deve degradar de forma elegante caso determinada voz não esteja disponível.

---

# 30. Importante sobre as vozes

Não é necessário baixar ou armazenar arquivos de voz.

Não criar arquivos:

```text
.mp3
.wav
.ogg
```

para cada reflexão.

O texto será sintetizado pelo mecanismo de voz disponível no dispositivo.

Isso mantém o projeto:

- simples;
- leve;
- gratuito;
- sem necessidade de armazenamento adicional.

---

# 31. Não alterar o banco

Essa funcionalidade não exige alteração no banco de dados.

Não criar nenhuma coluna adicional.

O áudio será gerado dinamicamente a partir dos campos existentes:

```text
paragrafo
descricao_paragrafo
reflexao
```

---

# 32. Testes

Crie ou atualize testes quando apropriado.

No mínimo, verifique manualmente:

### Estado inicial

```text
🔊 Ouvir
```

### Ao clicar

```text
🔊 Ouvindo...
⏸ Pausar
⏹ Parar
```

### Pausar

```text
▶ Continuar
⏹ Parar
```

### Continuar

A leitura deve continuar do ponto em que parou.

### Parar

Voltar para:

```text
🔊 Ouvir
```

### Finalização

Após terminar automaticamente:

```text
🔊 Ouvir
```

### Erro

O botão deve voltar ao estado normal.

### Navegador sem suporte

A Home deve continuar funcionando sem quebrar.

---

# 33. Teste de conteúdo

Teste com textos contendo:

- acentos;
- pontuação;
- aspas;
- parágrafos longos;
- quebras de linha;
- caracteres especiais.

Exemplo:

```text
"Meu Deus, confio em Vós!"
```

Verifique se a leitura ocorre corretamente.

---

# 34. Teste de textos longos

A API de síntese de voz pode apresentar diferenças de comportamento dependendo do navegador para textos muito grandes.

Por isso, analise se é necessário dividir textos excessivamente longos em partes.

**Não implemente divisão em chunks automaticamente sem necessidade.**

Primeiro teste o tamanho normal das reflexões existentes.

Se houver necessidade de dividir o texto, faça isso de forma que:

- não corte palavras;
- respeite frases;
- mantenha a ordem;
- permita pausar;
- permita continuar;
- permita cancelar;
- não gere múltiplas falas simultâneas.

---

# 35. Privacidade

Não enviar o conteúdo das reflexões para APIs externas.

A implementação deve funcionar localmente através da API de síntese de voz do navegador.

Não armazenar:

- áudio;
- voz do usuário;
- dados pessoais;
- histórico de reprodução.

---

# 36. Resultado esperado

Ao final, a Home deverá permitir uma experiência semelhante a:

```text
        Santa Faustina

     Reflexão do Dia

       Parágrafo 123

   [texto do parágrafo]

         Reflexão

   [texto da reflexão]


      🔊 Ouvir reflexão
```

Ao clicar:

```text
      🔊 Ouvindo...

      ⏸ Pausar
      ⏹ Parar
```

Ao pausar:

```text
      ▶ Continuar
      ⏹ Parar
```

Ao terminar:

```text
      🔊 Ouvir reflexão
```

---

# 37. Regra final

Não apenas explique como implementar.

**Implemente diretamente no projeto existente.**

Antes de começar:

1. Analise a estrutura atual;
2. Localize a Home;
3. Identifique como os dados da reflexão são enviados para a View;
4. Verifique a estrutura atual de JavaScript;
5. Verifique a configuração do Vite;
6. Verifique o Tailwind;
7. Identifique o melhor local para integrar os controles.

Depois:

1. Implemente a Web Speech API;
2. Implemente seleção de voz `pt-BR`;
3. Implemente fallback de voz;
4. Implemente `Ouvir`;
5. Implemente `Pausar`;
6. Implemente `Continuar`;
7. Implemente `Parar`;
8. Implemente tratamento de erros;
9. Implemente tratamento de navegadores sem suporte;
10. Integre ao design atual;
11. Teste em desktop;
12. Teste em mobile;
13. Revise o código.

**Não instale uma biblioteca externa se a Web Speech API nativa atender aos requisitos.**

Ao final, informe:

- arquivos criados;
- arquivos alterados;
- dependências adicionadas, se houver;
- como a Web Speech API foi implementada;
- como a voz `pt-BR` é selecionada;
- como os controles funcionam;
- navegadores/ambientes considerados;
- testes realizados;
- resultado dos testes;
- eventuais limitações encontradas.