<?php
/**
 *  AuthController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 26, 2012, 1:40:37 PM
 */
class AuthController extends Zend_Controller_Action
{
    /**
     * @var Yadif_Container
     */
    protected $_container;
    /**
     * @var Zend_Config
     */
    protected $_config;
    /**
     * @var string The action page title
     */
    protected $_pageTitle; 
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->_container       = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config          = $this->_container->getConfig();
        $this->_redirector      = $this->_helper->getHelper('Redirector');
        $this->_redirector->setExit(true);

        // Change the layout file we're using
        //$this->_helper->layout->setLayout('layout-login');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Post Dispatch
     */
    public function postDispatch()
    {
        // Set the page title
        $this->view->headTitle()->prepend($this->_pageTitle);

        // Append Login Stylesheet
        $this->view->headLink()->appendStylesheet('/css/gui/auth/8d7e2062b62a4a745c67d280a38f3078f3ff48c3.css', 'all');
        
        // Append Login Javascript File.
        $this->view->inlineScript()->appendFile('/js/gui/auth/4d6a05274bdd5437c58a5bbbdbf9554b21ecc264.js');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * @ignore
     */
    public function indexAction()
    {
        $this->_redirector->gotoSimple('login', 'auth');   
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Login Page
     */
    public function loginAction()
    {
        // CHECK FOR IDENTITY/SESSION
        if(Zend_Auth::getInstance()->hasIdentity())
        {
            // IF ALREADY LOGGED IN, ATTEMPT TO SENT TO ADMIN PAGE
            $this->_redirector->goToSimple('index', 'dashboard', '');
        }
        
        // Lod the Login Form
        $form = new Application_Form_Login();
        $form->setAction($this->view->url());
        $form->setUserParams($this->_getParam('userParams'));
        
        /*
         * Postback Event Logic
         */
        if($this->_request->isPost())
        {
            // Get the postback data
            $formData = $this->_request->getPost();
            
            // Did form pass validation?
            $isValid = $form->isValid($formData);
            
            // Populate the form 
            $form->populate($formData);
            
            // Pass the form to the view
            $this->view->form = $form;
            
            // Access active directory
            $authAdapter = new Zend_Auth_Adapter_Ldap
            (
                $this->_config->ldap->toArray(),
                $form->getValue('username'),
                $form->getValue('password')
            );
            
            // Validate the credentials supplied, aginst that of active directory
            $authResult = $authAdapter->authenticate();
            
            // Failed authorization
            if(!$authResult->isValid())
            {
                $messages = $authResult->getMessages();
                $this->view->message = $messages[0];
                return $this->render('login');
            }
            
            // Store the user session
            $storage = Zend_Auth::getInstance()->getStorage();
            $storage->write($form->getValue('username'));
            
            // Build other parameters
            $string = $formData['userParams'];
            $userParams = WrsGroup_RequestUtils::getParamsForRedirect($string);
            
            // Redirect the user to dashboard if they were refered by auth controller.
            if($userParams['controller'] == 'auth')
            {
                $this->_redirector->gotoSimple('index', 'dashboard', '');
            }
            
            // Reirect the user
            $this->_redirector->gotoSimple
            (
                $userParams['action'],
                $userParams['controller'],
                $userParams['module'],
                $userParams['other']
            );   
        }
        
        /***********************************************************************
         * Now Prepare the view
         */
        
        // Set the page title
        $this->_pageTitle = 'Login';
        
        // Pass the login form to view
        $this->view->form = $form;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Log the user out of their session.
     */
    public function logoutAction()
    {
        // LOGOUT
        Zend_Auth::getInstance()->clearIdentity();
        
        // REDIRECT
        return $this->_redirector->goToSimple('index', 'index', '');
    }
    
    //--------------------------------------------------------------------------
}
/* End of file AuthController.php */
/* Location: application/controllers/AuthController.php */