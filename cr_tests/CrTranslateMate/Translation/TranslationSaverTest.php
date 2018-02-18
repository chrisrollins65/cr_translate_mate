<?php

namespace CrTranslateMate\Translation;

use CrTranslateMate\Language\LanguageFileManager;
use CrTranslateMate\Requests\SaveTranslationRequest;
use PHPUnit\Framework\TestCase;

/**
 * MOCK INTERNAL PHP FUNCTIONS
 * Yes, I know this is ugly. But solutions like https://github.com/php-mock/php-mock-phpunit weren't working
 * and I'm tired, so this'll work for now
 */
$mockPhpFunctions = [];
function is_writable($file)
{
    global $mockPhpFunctions;
    return !isset($mockPhpFunctions['is_writable']) ? \is_writable($file) : $mockPhpFunctions['is_writable'];
}

function file_put_contents($path, $contents)
{
    global $mockPhpFunctions;
    return !isset($mockPhpFunctions['file_put_contents'])
        ? \file_put_contents($path, $contents) : $mockPhpFunctions['file_put_contents'];
}

function mkdir($directory, $permissions, $recursive)
{
    global $mockPhpFunctions;
    return !isset($mockPhpFunctions['mkdir'])
        ? \mkdir($directory, $permissions, $recursive) : $mockPhpFunctions['mkdir'];
}

/* END PHP INTERNAL FUNCTION MOCKS */

class TranslationSaverTest extends TestCase
{
    private $testFileDir = 'test_save_dir';
    private $testFileName = 'testFile.php';
    private $filePath;
    private $testFileContents = '<?php' . "\n"
        . '$_[\'test_translation1\'] = \'test translation 1\';' . "\n"
        . '$_[\'test_translation2\'] = \'test translation 2\';';
    /** @var LanguageFileManager|\PHPUnit_Framework_MockObject_MockObject */
    private $languageFileManagerMock;
    /** @var TranslationsSaver */
    private $saver;
    /** @var SaveTranslationRequest */
    private $request;

    protected function setUp()
    {
        $this->filePath = __DIR__ . '/' . $this->filePath . '/' . $this->testFileDir . '/' . $this->testFileName;
        $this->languageFileManagerMock =
            $this->getMockBuilder(LanguageFileManager::class)->disableOriginalConstructor()->getMock();

        $this->saver = new TranslationsSaver($this->languageFileManagerMock);

        $this->request = new SaveTranslationRequest([
            'userInterface' => 'admin',
            'fileName' => 'testFile.php',
            'language' => 'en',
            'translation' => 'test saving translation',
            'key' => 'savedKey',
        ]);
        file_put_contents($this->filePath, $this->testFileContents);
    }

    protected function tearDown()
    {
        global $mockPhpFunctions;
        $mockPhpFunctions = [];
        unlink($this->filePath);
    }

    public function testThatTranslationIsSaved()
    {
        $this->mockGettingFilePath();
        $expectedContents = $this->testFileContents . "\n"
            . '$_[\'' . $this->request->getKey() . '\'] = \'' . $this->request->getTranslation() . '\';';
        $this->saver->save($this->request);
        $this->assertEquals($expectedContents, file_get_contents($this->filePath));
    }

    public function testThatFileAndDirectoryAreCreatedIfNeeded()
    {
        $filePath = str_replace($this->testFileName, 'newDir/' . $this->testFileName, $this->filePath);
        $this->mockGettingFilePath($filePath);
        $expectedContents = "<?php\n"
            . '$_[\'' . $this->request->getKey() . '\'] = \'' . $this->request->getTranslation() . '\';';
        $this->saver->save($this->request);
        $this->assertEquals($expectedContents, file_get_contents($filePath));
        unlink($filePath);
        rmdir(dirname($filePath));
    }

    /**
     * @expectedException \CrTranslateMate\Exceptions\UnableToWriteToFileException
     * @expectedExceptionMessage testFile.php
     */
    public function testThatExceptionThrownIfFileNotWritable()
    {
        $this->mockGettingFilePath();
        global $mockPhpFunctions;
        $mockPhpFunctions['is_writable'] = false;
        $this->saver->save($this->request);
    }

    /**
     * @expectedException \CrTranslateMate\Exceptions\UnableToWriteToFileException
     * @expectedExceptionMessage testFile.php
     */
    public function testThatExceptionThrownIfFileNotSaved()
    {
        $this->mockGettingFilePath();
        global $mockPhpFunctions;
        $mockPhpFunctions['file_put_contents'] = false;
        $this->saver->save($this->request);
    }

    /**
     * @expectedException \CrTranslateMate\Exceptions\UnableToWriteToFileException
     * @expectedExceptionMessage newDir
     */
    public function testThatExceptionThrownIfDirectoryCantBeCreated()
    {
        $filePath = str_replace($this->testFileName, 'newDir/' . $this->testFileName, $this->filePath);
        $this->mockGettingFilePath($filePath);
        global $mockPhpFunctions;
        $mockPhpFunctions['mkdir'] = false;
        $this->saver->save($this->request);
    }

    /**
     * @param string|null $filePath
     */
    private function mockGettingFilePath($filePath = null)
    {
        if (is_null($filePath)) {
            $filePath = $this->filePath;
        }
        $this->languageFileManagerMock
            ->expects($this->once())
            ->method('getFilePath')
            ->with($this->request->getInterface(), $this->request->getFileName(), $this->request->getLanguage())
            ->willReturn($filePath);
    }
}