<?php

namespace App;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeNone;

class QRCodeImageGenerator
{
    private const MARGIN_IN_PIXELS = 10;

    public static function create(string $url, int $size): string
    {
        LoggerWrapper::info('Generating QR code image', ['url' => $url]);

        $sizeWithoutMargin = $size - 2 * self::MARGIN_IN_PIXELS;
        $result = Builder::create()
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelQuartile())
            ->size($sizeWithoutMargin)
            ->margin(self::MARGIN_IN_PIXELS)
            ->roundBlockSizeMode(new RoundBlockSizeModeNone())
            ->build();
        return $result->getString();
    }
}
