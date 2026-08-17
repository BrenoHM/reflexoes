/**
 * "Ouvir reflexão" — lê a reflexão da Home em voz alta usando a Web Speech
 * API nativa do navegador (window.speechSynthesis). Não depende de nenhum
 * serviço externo: nada é enviado pela rede, nada é armazenado.
 *
 * O texto a ser lido vem de `window.reflectionSpeechData`, publicado pela
 * própria view (ver resources/views/home/index.blade.php) via `@js()`, já
 * sem tags HTML.
 */

const STATE = {
    IDLE: 'idle',
    SPEAKING: 'speaking',
    PAUSED: 'paused',
};

/**
 * Procura, entre as vozes disponíveis, a melhor opção em português:
 * pt-BR exato primeiro, depois qualquer variante "pt-BR*", depois
 * qualquer voz "pt-*". Retorna null se nenhuma existir (o navegador usará
 * sua voz padrão).
 */
export function getPortugueseVoice(voices) {
    if (!Array.isArray(voices) || voices.length === 0) {
        return null;
    }

    const byLang = (prefix) => voices.find(
        (voice) => typeof voice.lang === 'string' && voice.lang.toLowerCase().startsWith(prefix)
    );

    return (
        voices.find((voice) => voice.lang?.toLowerCase() === 'pt-br')
        ?? byLang('pt-br')
        ?? byLang('pt')
        ?? null
    );
}

/**
 * Em vários navegadores, speechSynthesis.getVoices() retorna uma lista
 * vazia na primeira chamada e só é preenchida de forma assíncrona.
 * Aguarda o evento "voiceschanged" (com um tempo limite de segurança) antes
 * de resolver, para não escolher a voz cedo demais.
 */
function loadVoices() {
    return new Promise((resolve) => {
        const initial = window.speechSynthesis.getVoices();

        if (initial.length > 0) {
            resolve(initial);
            return;
        }

        let resolved = false;

        const finish = () => {
            if (resolved) return;
            resolved = true;
            window.speechSynthesis.removeEventListener('voiceschanged', finish);
            resolve(window.speechSynthesis.getVoices());
        };

        window.speechSynthesis.addEventListener('voiceschanged', finish);

        // Alguns navegadores nunca disparam o evento; não travamos a
        // funcionalidade esperando para sempre.
        setTimeout(finish, 1000);
    });
}

/**
 * Monta o texto natural que será narrado, a partir dos dados já sem HTML.
 * A pontuação/quebra de parágrafo cria pausas naturais na fala.
 */
export function buildSpeechText({ paragrafo, descricaoParagrafo, reflexao }) {
    return [
        'Diário de Santa Faustina.',
        `Parágrafo ${paragrafo}.`,
        descricaoParagrafo,
        'Reflexão.',
        reflexao,
    ]
        .map((part) => String(part ?? '').trim())
        .filter((part) => part.length > 0)
        .join('\n\n');
}

export function initReflectionSpeech() {
    const root = document.getElementById('reflection-speech');

    if (!root || typeof window.reflectionSpeechData === 'undefined') {
        return;
    }

    if (!('speechSynthesis' in window) || typeof window.SpeechSynthesisUtterance === 'undefined') {
        // Mantém oculto (classe "hidden" no HTML): a Home segue funcionando
        // normalmente, só sem o recurso complementar de áudio.
        return;
    }

    const listenButton = root.querySelector('[data-speech-listen]');
    const pauseButton = root.querySelector('[data-speech-pause]');
    const resumeButton = root.querySelector('[data-speech-resume]');
    const stopButton = root.querySelector('[data-speech-stop]');
    const statusLabel = root.querySelector('[data-speech-status]');
    const errorLabel = root.querySelector('[data-speech-error]');

    if (!listenButton || !pauseButton || !resumeButton || !stopButton) {
        return;
    }

    const text = buildSpeechText(window.reflectionSpeechData);

    if (!text) {
        return;
    }

    let state = STATE.IDLE;
    let preloadedVoice = null;

    loadVoices().then((voices) => {
        preloadedVoice = getPortugueseVoice(voices);
    });

    function render() {
        root.dataset.state = state;

        listenButton.hidden = state !== STATE.IDLE;
        pauseButton.hidden = state !== STATE.SPEAKING;
        resumeButton.hidden = state !== STATE.PAUSED;
        stopButton.hidden = state === STATE.IDLE;

        if (statusLabel) {
            statusLabel.textContent = {
                [STATE.SPEAKING]: '🔊 Ouvindo...',
                [STATE.PAUSED]: '🔊 Leitura pausada',
            }[state] ?? '';

            // Pequena pulsação discreta só enquanto está lendo de fato.
            statusLabel.classList.toggle('animate-pulse', state === STATE.SPEAKING);
        }
    }

    function setState(next) {
        state = next;
        render();
    }

    function showError(message) {
        if (errorLabel) {
            errorLabel.textContent = message;
            errorLabel.hidden = false;
        }
    }

    function clearError() {
        if (errorLabel) {
            errorLabel.hidden = true;
            errorLabel.textContent = '';
        }
    }

    function speak() {
        clearError();

        // Nunca enfileirar falas: qualquer leitura em andamento é
        // cancelada antes de iniciar uma nova.
        window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'pt-BR';
        utterance.rate = 0.9;
        utterance.pitch = 1;
        utterance.volume = 1;

        const voice = getPortugueseVoice(window.speechSynthesis.getVoices()) ?? preloadedVoice;
        if (voice) {
            utterance.voice = voice;
        }

        utterance.onend = () => setState(STATE.IDLE);
        utterance.onerror = () => {
            showError('Não foi possível iniciar a leitura neste dispositivo.');
            setState(STATE.IDLE);
        };

        window.speechSynthesis.speak(utterance);
        setState(STATE.SPEAKING);
    }

    listenButton.addEventListener('click', speak);

    pauseButton.addEventListener('click', () => {
        window.speechSynthesis.pause();
        setState(STATE.PAUSED);
    });

    resumeButton.addEventListener('click', () => {
        window.speechSynthesis.resume();
        setState(STATE.SPEAKING);
    });

    stopButton.addEventListener('click', () => {
        window.speechSynthesis.cancel();
        setState(STATE.IDLE);
    });

    render();
    root.classList.remove('hidden');
}
