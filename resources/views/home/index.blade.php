<x-public-layout>
    <div class="relative max-w-6xl mx-auto">
        {{-- Elementos decorativos discretos --}}
        <div class="pointer-events-none absolute -top-10 -left-16 h-72 w-72 rounded-full bg-amber-200/30 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute top-1/3 -right-16 h-72 w-72 rounded-full bg-stone-300/20 blur-3xl" aria-hidden="true"></div>

        <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

            {{-- Coluna: retrato + identificação --}}
            <div class="lg:col-span-5 flex flex-col items-center text-center lg:items-start lg:text-left">
                <p class="text-xs font-sans font-semibold tracking-[0.25em] uppercase text-amber-700">
                    Diário de Santa Faustina
                </p>

                <h1 class="font-display text-4xl sm:text-5xl text-stone-900 mt-3">
                    Santa Faustina
                </h1>

                <p class="font-reading text-lg text-stone-500 mt-2">
                    Reflexões Diárias
                </p>

                <div class="relative mt-8 w-48 sm:w-56 lg:w-full lg:max-w-xs">
                    <div class="absolute inset-0 translate-x-3 translate-y-3 rounded-[2rem] bg-amber-200/40 blur-xl" aria-hidden="true"></div>
                    <img
                        src="{{ asset('assets/images/Maria_Faustyna_Kowalska.jpg') }}"
                        alt="Retrato de Santa Faustina Kowalska"
                        class="relative w-full aspect-[3/4] object-cover rounded-[2rem] ring-4 ring-white shadow-xl"
                    >
                </div>
            </div>

            {{-- Coluna: card da reflexão --}}
            <div class="lg:col-span-7">
                <div class="bg-white/90 backdrop-blur-sm shadow-sm border border-stone-200 rounded-3xl px-6 py-10 sm:px-10 sm:py-12">
                    @if ($reflection)
                        <span class="inline-block text-xs font-sans font-semibold tracking-widest uppercase text-amber-800 bg-amber-50 border border-amber-200 rounded-full px-4 py-1.5">
                            Parágrafo {{ $reflection->paragrafo }}
                        </span>

                        <div class="mt-6">
                            <p class="text-xs font-sans font-semibold tracking-widest uppercase text-stone-400 mb-3">
                                Diário de Santa Faustina
                            </p>
                            <div class="relative">
                                <span class="absolute -top-3 left-0 text-6xl font-display text-amber-200 select-none" aria-hidden="true">&ldquo;</span>
                                <blockquote class="font-reading italic text-lg sm:text-xl leading-relaxed text-stone-700 whitespace-pre-line pl-8">
                                    {{ $reflection->descricao_paragrafo }}
                                </blockquote>
                            </div>
                        </div>

                        <div class="my-8 flex items-center gap-3" aria-hidden="true">
                            <span class="h-px flex-1 bg-stone-200"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>
                            <span class="h-px flex-1 bg-stone-200"></span>
                        </div>

                        <p class="text-xs font-sans font-semibold tracking-widest uppercase text-stone-400 mb-2">
                            Reflexão
                        </p>
                        <p class="font-reading text-base sm:text-lg leading-relaxed text-stone-700 whitespace-pre-line">
                            {{ $reflection->reflexao }}
                        </p>

                        {{-- "Ouvir reflexão": Web Speech API, ver resources/js/features/text-to-speech.js.
                             Começa oculto (classe "hidden") e só aparece se o navegador suportar. --}}
                        <div id="reflection-speech" class="hidden mt-8 pt-6 border-t border-stone-200" data-state="idle">
                            <div class="flex flex-col items-center gap-3">
                                <p data-speech-status class="text-sm font-sans font-medium text-amber-700 min-h-[1.25rem]" aria-live="polite"></p>

                                <div class="flex flex-wrap items-center justify-center gap-3">
                                    <button
                                        type="button"
                                        data-speech-listen
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-sans font-semibold bg-stone-900 text-white hover:bg-stone-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2"
                                    >
                                        <span aria-hidden="true">🔊</span> Ouvir reflexão
                                    </button>

                                    <button
                                        type="button"
                                        data-speech-pause
                                        hidden
                                        aria-label="Pausar leitura"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-sans font-semibold bg-white border border-stone-300 text-stone-700 hover:bg-stone-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2"
                                    >
                                        <span aria-hidden="true">⏸</span> Pausar
                                    </button>

                                    <button
                                        type="button"
                                        data-speech-resume
                                        hidden
                                        aria-label="Continuar leitura"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-sans font-semibold bg-stone-900 text-white hover:bg-stone-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2"
                                    >
                                        <span aria-hidden="true">▶</span> Continuar
                                    </button>

                                    <button
                                        type="button"
                                        data-speech-stop
                                        hidden
                                        aria-label="Parar leitura"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-sans font-semibold bg-white border border-stone-300 text-stone-600 hover:bg-stone-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2"
                                    >
                                        <span aria-hidden="true">⏹</span> Parar
                                    </button>
                                </div>

                                <p data-speech-error hidden role="alert" class="text-xs text-stone-500 italic"></p>
                            </div>
                        </div>

                        <script>
                            window.reflectionSpeechData = @js($speechData);
                        </script>
                    @else
                        <div class="py-6 text-center lg:text-left">
                            <p class="text-lg text-stone-600">
                                Ainda não há reflexões cadastradas.
                            </p>
                            <p class="text-stone-500 mt-1">
                                Volte mais tarde.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Doação via Pix: convite secundário, só depois da reflexão --}}
        <section class="relative mt-16 sm:mt-24" aria-labelledby="doacao-heading">
            <div class="bg-white/90 backdrop-blur-sm shadow-sm border border-stone-200 rounded-3xl px-6 py-10 sm:px-10 sm:py-12">
                <div class="max-w-xl mx-auto text-center">
                    <p class="text-xs font-sans font-semibold tracking-widest uppercase text-amber-700">
                        Contribuição voluntária
                    </p>
                    <h2 id="doacao-heading" class="font-display text-2xl sm:text-3xl text-stone-900 mt-2">
                        Ajude a manter este projeto
                    </h2>
                    <p class="font-reading text-stone-600 mt-4 leading-relaxed">
                        Se esta reflexão tocou seu coração, você pode contribuir com a
                        continuidade deste espaço através do Pix, doando o valor que
                        desejar. Toda contribuição é bem-vinda.
                    </p>
                </div>

                <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-10 sm:gap-8 items-center">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div
                            role="img"
                            aria-label="QR Code para contribuição via Pix"
                            class="bg-white rounded-2xl border border-stone-200 p-3 shadow-sm [&>svg]:w-full [&>svg]:h-auto [&>svg]:max-w-[220px]"
                        >
                            {{-- SVG gerado no servidor a partir da config do Pix, não é
                                 conteúdo enviado por usuário — seguro para {!! !!} --}}
                            {!! $pixQrCodeSvg !!}
                        </div>
                        <p class="text-sm text-stone-500">
                            Escaneie o QR Code<br>com o aplicativo do seu banco
                        </p>
                    </div>

                    <div x-data="{ copied: false }" class="flex flex-col gap-3">
                        <p class="text-xs font-sans font-semibold tracking-widest uppercase text-stone-400">
                            Pix Copia e Cola
                        </p>

                        <div class="font-mono text-xs leading-relaxed text-stone-600 bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 break-all select-all max-h-28 overflow-y-auto">
                            {{ $pixPayload }}
                        </div>

                        <button
                            type="button"
                            @click="navigator.clipboard && navigator.clipboard.writeText(@js($pixPayload)).then(() => { copied = true; setTimeout(() => copied = false, 2500) })"
                            aria-live="polite"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full text-sm font-sans font-semibold bg-stone-900 text-white hover:bg-stone-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2"
                        >
                            <span x-show="!copied">Copiar código Pix</span>
                            <span x-show="copied" x-cloak>&check; Pix copiado!</span>
                        </button>

                        <p class="text-sm text-stone-500 mt-2">
                            Recebedor: <span class="text-stone-700">{{ $pixReceiverName }}</span>
                            <br>
                            Banco: <span class="text-stone-700">{{ $pixBank }}</span>
                        </p>
                    </div>
                </div>

                <p class="text-center text-sm text-stone-500 italic mt-10">
                    Obrigado por ajudar a manter este espaço de fé e reflexão.
                </p>
            </div>
        </section>
    </div>
</x-public-layout>
