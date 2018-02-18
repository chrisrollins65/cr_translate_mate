<?php

namespace CrTranslateMate\Language;

use CrTranslateMate\Constants;
use CrTranslateMate\Exceptions\DirectoryNotFoundException;

class Language
{
    /** @var int */
    private $language_id;
    /** @var string */
    private $name;
    /** @var string */
    private $code;
    /** @var string */
    private $locale;
    /** @var string */
    private $image;
    /** @var string */
    private $directory;
    /** @var int */
    private $sort_order;
    /** @var int */
    private $status;

    /**
     * @param $languageData
     * @param Constants $constants
     * @throws DirectoryNotFoundException
     */
    public function __construct($languageData, Constants $constants)
    {
        foreach ($languageData as $key=>$value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->checkDirectory($constants->get('DIR_LANGUAGE'));
    }

    /**
     * @param $languageDirectory
     * @throws DirectoryNotFoundException
     */
    private function checkDirectory($languageDirectory)
    {
        if (empty($this->directory) || !is_dir($languageDirectory . $this->directory)) {
            if (!empty($this->code) && is_dir($languageDirectory . $this->code))
            {
                $this->directory = $this->code;
            }
            else {
                throw new DirectoryNotFoundException('Unable to locate the directory for language: ' . $this->name);
            }
        }
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sort_order;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }


}