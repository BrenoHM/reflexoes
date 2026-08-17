<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reflexões
            </h2>
            <a href="{{ route('admin.reflections.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md text-sm text-white hover:bg-gray-700">
                Nova reflexão
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form method="GET" action="{{ route('admin.reflections.index') }}"
                  class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-wrap items-end gap-4">
                <div>
                    <x-input-label for="filtro_paragrafo" value="Parágrafo" />
                    <x-text-input id="filtro_paragrafo" name="paragrafo" type="number" min="1" class="mt-1" :value="$filtros['paragrafo'] ?? ''" />
                </div>
                <div class="flex items-center gap-3">
                    <x-primary-button type="submit">Filtrar</x-primary-button>
                    @if (($filtros['paragrafo'] ?? '') !== '')
                        <a href="{{ route('admin.reflections.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Limpar filtros
                        </a>
                    @endif
                </div>
            </form>

            <div class="bg-white shadow-sm border border-gray-100 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">ID</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Parágrafo</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Descrição do parágrafo</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Reflexão</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Criada em</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($reflections as $reflection)
                                <tr>
                                    <td class="px-4 py-3 text-gray-500">{{ $reflection->id }}</td>
                                    <td class="px-4 py-3 text-gray-900">{{ $reflection->paragrafo }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ Str::limit($reflection->descricao_paragrafo, 60) }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ Str::limit($reflection->reflexao, 60) }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $reflection->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.reflections.show', $reflection) }}" class="text-gray-600 hover:text-gray-900">Visualizar</a>
                                            <a href="{{ route('admin.reflections.edit', $reflection) }}" class="text-indigo-600 hover:text-indigo-800">Editar</a>
                                            <form method="POST" action="{{ route('admin.reflections.destroy', $reflection) }}"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir esta reflexão?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        Nenhuma reflexão encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $reflections->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
