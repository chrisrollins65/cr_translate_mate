<?php

namespace CrTranslateMate\Translation;

use CrTranslateMate\Exceptions\NoTranslationForTextException;

class TranslationText
{
    /** @var string */
    private $textKey;
    /** @var array */
    private $translations = array();
    /** @var array */
    private $languageCodes;

    /**
     * @param string $textKey
     * @param array $languageCodes
     */
    public function __construct($textKey, $languageCodes)
    {
        $this->textKey = $textKey;
        $this->languageCodes = $languageCodes;
    }

    /**
     * @param string $languageCode
     * @param string $translation
     */
    public function addTranslation($languageCode, $translation)
    {
        $this->translations[$languageCode] = $translation;
    }

    /**
     * @return bool
     */
    public function isFullyTranslated()
    {
        foreach ($this->languageCodes as $languageCode) {
            if (!array_key_exists($languageCode, $this->translations)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $string
     * @return bool
     */
    public function stringExistsInTranslations($string)
    {
        foreach ($this->languageCodes as $languageCode) {
            if (isset($this->translations[$languageCode]) && is_string($this->translations[$languageCode])
                && stripos($this->translations[$languageCode], $string) !== FALSE
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $languageCode
     * @return bool
     */
    public function hasTranslationForLanguage($languageCode)
    {
        return isset($this->translations[$languageCode]);
    }

    /**
     * @param string $languageCode
     * @return string
     * @throws NoTranslationForTextException
     */
    public function getTranslation($languageCode)
    {
        if (!$this->hasTranslationForLanguage($languageCode)) {
            throw new NoTranslationForTextException(
                'Text ' . $this->textKey . ' doesn\'t have a translation for language ' . $languageCode
            );
        }
        return $this->translations[$languageCode];
    }

    /**
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}