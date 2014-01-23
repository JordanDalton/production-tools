<?php
/**
 *  WorkCenterGroup
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 13, 2012, 3:02:46 PM
 */
class Rest_WorkCenterGroupController extends Zend_Rest_Controller
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
		/**/
        // Load the Work Center Group Model
        $model = new Rest_Model_Tables_WorkCenterGroup($this->_db);

        $this->view->data = $model->getAll($this->_config->defaultWarehouseId, $this->_params);
        
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
        // Load the Work Center Group Model
        $model = new Rest_Model_Tables_WorkCenterGroup($this->_db);
        
        $successful = $model->createWorkCenterGroup($this->_params, $this->_config->defaultWarehouseId);
		
		$this->view->data   = $successful;
        $this->view->status = $successful ? "success" : 'failed';

        // Set the response
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Delete a work center with a specified group id/letter
     */
    public function deleteAction()
    {
        // If group(s) are specified, then proceed.
        if(isSet($this->_params['group-id']))
        {
            // Load the Work Center Group Model
            $model = new Rest_Model_Tables_WorkCenterGroup($this->_db);
            
			// Delete Successful
			$deleteSuccessful = TRUE;
			
			// Get the group id/letters
			$getGroups = explode(',', $this->_params['group-id']);
			
			foreach($getGroups as $group)
			{
				if(!$model->deleteWorkCenterGroup($group)) $deleteSuccessful = FALSE;
			}
			
            // Delete was successful
            if($deleteSuccessful)
            {
                $this->view->message    = "Group(s) successfully deleted.";
            } 
            
            // Failed to delete the work center group(s)
            else {
                
                $this->view->status     = "failed";
                $this->view->message    = "There was an error deleting the work center group(s).";    
            }
        }
        
        // No group specified, bail!!
        else {
            
            $this->view->status     = "failed";
            $this->view->message    = "Group id(s) must be specified.";
            
        }
        
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WorkCenterGroup.php */
/* Location: api/modules/rest/controllers/WorkCenterGroup.php */