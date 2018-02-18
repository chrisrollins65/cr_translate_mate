<?php

namespace CrTranslateMate\Dto;

class TextSearchOptionsDto
{
    /** @var int */
    private $numberOfTexts = 20;
    /** @var bool */
    private $onlyNonTranslated = false;
    /** @var string */
    private $directoryBase = 'catalog';
    /** @var string|null */
    private $singleFileToLoad = null;
    /** @var string|null */
    private $keyToSearchFor = null;
    /** @var string|null */
    private $textToSearchFor = null;
    /** @var string|null */
    private $fileToStartAfter = null;

    /**
     * @return int
     */
    public function getNumberOfTexts()
    {
        return $this->numberOfTexts;
    }

    /**
     * @param int $numberOfTexts
     * @return TextSearchOptionsDto
     */
    public function setNumberOfTexts($numberOfTexts)
    {
        $this->numberOfTexts = $numberOfTexts;
        return $this;
    }

    /**
     * @return boolean
     */
    public function onlyNonTranslated()
    {
        return $this->onlyNonTranslated;
    }

    /**
     * @param boolean $onlyNonTranslated
     * @return TextSearchOptionsDto
     */
    public function setOnlyNonTranslated($onlyNonTranslated)
    {
        $this->onlyNonTranslated = $onlyNonTranslated;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectoryBase()
    {
        return $this->directoryBase;
    }

    /**
     * @param string $directoryBase
     * @return TextSearchOptionsDto
     */
    public function setDirectoryBase($directoryBase)
    {
        $this->directoryBase = $directoryBase;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSingleFileToLoad()
    {
        return $this->singleFileToLoad;
    }

    /**
     * @param string $singleFileToLoad
     * @return TextSearchOptionsDto
     */
    public function setSingleFileToLoad($singleFileToLoad)
    {
        $this->singleFileToLoad = $singleFileToLoad;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getKeyToSearchFor()
    {
        return $this->keyToSearchFor;
    }

    /**
     * @param string $keyToSearchFor
     * @return TextSearchOptionsDto
     */
    public function setKeyToSearchFor($keyToSearchFor)
    {
        $this->keyToSearchFor = $keyToSearchFor;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTextToSearchFor()
    {
        return $this->textToSearchFor;
    }

    /**
     * @param string $textToSearchFor
     * @return TextSearchOptionsDto
     */
    public function setTextToSearchFor($textToSearchFor)
    {
        $this->textToSearchFor = $textToSearchFor;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFileToStartAfter()
    {
        return $this->fileToStartAfter;
    }

    /**
     * @param string $fileToStartAfter
     * @return TextSearchOptionsDto
     */
    public function setFileToStartAfter($fileToStartAfter)
    {
        $this->fileToStartAfter = $fileToStartAfter;
        return $this;
    }
}