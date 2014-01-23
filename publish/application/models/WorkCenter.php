<?php
/**
 *  Interact with API for getting work center information.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 14, 2012, 9:40:01 AM
 */
class Application_Model_WorkCenter
{
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
	public function __construct()
	{
        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        //Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
		
		// Create instance of httpClient from the object.ini file.
        $this->_httpClient = $this->_container->httpClient;
	}

    //--------------------------------------------------------------------------
    
    /**
     * Get all the work centers from the API.
     * @param array $params
     * @return object array 
     */
	public function getWorkCenters($params = array())
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
		foreach($params as $key => $value)
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
		
		// Return array
		return $results;
	}
    
    //--------------------------------------------------------------------------
}
/* End of file WorkCenter.php */