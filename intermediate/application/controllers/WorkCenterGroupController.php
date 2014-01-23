<?php
/**
 *  WorkOrderController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 10, 2012, 8:36:49 AM
 */
class WorkCenterGroupController extends Zend_Controller_Action
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
        $this->_container = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config    = $this->_container->getConfig();
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
     * Work Order Dashboard
     */
    public function indexAction()
    {   
        // Load the Work Center Group > New Entry form..
        $form = new Application_Form_WorkCenterGroup_Add();
        
        // Set the page title
        $this->_pageTitle = 'Work Center Groups';
        
        // Append Stylesheet
        $this->view->headLink()->appendStylesheet('/css/gui/work-center-group/c96f063831595490cae4f4d349067a731582c07f.css', 'all');
        
        // Append Javascript File.
        $this->view->inlineScript()->appendFile('/js/gui/work-center-group/c184db06263a9b6e05ea5b21241b22e5860b6df1.js');
        
        // Pass the form to view
        $this->view->form = $form;
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WorkOrderController.php */
/* Location: application/controllers/WorkOrderController.php */