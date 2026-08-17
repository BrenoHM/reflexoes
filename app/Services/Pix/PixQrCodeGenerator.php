<?php

namespace App\Services\Pix;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;

/**
 * Gera a imagem (SVG) do QR Code a partir de um payload Pix já pronto.
 * SVG foi escolhido por não depender da extensão GD/Imagick e por manter
 * boa qualidade visual em qualquer tamanho de tela.
 */
class PixQrCodeGenerator
{
    public static function svgFor(string $payload, int $size = 260): string
    {
        $result = (new Builder(
            writer: new SvgWriter(),
            writerOptions: [
                SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true,
            ],
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 8,
        ))->build();

        return $result->getString();
    }
}
