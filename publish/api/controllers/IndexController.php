<?php
/**
 * Index controller
 * 
 * @author Jordan Dalton <jordandalton@wrsgroup.com>
 */
class IndexController extends Zend_Controller_Action
{
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
        // Is user logged in?
        $this->_helper->login();
        
        $this->_redirector      = $this->_helper->getHelper('Redirector');
        $this->_redirector->setExit(true);
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
     * Main Startpage
     */
    public function indexAction()
    {        
        // FORWARD TO DASHBOARD
        $this->_redirector->gotoSimple('index', 'dashboard', '');
    }
    
    //--------------------------------------------------------------------------
}
/* End of file IndexController.php */
/* Location: api/controllers/IndexController.php */