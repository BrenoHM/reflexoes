<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Parágrafo {{ $reflection->paragrafo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 sm:p-8 shadow-sm border border-gray-100 rounded-lg space-y-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Parágrafo</dt>
                        <dd class="text-gray-900">{{ $reflection->paragrafo }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Cadastrada em</dt>
                        <dd class="text-gray-900">{{ $reflection->created_at->translatedFormat('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Última atualização</dt>
                        <dd class="text-gray-900">{{ $reflection->updated_at->translatedFormat('d/m/Y H:i') }}</dd>
                    </div>
                </dl>

                <div>
                    <dt class="text-sm text-gray-500 mb-1">Descrição do parágrafo</dt>
                    <dd class="text-gray-900 whitespace-pre-line leading-relaxed">{{ $reflection->descricao_paragrafo }}</dd>
                </div>

                <div>
                    <dt class="text-sm text-gray-500 mb-1">Reflexão</dt>
                    <dd class="text-gray-900 whitespace-pre-line leading-relaxed">{{ $reflection->reflexao }}</dd>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.reflections.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    &larr; Voltar para a listagem
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reflections.edit', $reflection) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        Editar
                    </a>

                    <form method="POST" action="{{ route('admin.reflections.destroy', $reflection) }}"
                          onsubmit="return confirm('Tem certeza que deseja excluir esta reflexão?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
