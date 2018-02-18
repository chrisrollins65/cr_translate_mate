<?php

namespace CrTranslateMate\Language;

class LanguageFile
{
    /** @var string */
    private $path;
    /** @var string */
    private $baseName;
    /** @var string */
    private $language;

    /**
     * @param $path
     * @param $languageDirectory
     * @throws \Exception
     */
    public function __construct($path, $languageDirectory)
    {
        $this->path = $path;
        $this->setBaseNameAndLanguage($languageDirectory);
    }

    /**
     * @return string
     */
    public function getBaseName()
    {
        return $this->baseName;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $languageDirectory
     * @throws \Exception
     */
    private function setBaseNameAndLanguage($languageDirectory)
    {
        $languageDirectory = $this->replaceBackSlashesWithForwardSlashes($languageDirectory);
        $languageDirectory = $this->ensureLanguageDirectoryHasTrailingSlash($languageDirectory);

        $basename = $this->replaceBackSlashesWithForwardSlashes($this->path);
        $basename = $this->removeLanguageDirectoryFromPath($basename, $languageDirectory);
        $basename = $this->removePhpExtension($basename);

        $languageAndName = explode('/', $basename, 2);
        if (count($languageAndName) !== 2) {
            throw new \Exception('Expected translation file to have both a language and file name: ' . $basename);
        }

        list($this->language, $this->baseName) = $languageAndName;
    }

    /**
     * @param string $languageDirectory
     * @return string
     */
    private function ensureLanguageDirectoryHasTrailingSlash($languageDirectory)
    {
        return rtrim($languageDirectory, '/') . '/';
    }

    /**
     * @param string $path
     * @return string
     */
    private function replaceBackSlashesWithForwardSlashes($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * @param string $path
     * @param string $languageDirectory
     * @return string mixed
     */
    private function removeLanguageDirectoryFromPath($path, $languageDirectory)
    {
        return str_replace($languageDirectory, '', $path);
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function removePhpExtension($fileName)
    {
        return substr($fileName, 0, strlen($fileName) - 4);
    }

    /**
     * @return bool
     */
    public function isMainLanuageFile()
    {
        return $this->baseName == $this->language;
    }
}