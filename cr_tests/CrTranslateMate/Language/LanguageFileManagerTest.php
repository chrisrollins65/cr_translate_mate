<?php

namespace CrTranslateMate\Language;

require_once __DIR__ . '/../FakeBuilders/FakeLanguageBuilder.php';

use CrTranslateMate\Constants;
use CrTranslateMate\FakeBuilders\FakeLanguageBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class LanguageFileManagerTest extends TestCase
{
    /** @var Constants|PHPUnit_Framework_MockObject_MockObject */
    private $constantsMock;
    /** @var LanguageFileManager */
    private $fileManager;
    private $languageDirectory;
    private $mainLanguageFileKey = '_main_lang_file';

    protected function setUp()
    {
        $this->languageDirectory = FakeLanguageBuilder::getLanguageDirectory();

        $this->constantsMock = $this->getMockBuilder(Constants::class)->disableOriginalConstructor()->getMock();
        $this->constantsMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($this->languageDirectory);

        $this->fileManager = new LanguageFileManager($this->constantsMock);
    }

    public function testThatFileListIsReturned()
    {
        $fileList = $this->fileManager->getFileList('admin');

        $this->assertArrayHasKey($this->mainLanguageFileKey, $fileList);
        $this->assertArrayHasKey('fake_lang_file', $fileList);
        $this->assertArrayHasKey('section/fake_lang_file', $fileList);
        foreach ($fileList as $files) {
            foreach ($files as $file) {
                $this->assertInstanceOf(LanguageFile::class, $file);
            }
        }
    }

    public function testFilePathGetter()
    {
        $directory = 'admin';
        $file = 'filename';
        $language = 'en-gb';
        $filePath = $this->fileManager->getFilePath($directory, $file, $language);
        $expected = $this->languageDirectory . $language . '/' . $file . '.php';

        $this->assertEquals($expected, $filePath);
    }

    public function testFilePathGetterForMainLanguageFile()
    {
        $directory = 'admin';
        $file = $this->mainLanguageFileKey;
        $language = 'en-gb';
        $filePath = $this->fileManager->getFilePath($directory, $file, $language);
        $expected = $this->languageDirectory . $language . '/' . $language . '.php';

        $this->assertEquals($expected, $filePath);
    }

    public function testMainLanguageFileKeyGetter()
    {
        $this->assertEquals($this->mainLanguageFileKey, $this->fileManager->getMainLanguageFileKey());
    }
}