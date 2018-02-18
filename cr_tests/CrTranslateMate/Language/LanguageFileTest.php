<?php

namespace CrTranslateMate\Language;

use PHPUnit\Framework\TestCase;

class LanguageFileTest extends TestCase
{
    private $path = 'path/to/lang_dir/language/section/texts.php';
    private $languageDirectory = 'path/to/lang_dir';

    public function testThatBaseNameAndLanguageAreSet()
    {
        $languageFile = new LanguageFile($this->path, $this->languageDirectory);
        $expectedBaseName = 'section/texts';
        $expectedLanguage = 'language';

        $this->assertEquals($expectedBaseName, $languageFile->getBaseName());
        $this->assertEquals($expectedLanguage, $languageFile->getLanguage());
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Expected translation file to have both a language and file name
     */
    public function testThatExceptionIsThrownIfNoLanguageInPath()
    {
        $path = 'path/to/lang_dir/texts.php';
        new LanguageFile($path, $this->languageDirectory);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Expected translation file to have both a language and file name
     */
    public function testThatExceptionIsThrownIfNoFileNameInPath()
    {
        $path = 'path/to/lang_dir/language';
        new LanguageFile($path, $this->languageDirectory);
    }

    public function testIfFileIsMainLanguageFile()
    {
        $path = 'path/to/lang_dir/language/language.php';
        $languageFile = new LanguageFile($path, $this->languageDirectory);
        $this->assertTrue($languageFile->isMainLanuageFile());
    }
}