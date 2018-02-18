<?php

namespace CrTranslateMate\Language;


class LanguageTextManager
{
    /** @var \Language */
    private $language;

    /**
     * @param \Language $language
     */
    public function __construct(\Language $language)
    {
        $this->language = $language;
    }

    /**
     * @param string $textKey
     * @return string
     */
    public function get($textKey)
    {
        return $this->language->get($textKey);
    }

    /**
     * @param string $textKey
     * @return string
     */
    public function getHtmlSafeText($textKey)
    {
        return crHtmlentities($this->language->get($textKey));
    }

    /**
     * @param string $textKey
     * @return string
     */
    public function getHtmlSpecialCharsText($textKey)
    {
        return htmlspecialchars($this->language->get($textKey), ENT_COMPAT | ENT_HTML401, 'UTF-8');
    }
}