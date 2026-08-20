<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // O Render fica atrás de um proxy reverso que termina o HTTPS e
        // repassa a requisição internamente por HTTP, sinalizando o
        // protocolo original via X-Forwarded-Proto. Sem confiar nesse
        // proxy, url()/asset()/redirect() geram links http:// e o cookie
        // de sessão "secure" nunca seria enviado. Não dá para restringir
        // por IP porque o Render não publica um range fixo, então
        // confiamos em qualquer proxy imediato (o próprio container nunca
        // fica exposto diretamente à internet).
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
