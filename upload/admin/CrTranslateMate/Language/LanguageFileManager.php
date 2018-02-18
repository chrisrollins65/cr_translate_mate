<?php

namespace CrTranslateMate\Language;

use CrTranslateMate\Constants;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class LanguageFileManager
{
    /** @var array */
    private $languageDirectories;
    /** @var string */
    private $mainLanguageFileKey = '_main_lang_file';

    /**
     * @param Constants $constants
     */
    public function __construct(Constants $constants)
    {
        $this->languageDirectories = array(
            'admin' => $constants->get('DIR_LANGUAGE'),
            'catalog' => $constants->get('DIR_CATALOG') . 'language/',
        );
    }

    /**
     * @param $directoryBase
     * @return array
     */
    public function getFileList($directoryBase) {
        $list = array();
        $directory = $this->languageDirectories[$directoryBase];

        $iterator = $this->getPhpFileIterator($directory);

        foreach($iterator as $path=>$object) {
            $file = new LanguageFile($path, $directory);

            if ($file->isMainLanuageFile()) {
                $list[$this->mainLanguageFileKey][] = $file;
            }
            else {
                // setting the basename as the key in the array prevents duplicates
                $list[$file->getBaseName()][] = $file;
            }
        }
        ksort($list); // put in alphabetical order

        return $list;
    }

    /**
     * @param $directory
     * @return RegexIterator
     */
    private function getPhpFileIterator($directory)
    {
        $directoryIterator = new RecursiveDirectoryIterator($directory);
        $iterator = new RecursiveIteratorIterator($directoryIterator);
        return new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
    }

    /**
     * @param $directoryBase
     * @param $file
     * @param $language
     * @return string
     */
    public function getFilePath($directoryBase, $file, $language)
    {
        if ( $file == $this->mainLanguageFileKey ) {
            $file = $language;
        };
        $directory = $this->languageDirectories[$directoryBase];
        return $directory . $language . '/' . $file . '.php';
    }

    /**
     * @return string
     */
    public function getMainLanguageFileKey()
    {
        return $this->mainLanguageFileKey;
    }
}