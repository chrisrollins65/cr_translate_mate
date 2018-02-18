<?php

namespace CrTranslateMate\Translation;

require_once __DIR__ . '/../FakeBuilders/FakeLanguageBuilder.php';

use CrTranslateMate\Constants;
use CrTranslateMate\Dto\TextSearchOptionsDto;
use CrTranslateMate\FakeBuilders\FakeLanguageBuilder;
use CrTranslateMate\Language\LanguageCollection;
use CrTranslateMate\Language\LanguageFileManager;
use PHPUnit\Framework\TestCase;

class TranslationsLoaderTest extends TestCase
{
    /** @var LanguageFileManager */
    private $fileManager;
    /** @var LanguageCollection */
    private $languageCollection;
    /** @var Constants|\PHPUnit_Framework_MockObject_MockObject */
    private $constantsMock;
    /** @var TranslationsLoader */
    private $translationsLoader;
    private $expectedTranslations = [
        '_main_lang_file' => [
            'test_translation1' => [
                'en-gb' => 'test translation 1',
            ],
            'test_translation2' => [
                'en-gb' => 'test translation 2',
                'en-gb2' => 'test translation 2',
            ],
        ],
        'fake_lang_file' => [
            'test_translation1' => [
                'en-gb' => 'test translation 1',
            ],
        ],
        'section/fake_lang_file' => [
            'test_translation1' => [
                'en-gb' => 'test translation 1',
            ],
            'test_translation2' => [
                'en-gb' => 'test translation 2',
            ],
        ],
    ];

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
        $this->languageCollection = new LanguageCollection(FakeLanguageBuilder::$languageData, $this->constantsMock);

        $this->translationsLoader =
            new TranslationsLoader($this->fileManager, $this->languageCollection);
    }

    public function testLoadingTranslationsWithDefaultOptions()
    {
        $texts = $this->translationsLoader->load((new TextSearchOptionsDto()));
        $this->assertTranslations($this->expectedTranslations, $texts);
    }

    public function testLoadingTranslationsAfterCertainFile()
    {
        $expectedTranslations = $this->expectedTranslations;
        unset($expectedTranslations['_main_lang_file']);
        $options = new TextSearchOptionsDto();
        $options->setFileToStartAfter('_main_lang_file');
        $texts = $this->translationsLoader->load($options);
        $this->assertTranslations($expectedTranslations, $texts);
    }

    public function testLoadingTranslationsFromSingleFile()
    {
        $expectedTranslations = ['fake_lang_file' => $this->expectedTranslations['fake_lang_file']];
        $options = new TextSearchOptionsDto();
        $options->setSingleFileToLoad('fake_lang_file');
        $texts = $this->translationsLoader->load($options);
        $this->assertTranslations($expectedTranslations, $texts);
    }

    public function testLoadingOnlyNonTranslated()
    {
        $options = new TextSearchOptionsDto();
        $options->setOnlyNonTranslated(true);
        $texts = $this->translationsLoader->load($options);
        $this->assertTrue(empty($texts['_main_lang_file']['test_translation2']));
    }

    public function testLoadingTranslationsByTextSearch()
    {
        $search = 'translation 2';
        $expectedTranslations = [
            '_main_lang_file' => [
                'test_translation2' => [
                    'en-gb' => 'test translation 2',
                    'en-gb2' => 'test translation 2',
                ],
            ],
            'section/fake_lang_file' => [
                'test_translation2' => [
                    'en-gb' => 'test translation 2',
                ],
            ],
        ];
        $options = new TextSearchOptionsDto();
        $options->setTextToSearchFor($search);
        $texts = $this->translationsLoader->load($options);
        $this->assertTranslations($expectedTranslations, $texts);
    }

    public function testLoadingTranslationsByKeySearch()
    {
        $search = 'translation1';
        $options = new TextSearchOptionsDto();
        $options->setKeyToSearchFor($search);
        $texts = $this->translationsLoader->load($options);
        foreach ($texts as $file => $translationTexts) {
            foreach ($translationTexts as $key => $translationText) {
                $this->assertContains($search, $key);
            }
        }
    }

    public function testGettingTheLastLoadedFile()
    {
        $fileToLoad = 'fake_lang_file';
        $options = new TextSearchOptionsDto();
        $options->setSingleFileToLoad($fileToLoad);
        $this->translationsLoader->load($options);
        $this->assertEquals($fileToLoad, $this->translationsLoader->getLastLoadedFile());
    }

    public function testGettingLimitedNumberOfTranslationTexts()
    {
        $maxNumberOfTexts = 3;
        $options = new TextSearchOptionsDto();
        $options->setNumberOfTexts($maxNumberOfTexts);
        $texts = $this->translationsLoader->load($options);
        $numberOfTextsLoaded = 0;
        foreach ($texts as $translationTexts) {
            $numberOfTextsLoaded += count($translationTexts);
        }
        $this->assertLessThanOrEqual($maxNumberOfTexts, $numberOfTextsLoaded);
    }

    /**
     * @param array $expected
     * @param array $actual
     */
    private function assertTranslations(array $expected, array $actual)
    {
        $this->assertEquals(array_keys($expected), array_keys($actual));
        foreach ($actual as $file => $translationTexts) {
            /** @var TranslationText $translationText */
            foreach ($translationTexts as $translationTextKey => $translationText) {
                $this->assertInstanceOf(TranslationText::class, $translationText);
                $this->assertEquals($expected[$file][$translationTextKey], $translationText->getTranslations());
            }
        }
    }
}