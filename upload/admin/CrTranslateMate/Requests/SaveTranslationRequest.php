<?php

namespace CrTranslateMate\Requests;

class SaveTranslationRequest
{
    /** @var string */
    private $userInterface;
    /** @var string */
    private $fileName;
    /** @var string */
    private $language;
    /** @var string */
    private $translation;
    /** @var string */
    private $key;

    /**
     * @param array $input
     */
    public function __construct(array $input)
    {
        $properties = array_keys(get_class_vars(self::class));
        foreach ($properties as $property) {
            if (isset($input[$property])) {
                $this->$property = $input[$property];
            }
        }
    }

    /**
     * @return string
     */
    public function getInterface()
    {
        return $this->userInterface;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }


}