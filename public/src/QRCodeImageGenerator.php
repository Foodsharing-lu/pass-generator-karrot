<?php

namespace App;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class QRCodeImageGenerator
{
    private const MARGIN_IN_PIXELS = 10;

    public static function create(string $url, int $size): string
    {
        LoggerWrapper::info('Generating QR code image', ['url' => $url]);

        $sizeWithoutMargin = $size - 2 * self::MARGIN_IN_PIXELS;
        $builder = new Builder(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Quartile,
            size: $sizeWithoutMargin,
            margin: self::MARGIN_IN_PIXELS,
            roundBlockSizeMode: RoundBlockSizeMode::None,
        );
        $result = $builder->build();
        return $result->getString();
    }
}
