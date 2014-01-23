<?php
/**
 *  WorkOrderController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 27, 2012, 8:54:59 AM
 */
class Rest_WorkOrderController extends Zend_Rest_Controller
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
		//$this->view->params = (bool) $this->_params['report'];

		if(isSet($this->_params['report']))
		{
			switch($this->_params['report'])
			{		
				case 'WO1010RG': $this->_runReportWO1010RG(); break;
			}
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
    
    /**
     * Set the work order status text based upon the id number supplied.
     * 
     * @param int $id The id number for the status.
     * @return string The text name for the status.
     */
    protected function _workOrderStatus($id)
    {
		$getStatus = new My_WorkOrder_Status($id);
        
        return $getStatus->get_status_text();
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Execute WO1010RG report
     */
    protected function _runReportWO1010RG()
    {
		ini_set('memory_limit', '-1');
	
		// Output array index
		$outputArray = array();
	
        // PODET Table mobel
        $podet_model = new Rest_Model_Tables_PODET($this->_db);
        
        // Create instance of Rest_Model_Reports_WO1010RG()
        $model = new Rest_Model_Reports_WO1010RG($this->_db);
		
		// Get the results
		$results = $model->getOpenWorkOrders($this->_params);
				
		/****************
		 * Debug output	*
		 ***************\/
		print('<pre>');
		var_dump($results);
		print('</pre>');
		exit;
		/**/
		
		// Count the number of results.
		$count = count($results);
		
		// Loop through the results, appending to the $outputArray
		foreach($results as $result)
		{		
			foreach($result as $key => $value)
			{
				// Trim all keys and values.
				$result[trim($key)] = trim($value);
			}
						
			// Set the due date;
			$result['due_date'] 		= isSet($result['due_date']) 
										? preg_replace('/(\d{4})(\d{2})(\d{2})/', "$2/$3/$1", $result['due_date'])
										: null;
								  
			// Set the release date;
			$result['release_date'] 	= isSet($result['release_date']) 
										? preg_replace('/(\d{4})(\d{2})(\d{2})/', "$2/$3/$1", $result['release_date']) 
										: null;
			
			// Build Time In Hours
			$result['build_time_hours'] = (isSet($result['order_quantity']) && isSet($result['run_labor_hours']))
										? ($result['order_quantity'] * $result['run_labor_hours']) / 60
										: null;
			
			// Create a fresh output
			$modifiedResults = array();

			/********************** Set the output array **********************/
			 
            $targetOuput = array(
                'backorder_quantity',
                'build_time_hours',
                'due_date',
                'item_number',
                'item_description',
                'order_number',
                'order_status',
                'order_quantity',
                'release_date',
                'run_labor_hours',
                'um',
                'work_center',
            );
            
            foreach($targetOuput as $key => $value)
            {			
                if($value === 'order_status')
                {
                    $modifiedResults[$value] = isSet($result[$value]) 
                                             ? utf8_encode($this->_workOrderStatus($result[$value]))
                                             : '';                    
                } else {
                    
                    $modifiedResults[$value] = isSet($result[$value]) 
                                             ? utf8_encode($result[$value]) 
                                             : '';
                }
            }
			
			// Get the current U/M for the item
			$getUM = $podet_model->getUmByItemNumber($modifiedResults['item_number']);
			
			// Append UM to the $modifiedResutls array..
            $modifiedResults['um'] = $getUM->pdunms;
            
			// Unset the $result
			unset($result);
			
			// Append to the $outputArray
			$outputArray[] = $modifiedResults;
		}

		// Append the count to the ouput.
		$this->view->count = $count;
		
        // Set the view
        $this->view->data = $outputArray;
                
        // Set the response
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WorkOrderController.php */
/* Location: api/modules/rest/controllers/WorkOrderController.php */
