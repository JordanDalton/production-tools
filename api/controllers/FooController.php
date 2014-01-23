<?php

/**
 *  FooController
 *
 *  Description goes here..
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 3, 2012, 8:50:09 AM
 */
class FooController extends Zend_Controller_Action
{
    /**
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * @var Yadif_Container
     */
    protected $_container;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->_container   = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config      = $this->_container->getConfig();  
        $this->_db          = $this->_container->getComponent('db');
        $this->_redirector  = $this->_helper->getHelper('Redirector');
        $this->_redirector->setExit(true);

        // Do not allow timeouts
        set_time_limit(0);
    }
    
    //-------------------------------------------------------------------------- 
	
    public function indexAction()
    {     
		$model = new Rest_Model_Reports_OR1000RG($this->_db);
		
		$results = $model->getReport(array());
		
		print('<pre>');
		print_r($results);
		print('</pre>');
	
        exit;
    }
    
    //--------------------------------------------------------------------------
}
/* End of file FooController.php */
/* Location: api/controllers/FooController.php */