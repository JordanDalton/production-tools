<?php
/**
 *  RestAuth
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created February 1, 2012, 10:43:56 AM
 */
class My_Controller_Plugin_RestAuth extends Zend_Controller_Plugin_Abstract
{
    protected $_requested_format = FALSE;
    
    const HEADER_API_AUTHORIZATION      = 'API-AUTHORIZATION';
    const HEADER_API_REQUESTED_FORMAT   = 'API-REQUESTED-FORMAT';
    
    //--------------------------------------------------------------------------
    
    /**
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Bail if not the rest module
        if('rest' !== $request->getModuleName()) return;
                
        // Only run restAuth during production enviornment
        switch(APPLICATION_ENV)
        {
            //------------------------------------------------------------------
            // Development Enviornment
            case 'development': break;
            //------------------------------------------------------------------
            // Production Enviornment
            default: 
                
                // Check if authorized to access API data.
                $isAuthorized = $this->restAuth($request);
                                
                // If user is not authorized, show error
                if(!$isAuthorized) $this->_redirectNoAuth($request);
                
            break;
            //------------------------------------------------------------------
        }        
    }
    
    //--------------------------------------------------------------------------

    /**
     * @param Zend_Controller_Request_Abstract $request 
     */
    protected function restAuth(Zend_Controller_Request_Abstract $request)
    {
        // Get api authorization header data
        $api_authorization = $request->getHeader(self::HEADER_API_AUTHORIZATION);
        
        // Get the requested format
        $requested_format = $request->getHeader(self::HEADER_API_REQUESTED_FORMAT)
                          ? $this->_requested_format = $request->getHeader(self::HEADER_API_REQUESTED_FORMAT)
                          : FALSE;
                
        // If api authorization header is not set...BAIL!!
        if(!$api_authorization OR empty($api_authorization)) return FALSE;
        
        /*
         * Split the authorization value into key and signature values
         * $exploded_apiKey[0] = Key
         * $exploded_apiKey[1] = Signature
         */
        $exploded_authorization = explode(':', $api_authorization);
                
        /*
         * If $exploded_apiKey does not have a count of 2, or if any of the keys
         * have no value.....BAIL!!!
         */
        if(empty($exploded_authorization[0]) 
            OR empty($exploded_authorization[1]) 
                OR count($exploded_authorization) !== 2) return FALSE;
                
        /***********************************************************************
         * Now lets consult the database to see if we have a match
         **********************************************************************/
        
        // First, load the REST Authorization Model
        $model = new Application_Model_RestAuth(); 
        
        // Query to see if a record exists based upon the key provided
        $apiKeyQuery = $model->getByKey($exploded_authorization[0]);

        // If no record exists....BAIL!!!
        if(!isSet($apiKeyQuery->key)) return FALSE;
        
        // Now Decode
        $d_apiKey       = $apiKeyQuery->key;
        $d_apiToken     = $apiKeyQuery->token;
        $d_signature    = hash_hmac('sha1', $d_apiToken, $d_apiKey);

        // Premature final checks
        if($exploded_authorization[1] != $d_signature) return false;
        if($exploded_authorization[1] != $apiKeyQuery->secret) return false;
        
        /*
         * Conduct the final check to see if we have a match.
         *  Match = TRUE;
         *  No Match = FALSE;
         */
        return ($apiKeyQuery->secret === $d_signature) ? TRUE : FALSE;
    }
    
    //--------------------------------------------------------------------------

    /**
     * Show Error Message
     */
    protected function _redirectNoAuth(Zend_Controller_Request_Abstract $request)
    {   
        $response = array();
        $response['status'] = 'failed';
        $response['response'] = array();
        $response['response']['message'] = 'Invalid API Key';
        
        // Default to json if no particular request format is made.
        if(!$this->_requested_format)
        {
            $this->getResponse()
                 ->setHttpResponseCode(403)
                 ->setheader('Content-Type', 'application/json')
                 ->appendBody(json_encode($response));
        }
        
            // Show error message.
            $request->setModuleName('default')
                    ->setControllerName('error')
                    ->setActionName('access')
                    ->setDispatched(true);  
    }
    
    //--------------------------------------------------------------------------
}
/* End of file RestAuth.php */
/* Location: library/MY/Controller/Plugin/RestAuth.php */