<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar reflexão
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-8 shadow-sm border border-gray-100 rounded-lg">
                <form method="POST" action="{{ route('admin.reflections.update', $reflection) }}">
                    @csrf
                    @method('PUT')

                    @include('admin.reflections._form')

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <a href="{{ route('admin.reflections.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Cancelar
                        </a>
                        <x-primary-button>Salvar alterações</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
