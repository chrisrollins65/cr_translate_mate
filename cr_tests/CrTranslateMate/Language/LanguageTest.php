<?php

namespace CrTranslateMate\Language;

require_once __DIR__ . '/../FakeBuilders/FakeLanguageBuilder.php';

use CrTranslateMate\Constants;
use CrTranslateMate\FakeBuilders\FakeLanguageBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class LanguageTest extends TestCase
{
    private $languageData;
    /** @var Constants|PHPUnit_Framework_MockObject_MockObject */
    private $constants;

    protected function setUp()
    {
        $this->constants = $this->getMockBuilder(Constants::class)->disableOriginalConstructor()->getMock();
        $this->languageData = FakeLanguageBuilder::$languageData[0];
    }

    public function testThatLanguageDataIsSet()
    {
        $this->expectConstantsToGetLanguageDir();

        $language = new Language($this->languageData, $this->constants);
        $this->assertEquals($language->getLanguageId(), $this->languageData['language_id']);
        $this->assertEquals($language->getName(), $this->languageData['name']);
        $this->assertEquals($language->getCode(), $this->languageData['code']);
        $this->assertEquals($language->getLocale(), $this->languageData['locale']);
        $this->assertEquals($language->getImage(), $this->languageData['image']);
        $this->assertEquals($language->getSortOrder(), $this->languageData['sort_order']);
        $this->assertEquals($language->getStatus(), $this->languageData['status']);
    }

    public function testThatLanguageDirectoryIsChecked()
    {
        $this->expectConstantsToGetLanguageDir();
        $expectedDirectory = $this->languageData['code'];

        $language = new Language($this->languageData, $this->constants);
        $this->assertEquals($language->getDirectory(), $expectedDirectory);
    }

    /**
     * @test
     * @expectedException \CrTranslateMate\Exceptions\DirectoryNotFoundException
     */
    public function testThatExceptionThrownIfLanguageDirectoryNotFound()
    {
        $this->expectConstantsToGetLanguageDir('/directory_that_does_not_exist/');
        new Language($this->languageData, $this->constants);
    }

    /**
     * @param string $directory
     */
    private function expectConstantsToGetLanguageDir($directory = '')
    {
        $this->constants
            ->expects($this->once())
            ->method('get')
            ->with('DIR_LANGUAGE')
            ->willReturn(FakeLanguageBuilder::getLanguageDirectory() . $directory);
    }
}