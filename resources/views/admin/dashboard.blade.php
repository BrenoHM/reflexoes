<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Painel Administrativo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 max-w-xs">
                <p class="text-sm text-gray-500">Reflexões cadastradas</p>
                <p class="text-3xl font-semibold text-gray-900 mt-1">{{ $totalReflections }}</p>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-medium text-gray-900">Últimas reflexões cadastradas</h3>
                    <a href="{{ route('admin.reflections.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        + Nova reflexão
                    </a>
                </div>

                @if ($recentReflections->isEmpty())
                    <p class="px-6 py-6 text-sm text-gray-500">Nenhuma reflexão cadastrada ainda.</p>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($recentReflections as $reflection)
                            <li class="px-6 py-3 flex items-center justify-between text-sm">
                                <span class="text-gray-700">Parágrafo {{ $reflection->paragrafo }}</span>
                                <a href="{{ route('admin.reflections.show', $reflection) }}" class="text-indigo-600 hover:text-indigo-800">
                                    Visualizar
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
