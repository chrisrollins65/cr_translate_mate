<?php

namespace CrTranslateMate\FakeBuilders;

use CrTranslateMate\Language\Language;

class FakeLanguageBuilder
{
    /**
     * @var array
     */
    public static $languageData = array(
        array( // Example from OpenCart 3.0.2.0
            'language_id' => '1',
            'name' => 'English',
            'code' => 'en-gb',
            'locale' => 'en-US,en_US.UTF-8,en_US,en-gb,english',
            'image' => 'gb.png',
            'directory' => 'english',
            'sort_order' => '1',
            'status' => '1',
        ),
        array(
            'language_id' => '2',
            'name' => 'English2',
            'code' => 'en-gb2',
            'locale' => 'en-US,en_US.UTF-8,en_US,en-gb,english2',
            'image' => 'gb.png2',
            'directory' => 'english2',
            'sort_order' => '2',
            'status' => '2',
        ),
    );

    /**
     * @param $constants
     * @return Language
     */
    public static function buildLanguage($constants)
    {
        return new Language(self::$languageData[0], $constants);
    }

    /**
     * @param $constants
     * @return Language
     */
    public static function buildAnotherLanguage($constants)
    {
        return new Language(self::$languageData[1], $constants);
    }

    /**
     * @param string $interface
     * @return string
     */
    public static function getLanguageDirectory($interface = 'DIR_LANGUAGE')
    {
        $directory = substr(__DIR__, 0, strpos(__DIR__, 'cr_tests')) . 'cr_tests/CrTranslateMate/Language/fake_dir_';
        return $interface == 'DIR_LANGUAGE' ? $directory . 'language/' : $directory;
    }
}