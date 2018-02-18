<?php

namespace CrTranslateMate\Dto;

use PHPUnit\Framework\TestCase;

class TextSearchOptionsDtoTest extends TestCase
{
    private $options = array(
        'numberOfTexts' => 2,
        'onlyNonTranslated' => true,
        'directoryBase' => 'admin',
        'singleFileToLoad' => 'fileToLoad.php',
        'keyToSearchFor' => 'searchKey',
        'textToSearchFor' => 'search text',
        'fileToStartAfter' => 'fileToStartAfter.php',
    );

    public function testSettersAndGetters()
    {
        $dto = new TextSearchOptionsDto();
        $dto->setNumberOfTexts($this->options['numberOfTexts'])
            ->setOnlyNonTranslated($this->options['onlyNonTranslated'])
            ->setDirectoryBase($this->options['directoryBase'])
            ->setTextToSearchFor($this->options['textToSearchFor'])
            ->setFileToStartAfter($this->options['fileToStartAfter'])
            ->setKeyToSearchFor($this->options['keyToSearchFor'])
            ->setSingleFileToLoad($this->options['singleFileToLoad']);

        $this->assertEquals($this->options['numberOfTexts'], $dto->getNumberOfTexts());
        $this->assertEquals($this->options['onlyNonTranslated'], $dto->onlyNonTranslated());
        $this->assertEquals($this->options['directoryBase'], $dto->getDirectoryBase());
        $this->assertEquals($this->options['textToSearchFor'], $dto->getTextToSearchFor());
        $this->assertEquals($this->options['fileToStartAfter'], $dto->getFileToStartAfter());
        $this->assertEquals($this->options['keyToSearchFor'], $dto->getKeyToSearchFor());
        $this->assertEquals($this->options['singleFileToLoad'], $dto->getSingleFileToLoad());
    }
}