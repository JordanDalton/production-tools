<?php
/**
 *  WorkCenter
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 13, 2012, 3:02:46 PM
 */
class Rest_WorkCenterController extends Zend_Rest_Controller
{
    /**
     * @var array The context for each action.
     */
    public $contexts = array
    (
        'index'  => array('json'),
        'get'    => array('json'),
        'post'   => array('json'),
        'put'    => array('json'),
        'delete' => array('json'),
    );
    /**
     * @var mixed The params passed at the URI
     */
    private $_params;
    
    //--------------------------------------------------------------------------

    /*
     * Since this controller requires use of A/S 400, it can only be accessible 
     * during production mode. With that said, lets check before we do anything else.
     */
    public function preDispatch()
    {
		// If not in production mode then bail the script and show user special message.
        if(APPLICATION_ENV === 'development')
        {
            $array = (object) array();
            $array->status  = 'failed';
            $array->version = '1.0';
            $array->message = 'Sorry, this controller is only accessible during production mode.';

            $this->_helper->json((array) $array);   
        }
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {	
        $this->_container = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config    = $this->_container->getConfig();
        $this->_db        = $this->_container->getComponent('db');
        
        // Get the parameters from the URI
        $this->_params = $this->_request->getParams();
        
        // Do not allow timeouts
        set_time_limit(0);
		
        // Setup the contexts
        $this->_helper->contextSwitch()->initContext();
        
        // If no format set, default to json
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
        // Load the work center model
        $model = new Rest_Model_Tables_WOWCM($this->_db);
        
        $this->view->data = $model->getAll();
        
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
/* End of file WorkCenter.php */
/* Location: api/modules/rest/controllers/WorkCenter.php */