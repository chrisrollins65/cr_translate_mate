<?php

use CrTranslateMate\Dto\TextSearchOptionsDto;
use CrTranslateMate\ErrorHandler;
use CrTranslateMate\Language\LanguageTextManager;
use CrTranslateMate\Loader;
use CrTranslateMate\Response\DataManager;

require_once(__DIR__ . '/../../../CrTranslateMate/autoloader.php');

/**
 * Class ControllerExtensionModuleCrTranslateMate
 */
class ControllerExtensionModuleCrTranslateMate extends Controller
{
    /** @var array */
    protected $error = array();
    /** @var string */
    protected $extensionName = 'cr_translate_mate';
    /** @var CrTranslateMateModel */
    protected $model;
    /** @var array */
    protected $ajaxActions = ['load', 'save'];

    /**
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->loader = new Loader($registry);
        $this->languageTextManager = new LanguageTextManager($registry->get('language'));
    }

    public function install()
    {
        //$this->model()->install();
    }

    public function uninstall()
    {
        //$this->model()->uninstall();
    }

    /**
     * @return CrTranslateMateModel
     */
    protected function model()
    {
        if (!$this->model) {
            $this->loader->loadModel('extension/module/' . $this->extensionName);
            $this->model = $this->{'model_extension_module_' . $this->extensionName}->getInstance();
        }

        return $this->model;
    }

    public function index()
    {
        $dataManager = new DataManager($this->model(), $this->extensionName, $this->loader, $this->url);
        $dataManager->loadLanguages();
        $dataManager->buildBreadcrumbs($this->languageTextManager, $this->session->data['user_token']);

        if ($this->isAjaxRequest()) {
            $this->handleAjaxRequest($dataManager);
            return;
        } else {
            new ErrorHandler($this->log);
        }

        $this->document->setTitle($this->languageTextManager->getHtmlSafeText('heading_title'));

        // add error messages if they exist
        $dataManager->set('error_warning', (isset($this->error['warning']) ? $this->error['warning'] : ''));

        // include the file list
        $interface = isset($_GET['interface']) && $_GET['interface'] == 'admin' ? 'admin' : 'catalog';
        $dataManager->set('interface', $interface);
        $dataManager->set('fileSelect', $this->model()->fileHTMLSelect($interface));

        $dataManager->buildFormLinks($this->session->data['user_token']);

        $javascriptRoute = 'view/javascript/' . $this->extensionName;
        $this->document->addScript($javascriptRoute . '/jquery.stickytableheaders.min.js');
        $this->document->addScript($javascriptRoute . '/' . $this->extensionName . '.js');
        $this->document->addStyle($javascriptRoute . '/' . $this->extensionName . '.css');

        $dataManager->loadCommonPageElements();

        $this->response->setOutput(
            $this->loader->loadView('extension/module/' . $this->extensionName, $dataManager->getData())
        );
    }

    /**
     * @return bool
     */
    private function isAjaxRequest()
    {
        return !empty($this->request->request['action'])
        && in_array($this->request->request['action'], $this->ajaxActions);
    }

    /**
     * @param $dataManager
     */
    private function handleAjaxRequest($dataManager)
    {
        new ErrorHandler($this->log, true);
        switch ($this->request->request['action']) {
            case 'load': // Retrieve translation texts
                $this->ajaxShow($dataManager);
                return;
            case 'save': // Handle form submission
                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                    $this->ajaxStore();
                }
                return;
        }
    }

    /**
     * @param DataManager $dataManager
     */
    public function ajaxShow($dataManager)
    {
        if (!$this->validate('load')) {
            $this->returnAjaxErrors($this->error);
        }

        $options = new TextSearchOptionsDto();
        if (!empty($_GET['length'])) {
            $options->setNumberOfTexts((int)$_GET['length']);
        }
        if (!empty($_GET['startAfter'])) {
            $options->setFileToStartAfter($_GET['startAfter']);
        }
        if (!empty($_GET['singleFile'])) {
            $options->setSingleFileToLoad($_GET['singleFile']);
        }
        if (!empty($_GET['keyFilter'])) {
            $options->setKeyToSearchFor($_GET['keyFilter']);
        }
        if (!empty($_GET['textFilter'])) {
            $options->setTextToSearchFor($_GET['textFilter']);
        }
        if (!empty($_GET['userInterface'])) {
            $options->setDirectoryBase($_GET['userInterface']);
        }
        if (!empty($_GET['notTranslated'])) {
            $options->setOnlyNonTranslated($_GET['notTranslated'] === 'true');
        }
        $dataManager->loadTexts($options);

        $results = array();
        $results['html'] = $this->loader->loadView('extension/module/' . $this->extensionName . '_table', $dataManager->getData());
        $results['lastFile'] = $this->model()->getLastLoadedFile();

        $this->response->addHeader('Content-type: application/json');
        $this->response->setOutput(json_encode($results));
    }

    public function ajaxStore()
    {
        if (!$this->validate('save')) {
            $this->returnAjaxErrors($this->error);
        }
        $result = $this->model()->saveTranslation($_POST);
        if (!is_array($result) || !isset($result['success'])) {
            $this->returnAjaxErrors(array($result));
        } else {
            echo json_encode($result);
        }
    }

    /**
     * @param $errors
     */
    protected function returnAjaxErrors($errors)
    {
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }
        if (!is_array($errors)) { ?>
            <div class="alert alert-danger" role="alert"><?php print_r($errors); ?></div>
        <?php } else {
            foreach ($errors as $error) { ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php }
        }
        die();
    }

    /**
     * @param $permission
     * @return bool
     */
    protected function userHasPermission($permission)
    {
        return $this->user->hasPermission($permission, 'extension/module/' . $this->extensionName);
    }

    /**
     * @param null $action
     * @return bool
     */
    private function validate($action = null)
    {
        if ($action == 'load') {
            if (!$this->userHasPermission('access')) {
                $this->error['warning'] = $this->languageTextManager->get('error_permission');
            }
        } else if ($action == 'save') {
            if (!$this->userHasPermission('modify')) {
                $this->error['warning'] = $this->languageTextManager->get('error_permission');
            }
            if ($this->request->server['REQUEST_METHOD'] != 'POST') {
                $this->error['post'] = $this->languageTextManager->get('error_post');
            }

            $missingFields = array();
            foreach (array('fileName', 'key', 'language', 'translation', 'userInterface') as $requiredField) {
                if (!isset($_POST[$requiredField])) {
                    $missingFields[] = $requiredField;
                }
            }
            if (!empty($missingFields)) {
                $this->error['missing'] =
                    $this->languageTextManager->get('error_missing') . ' ' . join(', ', $missingFields);
            }
        }

        return !$this->error;
    }
}