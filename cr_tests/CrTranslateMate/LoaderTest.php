<?php

namespace CrTranslateMate;

use Language;
use Loader as OpenCartLoader;
use PHPUnit\Framework\TestCase;
use Registry;

class LoaderTest extends TestCase
{
    /** @var Registry | \PHPUnit_Framework_MockObject_MockObject */
    private $registryMock;
    /** @var OpenCartLoader | \PHPUnit_Framework_MockObject_MockObject */
    private $openCartLoaderMock;
    /** @var Language | \PHPUnit_Framework_MockObject_MockObject */
    private $languageMock;
    /** @var Loader */
    private $loader;

    protected function setUp()
    {
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()->setMethods(['get'])->getMock();
        $this->openCartLoaderMock = $this->getMockBuilder(OpenCartLoader::class)
            ->disableOriginalConstructor()->setMethods(['model', 'controller', 'view'])->getMock();
        $this->languageMock = $this->getMockBuilder(Language::class)
            ->disableOriginalConstructor()->setMethods(['load'])->getMock();
        $this->registryMock->expects($this->at(0))->method('get')->with('load')->willReturn($this->openCartLoaderMock);
        $this->loader = new Loader($this->registryMock);
    }

    public function testLoadingModel()
    {
        $modelRoute = 'model/route';
        $model = $this->getMockBuilder(\Model::class)->disableOriginalConstructor()->getMock();
        $this->openCartLoaderMock->expects($this->once())->method('model')->with($modelRoute);
        $this->registryMock->expects($this->once())->method('get')->with('model_model_route')->willReturn($model);
        $returnedModel = $this->loader->loadModel($modelRoute);
        $this->assertEquals($model, $returnedModel);
    }

    public function testLoadingLanguageFileText()
    {
        $searchedTextKey = 'test_key';
        $searchedText = 'Test translation';
        $languageFileName = 'languageFile.php';
        $languageFileContents = [$searchedTextKey => $searchedText];
        $this->registryMock->expects($this->once())->method('get')->with('language')->willReturn($this->languageMock);
        $this->languageMock->expects($this->once())
            ->method('load')->with($languageFileName)->willReturn($languageFileContents);
        $returnedText = $this->loader->loadLanguageText($searchedTextKey, $languageFileName);
        $this->assertEquals($searchedText, $returnedText);
    }

    public function testThatEmptyStringReturnedIfTextDoesntExistWhenLoadingLanguageFileText()
    {
        $searchedTextKey = 'test_key';
        $expectedText = '';
        $languageFileName = 'languageFile.php';
        $languageFileContents = [];
        $this->registryMock->expects($this->once())->method('get')->with('language')->willReturn($this->languageMock);
        $this->languageMock->expects($this->once())
            ->method('load')->with($languageFileName)->willReturn($languageFileContents);
        $returnedText = $this->loader->loadLanguageText($searchedTextKey, $languageFileName);
        $this->assertEquals($expectedText, $returnedText);
    }

    public function testLoadingController()
    {
        $route = 'controller/route';
        $expectedOutput = '<p>Some produced HTML output returned from loaded controller</p>';
        $this->openCartLoaderMock->expects($this->once())
            ->method('controller')->with($route)->willReturn($expectedOutput);
        $actualOutput = $this->loader->loadController($route);
        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testLoadingView()
    {
        $route = 'view/route';
        $expectedOutput = '<p>Some produced HTML output returned from loaded view</p>';
        $this->openCartLoaderMock->expects($this->once())
            ->method('view')->with($route)->willReturn($expectedOutput);
        $actualOutput = $this->loader->loadView($route);
        $this->assertEquals($expectedOutput, $actualOutput);
    }
}