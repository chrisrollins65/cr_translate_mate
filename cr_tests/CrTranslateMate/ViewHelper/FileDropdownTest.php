<?php

namespace CrTranslateMate\ViewHelper;

require_once __DIR__ . '/../FakeBuilders/FakeLanguageBuilder.php';

use CrTranslateMate\Constants;
use CrTranslateMate\FakeBuilders\FakeLanguageBuilder;
use CrTranslateMate\Language\LanguageFileManager;
use PHPUnit\Framework\TestCase;

class FileDropdownTest extends TestCase
{
    /** @var Constants | \PHPUnit_Framework_MockObject_MockObject */
    private $constantsMock;
    /** @var LanguageFileManager */
    private $fileManager;
    private $mainLanguageFileName = 'Main language file';
    /** @var FileDropdown */
    private $fileDropdown;

    protected function setUp()
    {
        $this->constantsMock = $this->getMockBuilder(Constants::class)->disableOriginalConstructor()->getMock();
        $this->constantsMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('DIR_LANGUAGE', FakeLanguageBuilder::getLanguageDirectory()),
                array('DIR_CATALOG', FakeLanguageBuilder::getLanguageDirectory('DIR_CATALOG')),
            )));

        $this->fileManager = new LanguageFileManager($this->constantsMock);

        $this->fileDropdown = new FileDropdown($this->fileManager, $this->mainLanguageFileName);
    }

    public function testThatDropdownIsBuilt()
    {
        $dropdown = $this->fileDropdown->buildDropdown();
        $expected = '<option value="_main_lang_file">' . $this->mainLanguageFileName . '</option>'
            . '<option value="fake_lang_file">fake_lang_file</option>'
            . '<optgroup label="section"><option value="section/fake_lang_file">fake_lang_file</option></optgroup>';
        $this->assertEquals($expected, $dropdown);
    }
}