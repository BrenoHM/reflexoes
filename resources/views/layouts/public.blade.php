<!DOCTYPE html>
<html lang="pt-BR" class="overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts: Playfair Display para títulos, Lora para leitura -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:600,700|lora:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-display { font-family: 'Playfair Display', ui-serif, Georgia, serif; }
            .font-reading { font-family: 'Lora', ui-serif, Georgia, serif; }
        </style>
    </head>
    <body class="min-h-screen flex flex-col overflow-x-hidden bg-gradient-to-b from-amber-50/60 via-stone-50 to-stone-100 text-stone-800 antialiased font-reading">
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
            {{ $slot }}
        </main>

        <footer class="py-6 text-center text-xs text-stone-400 border-t border-stone-200/70">
            {{ config('app.name') }}
            <span aria-hidden="true">&middot;</span>
            <a href="{{ route('login') }}" class="hover:text-stone-600 underline decoration-stone-300">Área administrativa</a>
        </footer>
    </body>
</html>
