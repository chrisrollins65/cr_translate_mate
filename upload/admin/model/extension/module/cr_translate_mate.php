<?php

use CrTranslateMate\Constants;
use CrTranslateMate\Dto\TextSearchOptionsDto;
use CrTranslateMate\Language\LanguageCollection;
use CrTranslateMate\Language\LanguageFileManager;
use CrTranslateMate\Loader;
use CrTranslateMate\Requests\SaveTranslationRequest;
use CrTranslateMate\Translation\TranslationsLoader;
use CrTranslateMate\Translation\TranslationsSaver;
use CrTranslateMate\ViewHelper\FileDropdown;

/**
 * Class ModelExtensionModuleCrTranslateMate
 */
class ModelExtensionModuleCrTranslateMate extends Model
{

    /** @var CrTranslateMateModel */
    protected $model; // instance of the model - used to avoid OpenCart's proxy system

    /**
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $constants = new Constants();
        $languageFileManager = new LanguageFileManager($constants);
        $loader = new Loader($registry);
        /** @var ModelLocalisationLanguage $languageModel */
        $languageModel = $loader->loadModel('localisation/language');
        $languageCollection = new LanguageCollection($languageModel->getLanguages(), $constants);
        $this->model = new CrTranslateMateModel(
            $loader,
            $languageFileManager,
            $registry->get('language'),
            $languageCollection
        );
        parent::__construct($registry);
    }

    /**
     * Get a real instance of this model (as opposed to the proxied version OpenCart creates)
     *
     * @return CrTranslateMateModel
     */
    public function getInstance()
    {
        return $this->model;
    }
}

/**
 * Class CrTranslateMateModel
 *
 * OpenCart's proxy system creates a new reflected model object each time a model function is called.
 * That means data is not preserved in the model from one function to another. To fix this, I've created
 * a custom model outside the official model class above. The Translate Mate controller will ask for an
 * instance of this class instead of using OpenCart's proxied version.
 */
class CrTranslateMateModel
{
    /** @var string */
    protected $extensionName = 'cr_translate_mate';
    /** @var Loader */
    private $loader;
    /** @var LanguageFileManager */
    private $languageFileManager;
    /** @var Language */
    private $languageManager;
    /** @var LanguageCollection */
    private $languageCollection;
    /** @var TranslationsLoader */
    private $translationsLoader;

    /**
     * @param Loader $loader
     * @param $languageFileManager
     * @param Language $languageManager
     * @param LanguageCollection $languageCollection
     */
    public function __construct(
        Loader $loader,
        $languageFileManager,
        Language $languageManager,
        LanguageCollection $languageCollection
    )
    {
        $this->loader = $loader;
        $this->languageFileManager = $languageFileManager;
        $this->languageManager = $languageManager;
        $this->languageCollection = $languageCollection;
        $this->translationsLoader = new TranslationsLoader($this->languageFileManager, $this->languageCollection);
    }

    /**
     * @param $input
     * @return array
     */
    public function saveTranslation($input)
    {
        $request = new SaveTranslationRequest($input);
        $translationSaver = new TranslationsSaver($this->languageFileManager, $this->languageManager);
        $translationSaver->save($request);
        return array('success' => $request->getTranslation());
    }

    /**
     * Get the name to use for the main language file for each language (english.php, spanish.php, etc)
     *
     * @return string
     */
    public function mainLangFileStr()
    {
        return crHtmlentities($this->loader->loadLanguageText('text_main_lang_file', 'module/' . $this->extensionName));
    }

    /**
     * @param TextSearchOptionsDto $options
     * @return array
     */
    public function loadTexts(TextSearchOptionsDto $options)
    {
        return $this->translationsLoader->load($options);
    }

    /**
     * @param string $userInterface
     * @return string
     */
    public function fileHTMLSelect($userInterface = "catalog")
    {
        $fileDropdownBuilder = new FileDropdown($this->languageFileManager, $this->mainLangFileStr());
        return $fileDropdownBuilder->buildDropdown($userInterface);
    }

    /**
     * @return string
     */
    public function getLastLoadedFile()
    {
        return $this->translationsLoader->getLastLoadedFile();
    }

    /**
     * @return LanguageCollection
     */
    public function getLanguages()
    {
        return $this->languageCollection;
    }
}