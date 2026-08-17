<?php

namespace Database\Seeders;

use App\Models\DailyReflection;
use Illuminate\Database\Seeder;

class DailyReflectionSeeder extends Seeder
{
    /**
     * Popula algumas reflexões de exemplo para permitir testar a aplicação
     * (página pública com sorteio aleatório, listagem administrativa, filtros,
     * dashboard).
     *
     * IMPORTANTE: os textos abaixo são FICTÍCIOS, criados apenas para fins de
     * demonstração/teste. Eles NÃO são citações reais do Diário de Santa
     * Faustina. O conteúdo real deve ser cadastrado pelo administrador através
     * do painel administrativo.
     */
    public function run(): void
    {
        $aviso = '[CONTEÚDO FICTÍCIO DE EXEMPLO — substitua pelo texto real do Diário de Santa Faustina] ';

        $exemplos = [
            [
                'paragrafo' => 300,
                'descricao_paragrafo' => $aviso.'Texto de exemplo para o parágrafo do Diário, usado apenas para validar o layout e o funcionamento da página pública.',
                'reflexao' => $aviso.'Texto de exemplo de reflexão associada ao parágrafo 300.',
            ],
            [
                'paragrafo' => 87,
                'descricao_paragrafo' => $aviso.'Outro texto de exemplo para o parágrafo do Diário, usado para validar o sorteio aleatório entre várias reflexões.',
                'reflexao' => $aviso.'Texto de exemplo de reflexão associada ao parágrafo 87.',
            ],
            [
                'paragrafo' => 1074,
                'descricao_paragrafo' => $aviso.'Mais um texto de exemplo para o parágrafo do Diário, usado para validar o filtro por parágrafo no painel administrativo.',
                'reflexao' => $aviso.'Texto de exemplo de reflexão associada ao parágrafo 1074.',
            ],
            [
                'paragrafo' => 1605,
                'descricao_paragrafo' => $aviso.'Texto de exemplo para validar a listagem e a paginação no painel administrativo.',
                'reflexao' => $aviso.'Texto de exemplo de reflexão associada ao parágrafo 1605.',
            ],
            [
                'paragrafo' => 742,
                'descricao_paragrafo' => $aviso.'Texto de exemplo para validar a exibição de várias reflexões disponíveis para sorteio.',
                'reflexao' => $aviso.'Texto de exemplo de reflexão associada ao parágrafo 742.',
            ],
        ];

        foreach ($exemplos as $exemplo) {
            DailyReflection::updateOrCreate(
                ['paragrafo' => $exemplo['paragrafo']],
                ['descricao_paragrafo' => $exemplo['descricao_paragrafo'], 'reflexao' => $exemplo['reflexao']]
            );
        }
    }
}
