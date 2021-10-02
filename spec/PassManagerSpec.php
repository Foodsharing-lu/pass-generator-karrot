<?php

namespace spec\App;

use App\PassManager;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\FailureException;

class PassManagerSpec extends ObjectBehavior
{
    private const PASS_FOLDER = 'spec/passes/';
    private const PASS_FILE_NAME = 'a';

    public function getMatchers(): array
    {
        return [
            'fileExist' => function ($subject, $fileName) {
                $path = self::PASS_FOLDER . $fileName . PassManager::FILE_EXTENSION;
                if (!file_exists($path)) {
                    throw new FailureException(
                        sprintf('the file "%s" should exist', $path)
                    );
                }
                return true;
            },
            'fileNotExist' => function () {
                $path = self::PASS_FOLDER . self::PASS_FILE_NAME . PassManager::FILE_EXTENSION;
                if (file_exists($path)) {
                    throw new FailureException(
                        sprintf('the file "%s" should not exist', $path)
                    );
                }
                return true;
            }
        ];
    }

    public function let()
    {
        mkdir(self::PASS_FOLDER);
        if (!file_exists(self::PASS_FOLDER)) {
            throw new \Exception('The folder ' . self::PASS_FOLDER . ' does not exist.');
        }
        $image = imagecreatetruecolor(1, 1);
        try {
            imagepng($image, self::PASS_FOLDER . self::PASS_FILE_NAME . PassManager::FILE_EXTENSION);
        } catch (\Exception $e) {
            throw new \Exception('You have no permission to create files in this directory: ' . $e);
        }
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PassManager::class);
    }

    public function it_creates_complete_passport_image_path()
    {
        $this->getCompletePassImagePath(self::PASS_FOLDER, self::PASS_FILE_NAME)
                ->shouldReturn(self::PASS_FOLDER . self::PASS_FILE_NAME . '.png');
    }

    public function it_throws_exception_if_folder_path_is_null()
    {
        $this->shouldThrow('TypeError')->duringGetCompletePassImagePath(null, 'a');
    }

    public function it_getCompletePassportImagePath_trailingSlash()
    {
        $this->shouldThrow('InvalidArgumentException')
                ->duringGetCompletePassImagePath('a', self::PASS_FILE_NAME);
    }

    public function it_returns_the_file_name()
    {
        $this->getFileName('a')->shouldReturn('a.png');
    }

    public function it_checks_pass_existence()
    {
        $this->hasPass(self::PASS_FOLDER, self::PASS_FILE_NAME)->shouldReturn(true);
    }

    public function it_deletes_pass()
    {
        $this->deletePass(self::PASS_FOLDER, self::PASS_FILE_NAME)->shouldFileNotExist();
    }

    public function it_creates_pass()
    {
        $photoPath = self::PASS_FOLDER . self::PASS_FILE_NAME . PassManager::FILE_EXTENSION;
        $image = imagecreatetruecolor(1, 1);
        ob_start();
        imagepng($image);
        $imageBlob = ob_get_clean();
        $this->createPass('b', self::PASS_FOLDER, 'c', $photoPath, $imageBlob, 'd')->shouldFileExist('b');
    }

    public function letGo()
    {
        array_map('unlink', glob(self::PASS_FOLDER . '*'));
        rmdir(self::PASS_FOLDER);
    }
}
