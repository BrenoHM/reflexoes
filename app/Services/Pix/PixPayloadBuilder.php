<?php

namespace App\Services\Pix;

/**
 * Monta o payload "Pix Copia e Cola" no padrão BR Code (EMV QR Code), de
 * acordo com o Manual de Padrões para Iniciação do Pix do Banco Central.
 *
 * Implementado sem depender de pacotes de terceiros: o formato é uma
 * sequência simples de campos TLV (tag + tamanho + valor) mais um CRC16 de
 * verificação, então reimplementar aqui evita puxar uma dependência externa
 * só para montar uma string.
 *
 * Nenhum valor fixo é incluído (o campo EMV "54 - Transaction Amount" é
 * omitido de propósito), então o pagador escolhe livremente o valor no
 * aplicativo do banco.
 */
class PixPayloadBuilder
{
    private const GUI_PIX = 'BR.GOV.BCB.PIX';

    private const MERCHANT_CATEGORY_CODE = '0000';

    private const TRANSACTION_CURRENCY_BRL = '986';

    private const COUNTRY_CODE = 'BR';

    /** Identificador de transação "sem referência específica", convenção usual para Pix estático. */
    private const DEFAULT_TX_ID = '***';

    public function __construct(
        private readonly string $pixKey,
        private readonly string $merchantName,
        private readonly string $merchantCity,
        private readonly string $txId = self::DEFAULT_TX_ID,
    ) {}

    public function build(): string
    {
        $payload =
            $this->field('00', '01') // Payload Format Indicator
            .$this->field('01', '11') // Point of Initiation Method: 11 = QR estático/reutilizável
            .$this->field('26', $this->merchantAccountInformation())
            .$this->field('52', self::MERCHANT_CATEGORY_CODE)
            .$this->field('53', self::TRANSACTION_CURRENCY_BRL)
            .$this->field('58', self::COUNTRY_CODE)
            .$this->field('59', $this->sanitize($this->merchantName, 25))
            .$this->field('60', $this->sanitize($this->merchantCity, 15))
            .$this->field('62', $this->field('05', $this->txId));

        // O CRC16 é calculado sobre o payload já incluindo o "6304" (tag +
        // tamanho do próprio campo do CRC), mas sem o valor do CRC em si.
        $payloadWithCrcTag = $payload.'6304';

        return $payloadWithCrcTag.self::crc16($payloadWithCrcTag);
    }

    private function merchantAccountInformation(): string
    {
        return $this->field('00', self::GUI_PIX)
            .$this->field('01', $this->pixKey);
    }

    private function field(string $id, string $value): string
    {
        return $id.str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT).$value;
    }

    /**
     * Os campos de nome/cidade do BR Code aceitam apenas ASCII, sem acentos.
     */
    private function sanitize(string $value, int $maxLength): string
    {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = $transliterated !== false ? $transliterated : $value;
        $value = preg_replace('/[^A-Za-z0-9 ]/', '', $value) ?? $value;

        return mb_substr(trim($value), 0, $maxLength);
    }

    /**
     * CRC-16/CCITT-FALSE (poly 0x1021, init 0xFFFF, sem reflexão, xorout 0),
     * exatamente o algoritmo exigido pelo padrão EMV/BR Code do Pix.
     */
    public static function crc16(string $payload): string
    {
        $crc = 0xFFFF;

        foreach (str_split($payload) as $char) {
            $crc ^= (ord($char) << 8);

            for ($i = 0; $i < 8; $i++) {
                $crc = ($crc & 0x8000)
                    ? (($crc << 1) ^ 0x1021)
                    : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
