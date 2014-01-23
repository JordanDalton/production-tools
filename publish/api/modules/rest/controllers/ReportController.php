<?php
/**
 *  REST Report Controller
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 8, 2012, 8:10:02 AM
 */
class Rest_ReportController extends Zend_Rest_Controller
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
        //if(APPLICATION_ENV === 'development')
        if(false)
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
        $options = $this->getInvokeArg('bootstrap');
        
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
        /* Debug the params passed *\/
        $this->view->params = $this->_params;
        /**/
        
        // First check that a report parameter is being passed @ the URI.
		if(isSet($this->_params['run']))
		{
            // If it is, then lets see which report, if supported, is being requested.
			switch($this->_params['run'])
			{
                /**************************************************************/
                // AS 400 WO1010RG Report
				case 'WO1010RG':
				case 'wo1010rg':
				
                    // Execute the report
                    $this->_runReportWO1010RG(); 
                    $this->view->report = 'WO1010RG';
                    
                break;
                break;
                /**************************************************************/
                // AS 400 WO1002RG Report
				case 'WO1002RG':
				case 'wo1002rg':

                    // Execute the report
                    $this->_runReportWO1002RG(); 
                    $this->view->report = 'WO1002RG';
                    
                break;
                break;
                /**************************************************************/
                // AS 400 OR1000RG Report
				case 'OR1000RG':
				case 'or1000rg':

                    // Execute the report
                    $this->_runReportOR1000RG();
                    $this->view->report = 'OR1000RG';
                    
                break;
                break;
                /**************************************************************/
                // Default to failure.
                default:    $this->view->status = "failed"; break;
                /**************************************************************/
            
			} // end switch($this->_params['run'])
            
		}
        
        else {
            $this->view->status = "failed";
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
	
        // Load the ORDET Table Model
        $ordet_model = new Rest_Model_Tables_ORDET($this->_db);
	
        // PODET Table mobel
        $podet_model = new Rest_Model_Tables_PODET($this->_db);
        
        // Create instance of Rest_Model_Reports_WO1010RG()
        $model = new Rest_Model_Reports_WO1010RG($this->_db);
		
		// Get the results
		$results = $model->getReport($this->_params);
				
		/****************
		 * Debug output	*
		 ***************\/
		print('<pre>');
		var_dump($results);
		print('</pre>');
		exit;
		/**/
		
		// Set the starting counter;
		$counter = 0;
				
		// Loop through the results, appending to the $outputArray
		foreach($results as $result)
		{		
			foreach($result as $key => $value)
			{
				// Trim all keys and values.
				$result[trim($key)] = trim($value);
			}
						
			// Set the due date;
			$result['entry_date'] 		= isSet($result['entry_date']) 
										? preg_replace('/(\d{4})(\d{2})(\d{2})/', "$2/$3/$1", $result['entry_date'])
										: null;
						
			// Set the due date;
			$result['due_date'] 		= isSet($result['due_date']) 
										? preg_replace('/(\d{4})(\d{2})(\d{2})/', "$2/$3/$1", $result['due_date'])
										: null;
										
			// Set the release date;
			$result['release_date'] 	= isSet($result['release_date']) 
										? preg_replace('/(\d{4})(\d{2})(\d{2})/', "$2/$3/$1", $result['release_date']) 
										: null;
										
            // Query for the back order value
            $getBackOrderValue = $ordet_model->getBackOrderValueByItemNumber($result['item_number']);
						
            // Get the back order value
            $result['back_order_value'] = $getBackOrderValue[0]['back_order_value'] ? $getBackOrderValue[0]['back_order_value'] : 0;
			
			// Create a fresh output
			$modifiedResults = array();

			/********************** Set the output array **********************/
			 
            $targetOuput = array(
                'back_order_quantity',
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
				'order_age',
				'back_order_value',
                'entry_date'
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
			
			// Increment the counter;
			$counter++;
		}

		// Append the count to the ouput.
		$this->view->count = $counter;
		
        // Set the view
        $this->view->data = $outputArray;
                
        // Set the response
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Execute WO1002RG report
     */
    protected function _runReportWO1002RG()
    {
		ini_set('memory_limit', '-1');
	
		// Output array index
		$outputArray = array();
        
        // Load the ORDET Table Model
        $ordet_model = new Rest_Model_Tables_ORDET($this->_db);
        
        // Load the WO1002RG Report Model
        $model = new Rest_Model_Reports_WO1002RG($this->_db);
        
        // Get the results
        $results = $model->getReport($this->_params);
        
		/****************
		 * Debug output	*
		 ***************\/
		print('<pre>');
		var_dump($results);
		print('</pre>');
		exit;
		/**/
        
		// Loop through the results, appending to the $outputArray
        foreach($results as $result)
        {
			foreach($result as $key => $value)
			{
				// Trim all keys and values.
				$result[trim($key)] = trim(utf8_encode($value));
			}
            
			// Create a fresh output
			$modifiedResults = array();
            
			/********************** Set the output array **********************/
			 
            $targetOuput = array(
                'item_number',
                'item_description',
                'back_order_quantity',
                'back_order_value',
                'status',
                'order_number',
                'order_quantity',
            );
            
            
            foreach($targetOuput as $key => $value)
            {			
                if($value === 'status')
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
            
            
            $outputArray[] = $modifiedResults;
        }
        
		// Count the number of results.
		$count = count($results);
        
		// Append the count to the ouput.
		$this->view->count = $count;
		
        // Set the view
        $this->view->data = $outputArray;
                
        // Set the response
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Execute OR1000RG report
     */
    protected function _runReportOR1000RG()
    {
		ini_set('memory_limit', '-1');
	
		// Output array index
		$outputArray = array();
        
		// Load the OR1000RG Report Model
		$model = new Rest_Model_Reports_OR1000RG($this->_db);
		
        // Get the results
        $results = $model->getReport($this->_params);
		
        // Set the view
        $this->view->data = $results;//$outputArray;
                
        // Set the response
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    //--------------------------------------------------------------------------
}
/* End of file ReportController.php */
/* Location: api/modules/rest/controllers/ReportController.php */