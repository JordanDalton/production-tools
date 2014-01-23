<?php
/**
 *  WorkOrderStatusController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 20, 2012, 10:05:52 AM
 */
class Rest_WorkOrderStatusController extends Zend_Rest_Controller
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
        /// Load the work order status model.
        $model = new My_WorkOrder_Status();
        
        // ID parameter is set
        if(isSet($this->_params['id']))
        {
            // Separate any multiple status ids set.
            $explode = explode(',', $this->_params['id']);
            
            // Create output array for results to be appended to.
            $outputArray = array();
            
            // Loop through all of the id number supplied.
            foreach($explode as $status_id)
            {
                $outputArray[(int) $status_id] = $model->id((int) $status_id)->get();
            }
            
            // Show the results.
            $this->view->data = $outputArray;
        }
        
        // ID parameter is not set.
        else {
        
            // Show all work order statuses
            $this->view->data = $model->getAll();
            
        }
        

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
/* End of file WorkOrderStatusController.php */
/* Location: api/modules/rest/controllers/WorkOrderStatusController.php */