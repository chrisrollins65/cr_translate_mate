<?php

namespace CrTranslateMate\Response;

use CrTranslateMate\Dto\TextSearchOptionsDto;
use CrTranslateMate\Language\LanguageTextManager;
use CrTranslateMate\Loader;
use CrTranslateMateModel;
use Url;

/**
 * Class DataManager
 */
class DataManager
{
    /** @var CrTranslateMateModel */
    private $model;
    /** @var string */
    private $extensionName;
    /** @var Loader */
    private $loader;
    /** @var Url */
    private $linkBuilder;
    /** @var array */
    private $data = array();

    /**
     * @param CrTranslateMateModel $model
     * @param $extensionName
     * @param Loader $loader
     * @param Url $linkBuilder
     */
    public function __construct(CrTranslateMateModel $model, $extensionName, Loader $loader, Url $linkBuilder)
    {
        $this->model = $model;
        $this->extensionName = $extensionName;
        $this->loader = $loader;
        $this->linkBuilder = $linkBuilder;
    }

    public function loadLanguages()
    {
        // load the module's language file directly into the $data that will be sent to the view
        $this->data =
            array_merge($this->data, $this->loader->loadLanguageFile('extension/module/' . $this->extensionName));

        $languages = $this->model->getLanguages();
        $this->data['languageNames'] = $languages->getNames();
        $this->data['languageDirectories'] = $languages->getDirectories();
    }

    /**
     * @param TextSearchOptionsDto $options
     */
    public function loadTexts($options)
    {
        $this->data['texts'] = $this->model->loadTexts($options);
    }

    /**
     * @param LanguageTextManager $language
     * @param string $token
     */
    public function buildBreadcrumbs($language, $token)
    {
        $this->data['breadcrumbs'] = array(
            array( // Home
                'text' => $language->getHtmlSafeText('text_home'),
                'href' => $this->linkBuilder->link('common/dashboard', 'user_token=' . $token, 'SSL'),
            ),
            array( // Modules
                'text' => $language->getHtmlSafeText('text_module'),
                'href' => $this->linkBuilder->link('extension/module', 'user_token=' . $token, 'SSL'),
            ),
            array( // This module
                'text' => $language->getHtmlSafeText('heading_title'),
                'href' =>
                    $this->linkBuilder->link('extension/module/' . $this->extensionName, 'user_token=' . $token, 'SSL')
            ),
        );
    }

    /**
     * @param $token
     */
    public function buildFormLinks($token)
    {
        $this->data['action'] = html_entity_decode(
            $this->linkBuilder->link('extension/module/' . $this->extensionName, 'user_token=' . $token . '&action=', 'SSL')
        );
        $this->data['cancel'] = $this->linkBuilder->link('extension/module', 'user_token=' . $token, 'SSL');
    }

    public function loadCommonPageElements()
    {
        $this->data['header'] = $this->loader->loadController('common/header');
        $this->data['column_left'] = $this->loader->loadController('common/column_left');
        $this->data['footer'] = $this->loader->loadController('common/footer');
    }

    /**
     * @param $textKey
     * @param $text
     */
    public function set($textKey, $text)
    {
        $this->data[$textKey] = $text;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}