<?php
/**
 * Bootstrap class
 *
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @var Zend_Config
     */
    protected $_appConfig;

    //--------------------------------------------------------------------------
    
    /**
     * Gets the config object used for initializing the application
     *
     * @return Zend_Config A config object
     */
    public function getAppConfig()
    {
        if (!$this->_appConfig) {
            $this->_appConfig = new Zend_Config($this->getOptions());
        }
        return $this->_appConfig;
    }
    
    //--------------------------------------------------------------------------

    /**
     * Resource initializer
     *
     * @return Zend_Loader_Autoloader_Resource The resource loader
     */
    protected function _initResources()
    {
        $resourceloader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));

        $resourceloader->addResourceType('model', 'models', 'Model');
        $resourceloader->addResourceType('form', 'forms', 'Form');

        return $resourceloader;
    }
    
    //--------------------------------------------------------------------------

    protected function _initContainer()
    {
        $objectConfig = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/objects.ini'
        );
        $environment = $this->getEnvironment();

        $config = $this->getAppConfig();
        $container = new Yadif_Container($objectConfig->$environment, $config);

        $this->setContainer($container);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Initializes the view
     *
     * @return Zend_View A view object
     */
    protected function _initView()
    {
        // Initialize view
        $view = new Zend_View();
        $view->doctype(Zend_View_Helper_Doctype::HTML5);
        $view->setHelperPath(
            APPLICATION_PATH . '/views/helpers',
            'Application_View_Helper'
        );

        // Add view helper path and prefix for WrsGroup library
        $view->addHelperPath(
            APPLICATION_PATH . '/../library/WrsGroup/View/Helper',
            'WrsGroup_View_Helper'
        );

        // Title
        $view->headTitle('(API) Production Tools');
        $view->headTitle()->setSeparator(' | ');

        // Meta tags
        $view->headMeta()->setCharset('UTF-8');
        $view->headMeta()
            ->appendName('description', 'Web application developed by WRS Group, Ltd.');

        $baseUrl = $this->getAppConfig()->resources->frontController->baseUrl;

        // Add stylesheets
        $view->headLink()
            ->appendStylesheet($baseUrl . '/css/api/b3b81bab4bab00e83498d0b73dcf244ffb751c8f.css', 'all');

        // JavaScript in <head>
        // Modernizr to enable HTML5 plus progressive enhancement for older browsers
        //$view->headScript()->appendFile($baseUrl . '/js/libs/modernizr-2.0.6.min.js');

        // Add jQuery
        $view->addHelperPath('ZendX/JQuery/View/Helper','ZendX_JQuery_View_Helper');
        $view->jQuery()->setLocalPath($baseUrl . '/js/libs/jquery-1.7.min.js')
            // ->setUiLocalPath($baseUrl . '/js/libs/jquery-ui-1.8.14.custom.min.js')
            ->enable()
            // ->uiEnable()
            ;
        
        // JavaScript in <body> for all pages
        // $view->inlineScript()->appendFile($baseUrl . '/js/somefile.js');

        $view->inlineScript()->appendFile('/js/api/000ad6815fc5cec3d1a47feb15b0caeeee08cf5b.js');
        //$view->inlineScript()->appendFile('/js/gui/da0e8d91b7e7186636ce76439dcba6ea351f4bc5.js');
        
        // Add the view to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

        // Return the view, so that it can be stored by the bootstrap
        return $view;
    }
    
    //--------------------------------------------------------------------------
    
    /*
     * Add Navigation
     */
    protected function _initNavigation()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml');

        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);
    }
    
    //--------------------------------------------------------------------------

    protected function _initCache()
    {
        $container = $this->getContainer();
        $cache = $container->getComponent('cache');
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('cache', $cache);
    }

    //--------------------------------------------------------------------------
    
    protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/controllers/helpers',
            'Controller_Helper'
        );
        Zend_Controller_Action_HelperBroker::addPrefix(
            'WrsGroup_Controller_Helper');
    } 

    //--------------------------------------------------------------------------
    
    protected function _initAppLog()
    {
        $container = $this->getContainer();
        Zend_Registry::set('appLog', $container->getComponent('logger'));
    }
    
    //--------------------------------------------------------------------------

    protected function _initSmtp()
    {
		// Set up SMTP mail
		$transport = $this->getContainer()->getComponent('smtp');
		Zend_Mail::setDefaultTransport($transport);
    }
    
    //--------------------------------------------------------------------------
    
	protected function _initRequest()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $request = $front->getRequest();
    	if (null === $front->getRequest()) {
            $request = new Zend_Controller_Request_Http();
            $front->setRequest($request);
        }
    	return $request;        
    } 	
    
    //--------------------------------------------------------------------------
    
    /*
     * Enable Zend_Rest_Route for specific modules
     */
    protected function _initRestRoute()
    {
        /**/
        $this->bootstrap('frontController');
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($frontController, array(), array('rest'));
        $frontController->getRouter()->addRoute('rest', $restRoute);
        /**/
    }
    
    //--------------------------------------------------------------------------
}
