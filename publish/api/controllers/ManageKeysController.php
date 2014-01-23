<?php
/**
 *  ManageKeysController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 31, 2012, 10:08:21 AM
 */
class ManageKeysController extends Zend_Controller_Action
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
     * @var string The default title for the page.
     */
    protected $_pageTitle = 'Untitled';
    
    //--------------------------------------------------------------------------
    
    public function init()
    {        
        $this->_container = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config    = $this->_container->getConfig();
        $this->_db        = $this->_container->getComponent('db');
        
        // Append layout javascript file
        $this->view->inlineScript()->appendFile('/js/api/da0e8d91b7e7186636ce76439dcba6ea351f4bc5.js');
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
     * Manage-Keys Main Page
     */
    public function indexAction()
    {
        /***********************************************************************
         * Prepare the view
         **********************************************************************/
        
        // Use an additional stylesheet for this page.
        $this->view->headLink()->appendStylesheet('/css/api/manage-keys/aeef1e6a924e98169eec7ff63335c5e26ba709c8.css');
        
        // Use additional javascript file.
        $this->view->inlineScript()->appendFile('/js/api/manage-keys/ae28d30bb14a6e7cfebfa8d87a2a541b31b9cc4f.js');
    }
    
    //--------------------------------------------------------------------------
}
/* End of file ManageKeysController.php */
/* Location: api/controllers/ManageKeysController.php */