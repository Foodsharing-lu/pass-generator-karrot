<?php

namespace App;

class PassManager
{
    public const FILE_EXTENSION = '.png';

    private const PASSPORT_BACKGROUND_FILE_PATH = __DIR__ . '/../assets/images/pass-background.png';

    public static function getCompletePassImagePath(string $path, string $id): string
    {
        if (!str_ends_with($path, '/')) {
            throw new \InvalidArgumentException('The path should contain a trailing slash.');
        }
        return $path . self::getFileName($id);
    }

    public static function getFileName(string $id): string
    {
        return $id . self::FILE_EXTENSION;
    }

    public static function hasPass(string $path, string $id): bool
    {
        $completePath = self::getCompletePassImagePath($path, $id);
        return file_exists($completePath);
    }

    public static function deletePass(string $path, string $id): void
    {
        LoggerWrapper::info('Deleting pass', ['userId' => $id]);

        $completePath = self::getCompletePassImagePath($path, $id);
        unlink($completePath);
    }

    public static function createPass(
        string $id,
        string $imageFolderPath,
        string $name,
        string $photoUrl,
        string $qrCodeImageBlob,
        string $url
    ): void {
        LoggerWrapper::info('Creating pass', ['userId' => $id]);

        // Prepare images.
        $background = new \Imagick(self::PASSPORT_BACKGROUND_FILE_PATH);
        $qrCode = new \Imagick();
        $qrCode->readImageBlob($qrCodeImageBlob);
        $handle = fopen($photoUrl, 'rb');
        $photo = new \Imagick();
        $photo->readImageFile($handle);
        $photo->adaptiveResizeImage(200, 200, true);

        // Define canvas.
        $canvas = new \Imagick();
        $width = $background->getImageWidth();
        $height = $background->getImageHeight();
        $canvas->newImage($width, $height, 'white');
        $canvas->setImageFormat('png');

        // Add all images.
        $canvas->compositeImage($background, \Imagick::COMPOSITE_COPY, 0, 0);
        $canvas->compositeImage($qrCode, \Imagick::COMPOSITE_COPY, 70, 290);
        $canvas->compositeImage($photo, \Imagick::COMPOSITE_COPY, 540, 230);

        // Add text.
        $draw = new \ImagickDraw();
        $draw->setFillColor('black');
        $draw->setFont(__DIR__ . '/../assets/fonts/DejaVuSans.ttf');
        $draw->setFontSize(36);
        $canvas->annotateImage($draw, 210, 210, 0, $name);
        $canvas->annotateImage($draw, 210, 260, 0, 'Foodsaver');
        $draw->setFontSize(18);
        $canvas->annotateImage($draw, 80, 450, 0, $url);
        $canvas->annotateImage($draw, 540, 450, 0, 'ID: ' . $id);

        // Save image.
        $completeImagePath = self::getCompletePassImagePath($imageFolderPath, $id);
        $canvas->writeImage($completeImagePath);
    }
}
