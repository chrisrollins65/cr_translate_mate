<?php

namespace CrTranslateMate;

use Language;
use Loader as OpenCartLoader;
use Registry;

class Loader
{
    /** @var Registry */
    private $registry;
    /** @var OpenCartLoader */
    private $openCartLoader;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->openCartLoader = $registry->get('load');
    }

    /**
     * @param string $route
     * @return \Model
     * @throws \Exception
     */
    public function loadModel($route)
    {
        $this->openCartLoader->model($route);
        return $this->registry->get('model_' . str_replace('/', '_', $route));
    }

    /**
     * @param string $fileName
     * @return array
     */
    public function loadLanguageFile($fileName)
    {
        /** @var Language $languageLoader */
        $languageLoader = $this->registry->get('language');
        return $languageLoader->load($fileName);
    }

    /**
     * @param string $text
     * @param string $fileName
     * @return string
     */
    public function loadLanguageText($text, $fileName)
    {
        $texts = $this->loadLanguageFile($fileName);
        return (array_key_exists($text, $texts)) ? $texts[$text] : '';
    }

    /**
     * @param string $route
     * @param array $data
     * @return mixed
     */
    public function loadController($route, $data = array())
    {
        return $this->openCartLoader->controller($route, $data);
    }

    /**
     * @param string $route
     * @param array $data
     * @return string
     */
    public function loadView($route, $data = array())
    {
        return $this->openCartLoader->view($route, $data);
    }
}