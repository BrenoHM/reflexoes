<?php

namespace App\Http\Controllers;

use App\Models\DailyReflection;
use App\Services\Pix\PixPayloadBuilder;
use App\Services\Pix\PixQrCodeGenerator;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Exibe uma reflexão sorteada aleatoriamente entre as reflexões ativas,
     * junto com a seção de doação via Pix.
     *
     * inRandomOrder() é suficiente para o volume de dados deste projeto.
     * Ordenar por RAND() no MySQL varre e ordena a tabela inteira, então só
     * deixaria de ser adequado se o número de reflexões crescesse muito
     * (dezenas de milhares+) — não é o caso aqui.
     */
    public function __invoke(): View
    {
        $reflection = DailyReflection::query()->inRandomOrder()->first();

        $pixPayload = (new PixPayloadBuilder(
            pixKey: config('donation.pix_key'),
            merchantName: config('donation.receiver_name'),
            merchantCity: config('donation.merchant_city'),
        ))->build();

        return view('home.index', [
            'reflection' => $reflection,
            'speechData' => $reflection ? [
                'paragrafo' => $reflection->paragrafo,
                // strip_tags: o texto enviado ao sintetizador de voz nunca
                // deve conter marcação HTML, mesmo que o campo já seja
                // exibido com escaping automático do Blade.
                'descricaoParagrafo' => strip_tags($reflection->descricao_paragrafo),
                'reflexao' => strip_tags($reflection->reflexao),
            ] : null,
            'pixPayload' => $pixPayload,
            'pixQrCodeSvg' => PixQrCodeGenerator::svgFor($pixPayload),
            'pixReceiverName' => config('donation.receiver_name'),
            'pixBank' => config('donation.bank'),
        ]);
    }
}
