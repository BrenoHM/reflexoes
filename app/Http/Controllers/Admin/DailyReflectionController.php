<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDailyReflectionRequest;
use App\Http\Requests\Admin\UpdateDailyReflectionRequest;
use App\Models\DailyReflection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyReflectionController extends Controller
{
    public function index(Request $request): View
    {
        $reflections = DailyReflection::query()
            ->when(
                $request->filled('paragrafo'),
                fn ($query) => $query->where('paragrafo', $request->input('paragrafo'))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reflections.index', [
            'reflections' => $reflections,
            'filtros' => $request->only(['paragrafo']),
        ]);
    }

    public function create(): View
    {
        return view('admin.reflections.create');
    }

    public function store(StoreDailyReflectionRequest $request): RedirectResponse
    {
        DailyReflection::create($request->validated());

        return redirect()
            ->route('admin.reflections.index')
            ->with('success', 'Reflexão cadastrada com sucesso.');
    }

    public function show(DailyReflection $reflection): View
    {
        return view('admin.reflections.show', [
            'reflection' => $reflection,
        ]);
    }

    public function edit(DailyReflection $reflection): View
    {
        return view('admin.reflections.edit', [
            'reflection' => $reflection,
        ]);
    }

    public function update(UpdateDailyReflectionRequest $request, DailyReflection $reflection): RedirectResponse
    {
        $reflection->update($request->validated());

        return redirect()
            ->route('admin.reflections.index')
            ->with('success', 'Reflexão atualizada com sucesso.');
    }

    public function destroy(DailyReflection $reflection): RedirectResponse
    {
        $reflection->delete();

        return redirect()
            ->route('admin.reflections.index')
            ->with('success', 'Reflexão excluída com sucesso.');
    }
}
