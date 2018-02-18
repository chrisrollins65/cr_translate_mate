<?php

namespace CrTranslateMate\Translation;

use CrTranslateMate\Exceptions\UnableToWriteToFileException;
use CrTranslateMate\Language\LanguageFileManager;
use CrTranslateMate\Requests\SaveTranslationRequest;

class TranslationsSaver
{
    /** @var LanguageFileManager */
    private $languageFileManager;

    /**
     * @param LanguageFileManager $languageFileManager
     */
    public function __construct(LanguageFileManager $languageFileManager)
    {
        $this->languageFileManager = $languageFileManager;
    }

    /**
     * @param SaveTranslationRequest $request
     * @return bool
     */
    public function save(SaveTranslationRequest $request)
    {
        $filePath = $this->getFilePath($request->getInterface(), $request->getFileName(), $request->getLanguage());
        $translations = $this->getFileTranslations($filePath);
        $translations[$request->getKey()] = html_entity_decode($request->getTranslation(), ENT_COMPAT, 'UTF-8');
        return $this->saveFile($filePath, $translations);
    }

    /**
     * @param string $interface
     * @param string $fileName
     * @param string $language
     * @return string
     * @throws UnableToWriteToFileException
     */
    private function getFilePath($interface, $fileName, $language)
    {
        $filePath = $this->languageFileManager->getFilePath($interface, $fileName, $language);
        if (file_exists($filePath) && !is_writable($filePath)) {
            throw new UnableToWriteToFileException($filePath);
        }
        return $filePath;
    }

    /**
     * @param string $filePath
     * @return array
     */
    private function getFileTranslations($filePath)
    {
        $_ = array();
        if (file_exists($filePath)) {
            include($filePath);
        }
        return $_;
    }

    /**
     * @param string $filePath
     * @param array $translations
     * @return bool
     * @throws UnableToWriteToFileException
     */
    private function saveFile($filePath, $translations)
    {
        $this->createDirectoryIfDoesntExist(dirname($filePath));
        $fileContents = $this->generateFileContents($translations);
        if (!file_put_contents($filePath, $fileContents)) {
            throw new UnableToWriteToFileException($filePath);
        }
        return true;
    }

    /**
     * NOTE: This method of saving removes comments originally in the translation files
     * Trying to preserve the original comments didn't feel like it was worth the hassle
     *
     * @param array $translations
     * @return string
     */
    private function generateFileContents($translations)
    {
        $fileContents = "<?php";
        foreach ($translations as $key => $value) {
            $fileContents .= "\n\$_['" . $key . '\'] = \'' . addcslashes($value, "'\\") . "';";
        }
        return $fileContents;
    }

    /**
     * @param string $directory
     * @throws UnableToWriteToFileException
     */
    private function createDirectoryIfDoesntExist($directory)
    {
        if (!is_dir($directory)) {
            // attempt to get the appropriate directory permissions by looking at neighboring directories
            $directoryPermissions = 0777;
            foreach (array_filter(glob(dirname($directory) . '/*', GLOB_ONLYDIR)) as $dir) {
                $directoryPermissions = fileperms($dir);
            }
            if (!mkdir($directory, $directoryPermissions, TRUE)) {
                throw new UnableToWriteToFileException($directory);
            }
        }
    }
}
