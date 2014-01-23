<?php
/**
 *  Rest_IndexController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 1, 2012, 4:52:32 PM
 */
class Rest_IndexController extends Zend_Rest_Controller
{
    public $contexts = array
    (
        'index'  => array('json'),
        'get'    => array('json'),
        'post'   => array('json'),
        'put'    => array('json'),
        'delete' => array('json'),
    );

    private $_params;
    
    //--------------------------------------------------------------------------
    
    public function init()
    {
        $options = $this->getInvokeArg('bootstrap');
        
        // Get the parameters from the URI
        $this->_params = $this->_request->getParams();
        
        // Setup the contexts
        $this->_helper->contextSwitch()->initContext();
        
        if(!isSet($this->_params['format']))
        {
            $this->_helper->contextSwitch()->initContext('json');
        }

        // Set default returns
        $this->view->status     = "success";
		$this->view->version    = "1.0";
    }
    
    //--------------------------------------------------------------------------
    
    public function indexAction()
    {
        // Set the response
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    //--------------------------------------------------------------------------
    
    public function getAction()
    {       
        $this->_forward('index');
    }
    
    //--------------------------------------------------------------------------
    
    public function postAction()
    {
        $this->_forward('index');
    }
    
    //--------------------------------------------------------------------------
    
    public function putAction()
    {
        $this->_forward('index');
    }
    
    //--------------------------------------------------------------------------
    
    public function deleteAction()
    {
        $this->_forward('index');
    }
    
    //--------------------------------------------------------------------------
}
/* End of file IndexController.php */
/* Location: api/modules/rest/controllers/IndexController.php */