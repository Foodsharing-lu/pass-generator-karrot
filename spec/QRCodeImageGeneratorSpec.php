<?php

namespace spec\App;

use App\QRCodeImageGenerator;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\FailureException;

class QRCodeImageGeneratorSpec extends ObjectBehavior
{
    public function getMatchers(): array
    {
        return [
            'imageWidthBe' => function ($subject, $expectedWidth) {
                $qrCode = new \Imagick();
                $qrCode->readImageBlob($subject);
                $geo = $qrCode->getImageGeometry();
                $imageWidth = $geo['width'];
                if ($imageWidth !== $expectedWidth) {
                    throw new FailureException(
                        sprintf(
                            'the image width "%d" does not match "%d"',
                            $imageWidth,
                            $expectedWidth
                        )
                    );
                }
                return true;
            }
        ];
    }

    public function letGo()
    {
        // Delete all files in the temporary folder after each test.
        array_map('unlink', array_filter((array) glob('spec/temp/*')));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(QRCodeImageGenerator::class);
    }

    public function it_create()
    {
        $result = $this->create('https://example.net', 150);
        $result->shouldBeString();
        $result->shouldImageWidthBe(150);
    }
}
