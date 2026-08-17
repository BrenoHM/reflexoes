<?php

namespace Tests\Unit\Services\Pix;

use App\Services\Pix\PixPayloadBuilder;
use PHPUnit\Framework\TestCase;

class PixPayloadBuilderTest extends TestCase
{
    /**
     * Vetor de teste padrão do algoritmo CRC-16/CCITT-FALSE (o mesmo exigido
     * pelo BR Code do Pix): CRC("123456789") deve ser 0x29B1.
     */
    public function test_crc16_matches_the_standard_test_vector(): void
    {
        $this->assertSame('29B1', PixPayloadBuilder::crc16('123456789'));
    }

    public function test_payload_contains_the_configured_pix_key(): void
    {
        $payload = $this->buildPayload();

        $this->assertStringContainsString('dc25aa47-1dbf-4562-ac1f-195223e94457', $payload);
    }

    public function test_payload_uses_the_official_pix_gui(): void
    {
        $payload = $this->buildPayload();

        $this->assertStringContainsString('BR.GOV.BCB.PIX', $payload);
    }

    public function test_payload_starts_with_the_correct_emv_header(): void
    {
        $payload = $this->buildPayload();

        // 00 (payload format) + 01 (ponto de iniciação estático) + 26 (merchant account info / Pix)
        $this->assertStringStartsWith('000201010211', $payload);
    }

    public function test_payload_does_not_contain_a_fixed_amount(): void
    {
        $payload = $this->buildPayload();

        // Tag "54" é o valor da transação; não deve existir, pois o pagador
        // escolhe livremente o valor no aplicativo do banco.
        $this->assertDoesNotMatchRegularExpression('/(?<!\d)54\d{2}/', $payload);
    }

    public function test_payload_contains_merchant_name_and_city(): void
    {
        $payload = $this->buildPayload();

        $this->assertStringContainsString('Breno Mol', $payload);
        $this->assertStringContainsString('SAO PAULO', $payload);
    }

    public function test_payload_ends_with_a_valid_crc16_checksum(): void
    {
        $payload = $this->buildPayload();

        $crcInPayload = substr($payload, -4);
        $payloadWithoutCrcValue = substr($payload, 0, -4);

        $this->assertSame($crcInPayload, PixPayloadBuilder::crc16($payloadWithoutCrcValue));
    }

    public function test_accented_merchant_data_is_sanitized_to_ascii(): void
    {
        $payload = (new PixPayloadBuilder(
            pixKey: 'dc25aa47-1dbf-4562-ac1f-195223e94457',
            merchantName: 'José da Conceição',
            merchantCity: 'São Paulo',
        ))->build();

        $this->assertStringContainsString('Jose da Conceicao', $payload);
        $this->assertStringContainsString('Sao Paulo', $payload);
        $this->assertStringNotContainsString('é', $payload);
        $this->assertStringNotContainsString('ã', $payload);
    }

    private function buildPayload(): string
    {
        return (new PixPayloadBuilder(
            pixKey: 'dc25aa47-1dbf-4562-ac1f-195223e94457',
            merchantName: 'Breno Mol',
            merchantCity: 'SAO PAULO',
        ))->build();
    }
}
