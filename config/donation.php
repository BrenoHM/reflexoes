<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dados do Pix para a seção de doação da Home
    |--------------------------------------------------------------------------
    |
    | A chave Pix não é um segredo de autenticação — ela é feita para ser
    | compartilhada publicamente com quem for pagar. Ainda assim, os valores
    | ficam centralizados aqui (e configuráveis via .env) para não haver mais
    | de uma fonte de verdade espalhada pelo código.
    |
    | "merchant_city" é obrigatório no payload padrão BR Code (EMV) do Pix,
    | mas não tem efeito no pagamento em si — é apenas informativo.
    |
    */

    'pix_key' => env('PIX_KEY', 'dc25aa47-1dbf-4562-ac1f-195223e94457'),

    'receiver_name' => env('PIX_RECEIVER_NAME', 'Breno Mol'),

    'bank' => env('PIX_BANK', 'Bradesco'),

    'merchant_city' => env('PIX_MERCHANT_CITY', 'SAO PAULO'),

];
