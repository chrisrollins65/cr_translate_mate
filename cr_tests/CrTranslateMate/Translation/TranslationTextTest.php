<?php

namespace CrTranslateMate\Translation;

use PHPUnit\Framework\TestCase;

class TranslationTextTest extends TestCase
{
    private $textKey = 'testKey';
    private $languageCodes = array('en', 'es', 'fr');
    /** @var TranslationText */
    private $translationText;

    protected function setUp()
    {
        $this->translationText = new TranslationText($this->textKey, $this->languageCodes);
    }

    public function testIfFullyTranslated()
    {
        $this->assertFalse($this->translationText->isFullyTranslated());
        $this->translationText->addTranslation('en', 'English translation');
        $this->assertFalse($this->translationText->isFullyTranslated());
        $this->translationText->addTranslation('es', 'Spanish translation');
        $this->translationText->addTranslation('fr', 'French translation');
        $this->assertTrue($this->translationText->isFullyTranslated());
    }

    public function testIfStringFoundInTranslations()
    {
        $string = 'test';
        $this->assertFalse($this->translationText->stringExistsInTranslations($string));
        $this->translationText->addTranslation('en', 'search for string test');
        $this->assertTrue($this->translationText->stringExistsInTranslations($string));
    }

    /**
     * @test
     * @expectedException \CrTranslateMate\Exceptions\NoTranslationForTextException
     */
    public function testThatExceptionIsThrownWhenGettingATranslationThatDoesntExist()
    {
        $languageCode = 'en';
        $this->translationText->getTranslation($languageCode);
    }

    public function testGettingTranslation()
    {
        $languageCode = 'en';
        $expectedTranslation = 'test translation';
        $this->translationText->addTranslation($languageCode, $expectedTranslation);
        $actualTranslation = $this->translationText->getTranslation($languageCode);
        $this->assertEquals($expectedTranslation, $actualTranslation);
    }
}