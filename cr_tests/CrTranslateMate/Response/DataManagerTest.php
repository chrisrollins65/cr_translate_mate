<?php

namespace CrTranslateMate\Response;

use CrTranslateMate\Dto\TextSearchOptionsDto;
use CrTranslateMate\Language\LanguageCollection;
use CrTranslateMate\Language\LanguageTextManager;
use CrTranslateMate\Loader;
use CrTranslateMateModel;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Url;

class DataManagerTest extends TestCase
{
    /** @var CrTranslateMateModel|PHPUnit_Framework_MockObject_MockObject */
    private $modelMock;
    /** @var Loader|PHPUnit_Framework_MockObject_MockObject */
    private $loaderMock;
    /** @var Url|PHPUnit_Framework_MockObject_MockObject */
    private $linkBuilderMock;
    /** @var LanguageCollection|PHPUnit_Framework_MockObject_MockObject */
    private $languageCollectionMock;
    /** @var LanguageTextManager|PHPUnit_Framework_MockObject_MockObject */
    private $languageTextManagerMock;
    private $extensionName = "cr_translate_mate";
    /** @var DataManager */
    private $dataManager;

    protected function setUp()
    {
        $this->modelMock = $this->getMockBuilder(CrTranslateMateModel::class)
            ->setMethods(array('getLanguages', 'loadTexts'))->disableOriginalConstructor()->getMock();
        $this->loaderMock = $this->getMockBuilder(Loader::class)->disableOriginalConstructor()->getMock();
        $this->linkBuilderMock = $this->getMockBuilder(Url::class)
            ->setMethods(array('link'))->disableOriginalConstructor()->getMock();
        $this->languageCollectionMock =
            $this->getMockBuilder(LanguageCollection::class)->disableOriginalConstructor()->getMock();
        $this->languageTextManagerMock =
            $this->getMockBuilder(LanguageTextManager::class)->disableOriginalConstructor()->getMock();

        $this->dataManager = new DataManager(
            $this->modelMock,
            $this->extensionName,
            $this->loaderMock,
            $this->linkBuilderMock
        );
    }

    public function testLoadingLanguages()
    {
        $languageFile = array('file' => 'en_gb.php');
        $languageNames = array('English');
        $directories = array('path/to/english/directory');

        $this->loaderMock
            ->expects($this->once())
            ->method('loadLanguageFile')
            ->willReturn($languageFile);
        $this->modelMock
            ->expects($this->once())
            ->method('getLanguages')
            ->willReturn($this->languageCollectionMock);
        $this->languageCollectionMock
            ->expects($this->once())
            ->method('getNames')
            ->willReturn($languageNames);
        $this->languageCollectionMock
            ->expects($this->once())
            ->method('getDirectories')
            ->willReturn($directories);

        $this->dataManager->loadLanguages();
        $data = $this->dataManager->getData();

        $this->assertEquals($languageNames, $data['languageNames']);
        $this->assertEquals($directories, $data['languageDirectories']);
    }

    public function testLoadingTexts()
    {
        $texts = array('text1', 'text2');
        $options = new TextSearchOptionsDto();
        $this->modelMock
            ->expects($this->once())
            ->method('loadTexts')
            ->with($options)
            ->willReturn($texts);

        $this->dataManager->loadTexts($options);
        $data = $this->dataManager->getData();

        $this->assertEquals($texts, $data['texts']);
    }

    public function testBuildingBreadcrumbs()
    {
        $htmlSafeText = 'text';
        $link = '<a>test link</a>';
        $this->languageTextManagerMock
            ->expects($this->exactly(3))
            ->method('getHtmlSafeText')
            ->willReturn($htmlSafeText);
        $this->linkBuilderMock
            ->expects($this->exactly(3))
            ->method('link')
            ->willReturn($link);

        $this->dataManager->buildBreadcrumbs($this->languageTextManagerMock, 'token');
        $data = $this->dataManager->getData();

        $this->assertTrue(is_array($data['breadcrumbs']));
        foreach ($data['breadcrumbs'] as $breadcrumb) {
            $this->assertEquals($htmlSafeText, $breadcrumb['text']);
            $this->assertEquals($link, $breadcrumb['href']);
        }
    }

    public function testBuildingFormLinks()
    {
        $link = '<a>test link</a>';
        $this->linkBuilderMock
            ->expects($this->exactly(2))
            ->method('link')
            ->willReturn($link);

        $this->dataManager->buildFormLinks('token');
        $data = $this->dataManager->getData();

        $this->assertEquals($link, $data['action']);
        $this->assertEquals($link, $data['cancel']);
    }

    public function testLoadingCommonPageElements()
    {
        $commonElement = 'common element';
        $this->loaderMock
            ->expects($this->exactly(3))
            ->method('loadController')
            ->willReturn($commonElement);

        $this->dataManager->loadCommonPageElements();
        $data = $this->dataManager->getData();

        $this->assertEquals($commonElement, $data['header']);
        $this->assertEquals($commonElement, $data['column_left']);
        $this->assertEquals($commonElement, $data['footer']);
    }

    public function testSetter()
    {
        $textKey = 'testKey';
        $text = 'test text';

        $this->dataManager->set($textKey, $text);
        $data = $this->dataManager->getData();

        $this->assertEquals($text, $data[$textKey]);
    }
}