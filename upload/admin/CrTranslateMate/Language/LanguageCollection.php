<?php

namespace CrTranslateMate\Language;

use CrTranslateMate\ArrayIterator;

class LanguageCollection implements \IteratorAggregate
{
    /** @var Language[] */
    private $languages;

    /**
     * @param $languageData
     * @param $constants
     */
    public function __construct($languageData, $constants)
    {
        foreach ($languageData as $key => $data) {
            $this->languages[$key] = new Language($data, $constants);
        }
    }

    /**
     * @return array
     */
    public function getCodes()
    {
        return array_map(
            function ($language) {
                /** @var Language $language */
                return $language->getCode();
            }, $this->languages
        );
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return array_map(
            function ($language) {
                /** @var Language $language */
                return $language->getName();
            }, $this->languages
        );
    }

    /**
     * @return array
     */
    public function getDirectories()
    {
        return array_map(
            function ($language) {
                /** @var Language $language */
                return $language->getDirectory();
            }, $this->languages
        );
    }

    /**
     * @return Language[]|ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->languages);
    }
}