<?php

namespace CrTranslateMate\ViewHelper;

use CrTranslateMate\Language\LanguageFile;
use CrTranslateMate\Language\LanguageFileManager;
use CrTranslateMate\Language\LanguageTextManager;

class FileDropdown
{
    /** @var LanguageFileManager */
    private $languageFileManager;
    /** @var string */
    private $mainLanguageFileName;

    /**
     * @param LanguageFileManager $languageFileManager
     * @param $mainLanguageFileName
     */
    public function __construct(LanguageFileManager $languageFileManager, $mainLanguageFileName)
    {
        $this->languageFileManager = $languageFileManager;
        $this->mainLanguageFileName = $mainLanguageFileName;
    }

    /**
     * @param string $userInterface
     * @return string
     */
    public function buildDropdown($userInterface="catalog") {
        $fileList = $this->languageFileManager->getFileList($userInterface);
        $fileTree = $this->convertPathsIntoTree($fileList);
        return $this->buildDropdownOptionsHtml($fileTree);
    }

    /**
     * @param array $fileTree
     * @return string
     */
    private function buildDropdownOptionsHtml(array $fileTree) {
        $html = '';
        foreach ( $fileTree as $key=>$file ) {
            if ( is_array($file) ) {
                $html .= '<optgroup label="'.crHtmlentities($key).'">'.$this->buildDropdownOptionsHtml($file).'</optgroup>';
            }
            elseif ($file instanceof LanguageFile) {
                if ($file->isMainLanuageFile()) {
                    $fileName = $this->mainLanguageFileName;
                    $optionValue = $this->languageFileManager->getMainLanguageFileKey();
                } else {
                    $fileName = $key;
                    $optionValue = crHtmlentities($file->getBaseName());
                }
                $html .='<option value="'.$optionValue.'">'.crHtmlentities($fileName).'</option>';
            }
        }
        return $html;
    }

    /**
     * @param $fileList
     * @return array
     */
    private function convertPathsIntoTree($fileList)
    {
        $fileTree = array();
        foreach ($fileList as $path=>$files) {
            $pathParts = explode('/', $path);
            $subTree = array(array_pop($pathParts)=>reset($files));
            foreach (array_reverse($pathParts) as $dir) {
                $subTree = array($dir => $subTree);
            }
            $fileTree = array_merge_recursive($fileTree, $subTree);
        }

        return $fileTree;
    }
}