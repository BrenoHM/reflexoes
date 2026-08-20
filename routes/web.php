<?php

use App\Http\Controllers\Admin\DailyReflectionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Health check para o Render: sem tocar no banco, para responder mesmo sob
// instabilidade pontual do MySQL. O grupo padrão "web" inclui StartSession
// (SESSION_DRIVER=database lê a tabela `sessions`) e EncryptCookies/
// AddQueuedCookiesToResponse - nenhum deles é necessário aqui, e juntos
// fariam esta rota depender do banco só para checar se a aplicação
// responde, então são removidos explicitamente desta rota.
Route::get('/health', HealthController::class)
    ->name('health')
    ->withoutMiddleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        // Também usa a sessão (para o cookie XSRF-TOKEN); irrelevante para
        // um GET que só devolve um JSON estático.
        \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
    ]);

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('reflections', DailyReflectionController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
