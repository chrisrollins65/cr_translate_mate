<?php

namespace CrTranslateMate\Language;

require_once __DIR__ . '/../FakeBuilders/FakeLanguageBuilder.php';

use CrTranslateMate\ArrayIterator;
use CrTranslateMate\Constants;
use CrTranslateMate\FakeBuilders\FakeLanguageBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class LanguageCollectionTest extends TestCase
{
    /** @var Constants|PHPUnit_Framework_MockObject_MockObject */
    private $constants;
    /** @var Language */
    private $language1;
    /** @var Language */
    private $language2;

    protected function setUp()
    {
        $this->constants = $this->getMockBuilder(Constants::class)->disableOriginalConstructor()->getMock();
        $this->constants
            ->expects($this->any())
            ->method('get')
            ->with('DIR_LANGUAGE')
            ->willReturn(FakeLanguageBuilder::getLanguageDirectory());

        $this->language1 = FakeLanguageBuilder::buildLanguage($this->constants);
        $this->language2 = FakeLanguageBuilder::buildAnotherLanguage($this->constants);
    }

    public function testLanguageInfoGetters()
    {
        $languageCollection = new LanguageCollection(FakeLanguageBuilder::$languageData, $this->constants);

        $expected = array($this->language1->getCode(), $this->language2->getCode());
        $this->assertEquals($expected, $languageCollection->getCodes());

        $expected = array($this->language1->getName(), $this->language2->getName());
        $this->assertEquals($expected, $languageCollection->getNames());

        $expected = array($this->language1->getDirectory(), $this->language2->getDirectory());
        $this->assertEquals($expected, $languageCollection->getDirectories());
    }

    public function testIteratorGetter()
    {
        $languageCollection = new LanguageCollection(FakeLanguageBuilder::$languageData, $this->constants);
        $iterator = $languageCollection->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertEquals($this->language1, $iterator->current());
        $this->assertEquals($this->language2, $iterator->next());
    }
}