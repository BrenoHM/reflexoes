<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyReflection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Painel simples: total de reflexões cadastradas e as últimas cadastradas.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalReflections' => DailyReflection::count(),
            'recentReflections' => DailyReflection::query()->latest()->take(5)->get(),
        ]);
    }
}
