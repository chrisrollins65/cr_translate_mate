<?php

namespace CrTranslateMate\Language;

use Language;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class LanguageTextManagerTest extends TestCase
{
    /** @var Language|PHPUnit_Framework_MockObject_MockObject */
    private $languageMock;
    /** @var LanguageTextManager */
    private $textManager;
    private $text = 'sample text';
    private $textKey = 'textKey';

    protected function setUp()
    {
        $this->languageMock = $this->getMockBuilder(Language::class)->disableOriginalConstructor()->setMethods(array('get'))->getMock();
        $this->languageMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($this->text);
        $this->textManager = new LanguageTextManager($this->languageMock);
    }

    public function testGetters()
    {
        $text = $this->textManager->get($this->textKey);
        $this->assertEquals($this->text, $text);

        $text = $this->textManager->getHtmlSafeText($this->textKey);
        $this->assertEquals($this->text, $text);

        $text = $this->textManager->getHtmlSpecialCharsText($this->textKey);
        $this->assertEquals($this->text, $text);
    }
}