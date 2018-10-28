<?php

namespace CrTranslateMate\Translation;

use CrTranslateMate\Dto\TextSearchOptionsDto;
use CrTranslateMate\Exceptions\FileNotFoundException;
use CrTranslateMate\Language\LanguageCollection;
use CrTranslateMate\Language\LanguageFileManager;

class TranslationsLoader
{
    /** @var LanguageFileManager */
    private $languageFileManager;
    /** @var LanguageCollection */
    private $languageCollection;
    /** @var string[] */
    private $languageCodes;
    /** @var string */
    private $lastLoadedFile;

    /**
     * @param LanguageFileManager $languageFileManager
     * @param LanguageCollection $languageCollection
     */
    public function __construct(LanguageFileManager $languageFileManager, LanguageCollection $languageCollection)
    {
        $this->languageFileManager = $languageFileManager;
        $this->languageCollection = $languageCollection;
        $this->languageCodes = $languageCollection->getCodes();
    }

    /**
     * @param TextSearchOptionsDto $options
     * @return array
     */
    public function load(TextSearchOptionsDto $options)
    {
        ob_start();
        $fileList = $this->languageFileManager->getFileList($options->getDirectoryBase());

        $texts = array();
        $numberOfTexts = 0;
        $reachedFileToStartAfter = is_null($options->getFileToStartAfter());
        while ($numberOfTexts < $options->getNumberOfTexts() && $fileName = key($fileList)) {
        	next($fileList);
            if (!$this->fileShouldBeLoaded($reachedFileToStartAfter, $options->getSingleFileToLoad(), $fileName)) {
                $reachedFileToStartAfter = $fileName == $options->getFileToStartAfter();
                continue;
            }

            $fileTexts = $this->getTextsFromFile($fileName, $options);

            if (!empty($fileTexts)) {
                $texts[$fileName] = $fileTexts;
                // since we load the whole file, we might surpass the number of texts requested
                // but that's fine. We'll just stop loading more files at that point
                $numberOfTexts += count($fileTexts);
            }

            $this->lastLoadedFile = $fileName;

            if (!is_null($options->getSingleFileToLoad())) {
                break;
            }
        }

        ob_end_clean(); // discard any output invalid translation files may have printed

        return $texts;
    }

    /**
     * @param bool $reachedFileToStartAfter
     * @param string $singleFileToLoad
     * @param string $currentFile
     * @return bool
     */
    private function fileShouldBeLoaded($reachedFileToStartAfter, $singleFileToLoad, $currentFile)
    {
        return ($reachedFileToStartAfter && is_null($singleFileToLoad) || $currentFile == $singleFileToLoad);
    }

    /**
     * @param string $fileName
     * @param TextSearchOptionsDto $options
     * @return array
     * @throws FileNotFoundException
     */
    private function getTextsFromFile($fileName, $options)
    {
        /** @var TranslationText[] $texts */
        $texts = array();
        foreach ($this->languageCodes as $languageCode) {
            $path = $this->languageFileManager->getFilePath($options->getDirectoryBase(), $fileName, $languageCode);
            if (!file_exists($path)) {
                continue;
            }

            $_ = array(); // OpenCart saves language strings in an array "$_"
            include($path);

            foreach ($_ as $textKey => $textValue) {
                if ($this->textKeyIsValidForThisSearch($textKey, $options->getKeyToSearchFor())) {
                    if (!array_key_exists($textKey, $texts)) {
                        $texts[$textKey] = new TranslationText($textKey, $this->languageCodes);
                    }
                    $texts[$textKey]->addTranslation($languageCode, $textValue);
                }
            }
        }

        if ($options->onlyNonTranslated()) {
            $texts = $this->filterOutTranslatedTexts($texts);
        }

        if (!is_null($options->getTextToSearchFor())) {
            $texts = $this->filterByText($texts, $options->getTextToSearchFor());
        }

        return $texts;
    }

    /**
     * @param string $text
     * @param string $keyToSearchFor
     * @return bool
     */
    private function textKeyIsValidForThisSearch($text, $keyToSearchFor)
    {
        return is_null($keyToSearchFor) || stripos($text, $keyToSearchFor) !== FALSE;
    }

    /**
     * @param TranslationText[] $texts
     * @return mixed
     */
    private function filterOutTranslatedTexts($texts)
    {
        foreach ($texts as $textKey => $text) {
            if ($text->isFullyTranslated()) {
                unset($texts[$textKey]);
            }
        }

        return $texts;
    }

    /**
     * @param TranslationText[] $texts
     * @param $textSearchedFor
     * @return mixed
     */
    private function filterByText($texts, $textSearchedFor)
    {
        foreach ($texts as $textKey => $text) {
            if (!$text->stringExistsInTranslations($textSearchedFor)) {
                unset($texts[$textKey]);
            }
        }

        return $texts;
    }

    /**
     * @return string
     */
    public function getLastLoadedFile()
    {
        return $this->lastLoadedFile;
    }
}