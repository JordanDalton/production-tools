<?php
/**
 *  ApiDocumentationController
 *
 *  Description goes here..
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 24, 2012, 9:41:52 AM
 */
class ApiDocumentationController extends Zend_Controller_Action
{
    /**
     * @var mixed The params passed at the URI
     */
    private $_params;
    /**
     * Set the page title.
     * 
     * @var string
     */
    protected $_pageTitle = 'Api Documentation';
    /**
     * @var string The path to the view file.
     */
    protected $_viewFilePath;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {        
        // Get the parameters from the URI
        $this->_params = $this->_request->getParams();
        
        /*
         * GET PATHS
         *  1) Controller
         *  2) Action
         *  3) Scripts
         */
        $viewScriptPath = $this->view->getScriptPaths();
        $viewFilePath   = $viewScriptPath[0] . $this->getViewScript();
        $fileUpdated    = date ("F d Y H:i:s.", filemtime($viewFilePath));
        
        // Pass file update timestamp to view.
        $this->view->fileUpdated = $fileUpdated;
        
        // Append Login Stylesheet
        $this->view->headLink()->appendStylesheet('/css/api/api-documentation/apiApiDocumentationIndexAction.css', 'all');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Run before an action is dispatched.
     */
    public function preDispatch()
    {
        // Is user logged in?
        $this->_helper->login();

        // Is user allowed?
        $allowed = $this->_helper->Acl();

        // Redirect if they do not have permissions
        if(!$allowed) $this->_redirector->goToSimple('denied', 'error', 'default');
    }
    
    //--------------------------------------------------------------------------

    /**
     * Post Dispatch
     */
    public function postDispatch()
    {
        // Set the page title
        $this->view->headTitle()->prepend($this->_pageTitle . ' | API Documentation ');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Main Page
     */
    public function indexAction()
    {
        $this->_pageTitle = 'Dashboard';
    }
    
    //--------------------------------------------------------------------------

    /**
     * API Keys
     */
    public function apiKeysAction()
    {
        $this->_pageTitle = 'API Keys';
    }
    
    //--------------------------------------------------------------------------

    /**
     * Establishing Connection
     */
    public function establishingConnectionAction()
    {
        $this->_pageTitle = 'Establishing Connection';
    }
    
    //--------------------------------------------------------------------------

    /**
     * Establishing Connection
     */
    public function handlingResultsAction()
    {
        $this->_pageTitle = 'Handling Results';
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * GET Report
     */
    public function getReportAction()
    {
        $this->_pageTitle = 'GET Report';
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * GET Work Center
     */
    public function getWorkCenterAction()
    {
        $this->_pageTitle = 'GET work-center';
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * GET Work Center Group
     */
    public function getWorkCenterGroupAction()
    {
        $this->_pageTitle = 'GET work-center-group';
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * PUT Work Center Group
     */
    public function putWorkCenterGroupAction()
    {
        $this->_pageTitle = 'PUT work-center-group';
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * DELETE Work Center Group
     */
    public function deleteWorkCenterGroupAction()
    {
        $this->_pageTitle = 'DELETE work-center-group';
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * GET Work Order Status
     */
    public function getWorkOrderStatusAction()
    {
        $this->_pageTitle = 'GET work-order-status';
    }
    
    //--------------------------------------------------------------------------
}

/* End of file ApiDocumentationController.php */