<?php

/**
 *  Client
 *
 *  Description goes here..
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Mar 2, 2012, 8:27:51 AM
 */
class My_Rest_Client
{
    /**
     * Zend_Http_Client for this request.
     * @var Zend_Http_Client
     */
    protected $_httpClient;
    
     /**
     * Zend_Uri of this web service
     * @var Zend_Uri_Http
     */
    protected $_uri = null;
    
    /**
     * The allowed request types
     * @var type 
     */
    protected $_allowed_request_types = array('get', 'post', 'put', 'delete');
    
    //--------------------------------------------------------------------------

    /**
     * Execute prior to completing the constructor.
     */
    public function preDispatch()
    {
        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        // Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();        
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     *
     * @param string|Zend_Uri_Http $uri URI for the web service
     * @return void
     */
    public function __construct($uri = null, $http_client = null)
    {
        // Execute preDispatch
        $this->preDispatch();
        
        // Check if uri is set or has a value
        if (!$uri OR !empty($uri)) {
            $this->setUri($this->_config->api->url);
        }
        
        // Check if http_client is set or has a value
        if (!$http_client OR !empty($http_client)){    
            $this->setHttpClient($this->_container->httpClient);
        }
    }
    
    //--------------------------------------------------------------------------

    /**
     * Set the URI to use in the request
     *
     * @param string|Zend_Uri_Http $uri URI for the web service
     * @return My_Rest_Client
     */
    public function setUri($uri)
    {
        $this->_uri = $uri;

        return $this;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Retrieve the current request URI object
     *
     * @return Zend_Uri_Http
     */
    public function getUri()
    {
        return $this->_uri;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the HTTP CLIENT to use in the request
     *
     * @param array|Zend_Http_Client $httpClient HTTP CLIENT for the web service
     * @return My_Rest_Client
     */
    public function setHttpClient($httpClient)
    {
        $this->_httpClient = $httpClient;

        return $this;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Retrieve the current request URI object
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Execute our REST CLIENT
     * @param string $request_type GET, POST, PUT, or DELETE
     * @param type $target
     * @param type $params 
     */
    public function execute($request_type = null, $target = null, $params = array())
    {                
        $request_type = !empty($request_type) ? 'get' : strtolower($request_type);
                     
        try {
            
            if(!in_array($request_type, $this->_allowed_request_types))
            {
                $exceptionMessage = '"<b>'. $request_type . '</b>" is an invalid request type. The following are only allowed: ' . implode(', ', $this->_allowed_request_types) . '.';
                
                throw new Exception($exceptionMessage);
            }

        } catch (Exception $exc) {
            
            return $exc->getMessage();
        }
        
        // If target is empty, set defaut path.
        if(empty($target)) $target = 'rest/';
        
        // Create Zend_Rest_Client Object
        $restClient = new Zend_Rest_Client($this->getUri());
		
        // Get the Zend_Http_Client information from the container.
        $restClient->setHttpClient($this->getHttpClient());
		
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
                
        // Prepare request string
        $setString = $target . $paramsToAppend;
        
        // Prepare the method
        $method = '';
        
        switch($request_type)
        {
            /******************************************************************/
            case 'get':    $method = $restClient->restGet($setString);    break;
            /******************************************************************/
            case 'post':   $method = $restClient->restPost($setString);   break;
            /******************************************************************/
            case 'put':    $method = $restClient->restPut($setString);    break;
            /******************************************************************/
            case 'delete': $method = $restClient->restDelete($setString); break;
            /******************************************************************/
        }
       
		// Execute request.
        $restResult = $restClient->restGet($setString);

        // Return the body of the result.
        return $restResult->getBody();  
    }
    
}
/* End of file Client.php */