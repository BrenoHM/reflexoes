<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyReflectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paragrafo' => ['required', 'integer', 'min:1'],
            'descricao_paragrafo' => ['required', 'string'],
            'reflexao' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'paragrafo.required' => 'O parágrafo é obrigatório.',
            'paragrafo.integer' => 'O parágrafo deve ser um número inteiro.',
            'paragrafo.min' => 'O parágrafo deve ser maior ou igual a 1.',
            'descricao_paragrafo.required' => 'A descrição do parágrafo é obrigatória.',
            'descricao_paragrafo.string' => 'A descrição do parágrafo deve ser um texto.',
            'reflexao.required' => 'A reflexão é obrigatória.',
            'reflexao.string' => 'A reflexão deve ser um texto.',
        ];
    }
}
