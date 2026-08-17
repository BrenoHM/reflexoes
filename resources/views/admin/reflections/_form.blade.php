@php
    $reflection ??= null;
@endphp

<div>
    <x-input-label for="paragrafo" value="Número do parágrafo" />
    <x-text-input
        id="paragrafo"
        name="paragrafo"
        type="number"
        min="1"
        class="block mt-1 w-full"
        :value="old('paragrafo', $reflection?->paragrafo)"
        required
    />
    <x-input-error class="mt-2" :messages="$errors->get('paragrafo')" />
</div>

<div class="mt-4">
    <x-input-label for="descricao_paragrafo" value="Descrição do parágrafo" />
    <textarea
        id="descricao_paragrafo"
        name="descricao_paragrafo"
        rows="6"
        required
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    >{{ old('descricao_paragrafo', $reflection?->descricao_paragrafo) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('descricao_paragrafo')" />
</div>

<div class="mt-4">
    <x-input-label for="reflexao" value="Reflexão" />
    <textarea
        id="reflexao"
        name="reflexao"
        rows="6"
        required
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    >{{ old('reflexao', $reflection?->reflexao) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('reflexao')" />
</div>
