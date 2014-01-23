<?php
/**
 *  AjaxController
 *
 *  Description goes here..
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 25, 2012, 9:19:57 AM
 */
class AjaxController extends Zend_Controller_Action
{
    /**
     * @var Boolean Only respond to ajax requests?
     */
    protected $_ajaxOnly = FALSE;
    /**
     * @var Zend_Config
     */
    protected $_config;
    /**
     * @var Yadif_Container
     */
    protected $_container;
    /**
     * @var Zend_Http_Client
     */
    protected $_httpClient;
	/**
	 * @mixed The URI parameters
	 */
    private $_params;
        
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {       
        $this->_container   = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config      = $this->_container->getConfig();
        $this->_db        = $this->_container->getComponent('db');
        
        $this->_redirector  = $this->_helper->getHelper('Redirector');
        $this->_redirector->setExit(true);
        
        // Get the parameters from the URI
        $this->_params = $this->_request->getParams();
		
		// Create instance of httpClient from the object.ini file.
        $this->_httpClient = $this->_container->httpClient;
        
        // Timeout after 5 minutes
        set_time_limit(300);
		
        // Disable the layout
        $this->_helper->layout->disableLayout();
        
        // Do not render
        $this->_helper->viewRenderer->setNoRender(true);
        
        // Disable ajax only during development enviornment.
        //$this->_ajaxOnly = (APPLICATION_ENV === 'development') ? FALSE : TRUE;

        /* Only allow ajax reqests if $_ajaxOnly is set to TRUE */
        if($this->_ajaxOnly && !$this->_request->isXmlHttpRequest())
        {
            exit('Only Ajax Requests Allowed');   
        }
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
     * Request the report from the REST server.
     * 
     *  Example: http://productiontoolsv2/ajax/get-report?run=WO1010RG&work-center=43
     *       Or: http://productiontoolsv2/ajax/get-report/run/WO1010RG/work-center=43
     * 
     *  Runs As: http://api.productiontoolsv2/rest/report/run/WO1010RG/work-center/43
     * 
     */
    public function getReportAction()
    {
        // Create Zend_Rest_Client Object
        $restClient = new Zend_Rest_Client($this->_config->api->url);
		
        // Get the Zend_Http_Client information from the container.
        $restClient->setHttpClient($this->_httpClient);
		
		// Params to ignore.
		$ignoreParams = array('controller', 'action', 'module');
		
		// Create index array for parameters that we plan to use.
		$paramsToAppendArray = array();
		
		// Loop through the params passed.
		foreach($this->_params as $key => $value)
		{		
            if(!in_array($key, $ignoreParams))
            {			
				$paramsToAppendArray[] = "{$key}/{$value}";
            }
		}
		
		// Prepare to append the request uri.
		$paramsToAppend = (count($paramsToAppendArray) >= 1) ? implode('/', $paramsToAppendArray) : null;
        
		// Execute GET request.
        $restResult = $restClient->restGet('rest/report/' . $paramsToAppend);
		
        // Send GET Request
        $results = Zend_Json::decode($restResult->getBody(), Zend_Json::TYPE_OBJECT); 

        // Set the response
        $this->getResponse()
             ->setHttpResponseCode(200)
             ->setheader('Content-Type', 'application/json')
             ->appendBody(json_encode($results));   
    }
    
    
    //--------------------------------------------------------------------------

    /**
     * Request work center(s) from the REST server.
     * 
     *  Example: http://productiontoolsv2/ajax/get-work-center?number=43
     *       Or: http://productiontoolsv2/ajax/get-work-center/number=43
     * 
     *  Runs As: http://api.productiontoolsv2/rest/work-center/number/43
     * 
     */
    public function getWorkCenterAction()
    {
        // Create Zend_Rest_Client Object
        $restClient = new Zend_Rest_Client($this->_config->api->url);
		
        // Get the Zend_Http_Client information from the container.
        $restClient->setHttpClient($this->_httpClient);
		
		// Params to ignore.
		$ignoreParams = array('controller', 'action', 'module');
		
		// Create index array for parameters that we plan to use.
		$paramsToAppendArray = array();
		
		// Loop through the params passed.
		foreach($this->_params as $key => $value)
		{		
            if(!in_array($key, $ignoreParams))
            {			
				$paramsToAppendArray[] = "{$key}/{$value}";
            }
		}
		
		// Prepare to append the request uri.
		$paramsToAppend = (count($paramsToAppendArray) >= 1) ? implode('/', $paramsToAppendArray) : null;
        
		// Execute GET request.
        $restResult = $restClient->restGet('rest/work-center/' . $paramsToAppend);
		
        // Send GET Request
        $results = Zend_Json::decode($restResult->getBody(), Zend_Json::TYPE_OBJECT); 

        // Set the response
        $this->getResponse()
             ->setHttpResponseCode(200)
             ->setheader('Content-Type', 'application/json')
             ->appendBody(json_encode($results));   
    }
    
    //--------------------------------------------------------------------------

    /**
     * Request work center group(s) from the REST server.
     * 
     *  Example: http://productiontoolsv2/ajax/get-work-center-group?id=a
     *       Or: http://productiontoolsv2/ajax/get-work-center-group/id=a
     * 
     *  Runs As: http://api.productiontoolsv2/rest/work-center-group/id/a
     * 
     */
    public function getWorkCenterGroupAction()
    {       
        // Create Zend_Rest_Client Object
        $restClient = new Zend_Rest_Client($this->_config->api->url);
		
        // Get the Zend_Http_Client information from the container.
        $restClient->setHttpClient($this->_httpClient);
		
		// Params to ignore.
		$ignoreParams = array('controller', 'action', 'module');
		
		// Create index array for parameters that we plan to use.
		$paramsToAppendArray = array();
		
		// Loop through the params passed.
		foreach($this->_params as $key => $value)
		{		
            if(!in_array($key, $ignoreParams))
            {			
                $value = urlencode($value);
                
				$paramsToAppendArray[] = "{$key}/{$value}";
            }
		}
        
		// Prepare to append the request uri.
		$paramsToAppend = (count($paramsToAppendArray) >= 1) ? implode('/', $paramsToAppendArray) : null;
                
		// Execute GET request.
        $restResult = $restClient->restGet('rest/work-center-group/' . $paramsToAppend);
		
        // Send GET Request
        $results = Zend_Json::decode($restResult->getBody(), Zend_Json::TYPE_OBJECT); 

        // Set the response
        $this->getResponse()
             ->setHttpResponseCode(200)
             ->setheader('Content-Type', 'application/json')
             ->appendBody(json_encode($results));   
    }
    
    //--------------------------------------------------------------------------

    /**
     * Request work order from the REST server.
     * 
     *  Example: http://productiontoolsv2/ajax/get-work-order?order-number=Y0751
     *       Or: http://productiontoolsv2/ajax/get-work-order/order-number=Y0751
     * 
     *  Runs As: http://api.productiontoolsv2/rest/work-order/order-number/Y0751
     */
    public function getWorkOrderAction()
    {	
        // Create Zend_Rest_Client Object
        $restClient = new Zend_Rest_Client($this->_config->api->url);
		
        // Get the Zend_Http_Client information from the container.
        $restClient->setHttpClient($this->_httpClient);
		
		// Params to ignore.
		$ignoreParams = array('controller', 'action', 'module');
		
		// Create index array for parameters that we plan to use.
		$paramsToAppendArray = array();
		
		// Loop through the params passed.
		foreach($this->_params as $key => $value)
		{
            if(!in_array($key, $ignoreParams))
            {			
				$paramsToAppendArray[] = "{$key}/{$value}";
            }
		}
		
		// Prepare to append the request uri.
		$paramsToAppend = (count($paramsToAppendArray) >= 1) ? implode('/', $paramsToAppendArray) : null;
				
        var_dump('rest/report/' . $paramsToAppend);
        exit;
        
		// Execute GET request.
        $restResult = $restClient->restGet('rest/work-order/' . $paramsToAppend);
		
        // Send GET Request
        $results = Zend_Json::decode($restResult->getBody(), Zend_Json::TYPE_OBJECT); 

        // Set the response
        $this->getResponse()
             ->setHttpResponseCode(200)
             ->setheader('Content-Type', 'application/json')
             ->appendBody(json_encode($results));   
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Post work center group to the REST server.
     * 
     *  Example: http://productiontoolsv2/ajax/put-work-center-group?work-center=43
     *       Or: http://productiontoolsv2/ajax/put-work-center-group/work-center=43
     * 
     *  Runs As: http://api.productiontoolsv2/rest/work-center-group/work-center/43
     * 
     */
    public function putWorkCenterGroupAction()
    {
        // Create Zend_Rest_Client Object
        $restClient = new Zend_Rest_Client($this->_config->api->url);
		
        // Get the Zend_Http_Client information from the container.
        $restClient->setHttpClient($this->_httpClient);
		
		// Params to ignore.
		$ignoreParams = array('controller', 'action', 'module');
		
		// Create index array for parameters that we plan to use.
		$paramsToAppendArray = array();
		
		// Loop through the params passed.
		foreach($this->_params as $key => $value)
		{		
            if(!in_array($key, $ignoreParams))
            {			
                // Encode so that we don't recieve any invalid http path errors.
                $value = urlencode($value);
                
				$paramsToAppendArray[] = "{$key}/{$value}";
            }
		}
        
		// Prepare to append the request uri.
		$paramsToAppend = (count($paramsToAppendArray) >= 1) ? implode('/', $paramsToAppendArray) : null;
        
		// Execute GET request.
        $restResult = $restClient->restPut('rest/work-center-group/' . $paramsToAppend);
		
        // Send GET Request
        $results = Zend_Json::decode($restResult->getBody(), Zend_Json::TYPE_OBJECT); 

        // Set the response
        $this->getResponse()
             ->setHttpResponseCode(200)
             ->setheader('Content-Type', 'application/json')
             ->appendBody(json_encode($results));   
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Delete work center group(s) from the REST server.
     * 
     *  Example: http://productiontoolsv2/ajax/delete-work-center-group?id=a
     *       Or: http://productiontoolsv2/ajax/delete-work-center-group/id=a
     * 
     *  Runs As: http://api.productiontoolsv2/rest/work-center-group/id/a
     * 
     */
    public function deleteWorkCenterGroupAction()
    {
        // Create Zend_Rest_Client Object
        $restClient = new Zend_Rest_Client($this->_config->api->url);
		
        // Get the Zend_Http_Client information from the container.
        $restClient->setHttpClient($this->_httpClient);
		
		// Params to ignore.
		$ignoreParams = array('controller', 'action', 'module');
		
		// Create index array for parameters that we plan to use.
		$paramsToAppendArray = array();
		
		// Loop through the params passed.
		foreach($this->_params as $key => $value)
		{		
            if(!in_array($key, $ignoreParams))
            {			
				$paramsToAppendArray[] = "{$key}/{$value}";
            }
		}
		
		// Prepare to append the request uri.
		$paramsToAppend = (count($paramsToAppendArray) >= 1) ? implode('/', $paramsToAppendArray) : null;
        
		// Execute GET request.
        $restResult = $restClient->restDelete('rest/work-center-group/' . $paramsToAppend);
		
        // Send GET Request
        $results = Zend_Json::decode($restResult->getBody(), Zend_Json::TYPE_OBJECT); 

        // Set the response
        $this->getResponse()
             ->setHttpResponseCode(200)
             ->setheader('Content-Type', 'application/json')
             ->appendBody(json_encode($results));   
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Save a work order table report.
     */
    public function saveWorkOrderTableReportAction()
    {        
        // Load the work order report model
        $model = new Application_Model_WorkOrderReport($this->_db);

        // Create index array for the headers
        $headers = array();
        
        // Create index array for the rows
        $rows = array();

        /* DEBUG OUT *\/
        $myFile = APPLICATION_PATH . "../jsonTest.txt";
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, $this->_params['postData']);
        fclose($fh);
        exit;
        /**/

        // Set the report name
        $report = $this->_params['report'];
        
        /**\/
        
        //print(urldecode($this->_params['postData']));
        //exit;
        
        $decoded = Zend_Json::decode('[{"Item #":"43135","Item Description":"Food Choices: Take Your Pick Interactive Flip Chart","B/O Qty":"0.00","B/O Value":"$734.20","Status":"Release Ready","Co/Wo No.":"Y5543","Order Qty":"20.00"}]', Zend_Json::TYPE_OBJECT);
        /**/        
        
        // Decode the post data
        $decoded = Zend_Json::decode(urldecode($this->_params['postData']), Zend_Json::TYPE_OBJECT);
        
        // Set the misc data
        $misc = array(
            'bo_value'           => $this->_params['bo_value'],
            'record_count'       => $this->_params['record_count'],
            'work_centers'       => (strlen($this->_params['work_centers']) >= 1) ? $this->_params['work_centers'] : null,
            'work_center_groups' => (strlen($this->_params['work_center_groups']) >= 1) ? $this->_params['work_center_groups'] : null
        );
        
        // Loop through the array.
        foreach($decoded as $decode)
        {
            //$stripOpeningBracket = preg_replace('/(^\[)/', '$replacement', $subject);
            
            // Create index array to store row data to.
            $rowData = array();
            
            foreach($decode as $key => $value)
            {
                // Trim the $key
                $key = trim($key);
                
                // Trim the $value
                $value = trim($value);
                
                /* 
                 * If the $key does not currently exists in our list of headers
                 * then add it.
                 */
                if(!in_array($key, $headers)) $headers[] = $key;
                                                
                // Create key and values for each column of data.
                $rowData[$key] = addcslashes($value, '\"');
            }

            // Append our $rowData to the $rows array
            $rows[] = $rowData;
        }
               
        // Now lets save the data
        $save = $model->saveTableReport($report, $headers, $rows, $misc);
        
        print_r($save ? $save : 0);
        
        /*
        $decode = json_decode($this->_params['postData']);
        $encode = json_encode($decode);
        
        echo $encode;
         * 
         */
    }
    
    //--------------------------------------------------------------------------
    
    public function testSelectAction()
    {
        // Load the work order report model
        $model = new Application_Model_WorkOrderReport($this->_db);
        
        $results = $model->getTableReport($this->_params['id']);
        
        // Do we have final results? Default to no (false).
        $finalResults = false;
        
        // If results exist, then continue to the next step
        if($results)
        {
            // Loop through the data
            foreach($results as $result)
            {
                $result = (object) $result;
                $result->headings = json_decode($result->headings);
                $result->rows     = json_decode($result->rows);
                
                $finalResults[] = $result;
            }
        }

        // Set the response
        $this->getResponse()
             ->setHttpResponseCode(200)
             ->setheader('Content-Type', 'application/json')
             ->appendBody(json_encode($finalResults));   
    }
    
    //--------------------------------------------------------------------------
    
    public function testGetAction()
    {
        $fgc = file_get_contents(APPLICATION_PATH . "../jsonTest.txt");
        
        $decode = Zend_Json::decode($fgc, Zend_Json::TYPE_OBJECT);
        
        print('<pre>');
        print_r($decode);
        print('</pre>');
    }
}
/* End of file AjaxController.php */
/* Location: application/controllers/AjaxController.php */