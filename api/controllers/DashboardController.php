<?php
/**
 *  DashboardController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 31, 2012, 10:39:55 AM
 */
class DashboardController extends Zend_Controller_Action
{
    /**
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * @var Yadif_Container
     */
    protected $_container;
    
    /**
     * Set the page title.
     * 
     * @var string
     */
    protected $_pageTitle = 'Untitled';
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->_container   = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config      = $this->_container->getConfig();  
        $this->_redirector  = $this->_helper->getHelper('Redirector');
        $this->_redirector->setExit(true);
                
        // Append Login Stylesheet
        $this->view->headLink()->appendStylesheet('/css/api/dashboard/apiDashboardIndexAction.css', 'all');
        
        // Append layout javascript file
        $this->view->inlineScript()->appendFile('/js/api/apiLayout.js');
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
        $this->view->headTitle()->prepend($this->_pageTitle);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Dashboard Homepage
     */
    public function indexAction()
    {
        // Set the page title
        $this->_pageTitle = 'Dashboard';
    }
    
    //--------------------------------------------------------------------------
}
/* End of file DashboardController.php */
/* Location: api/controllers/DashboardController.php */