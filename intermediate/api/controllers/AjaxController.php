<?php

/**
 *  AjaxController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 1, 2012, 1:25:38 PM
 */
class AjaxController extends Zend_Controller_Action
{
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
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->_container = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config    = $this->_container->getConfig();
        $this->_db        = $this->_container->getComponent('db');
        
        // Is user logged in?
        $this->_helper->login();
        
        $this->_httpClient = $this->_container->httpClient;
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Get all the api keys from the database
     */
    public function getApiKeysAction()
    {
        // Create index array for the final output.
        $output = array();
        
        // Load the RestAuth model
        $model = new Application_Model_RestAuth($this->_db);
        
        // Query for the records
        $results = $model->getAll();

        // Loop through the results
        foreach($results as $result)
        {
            // Conver to object array
            $result = (object) $result;
            
            // Assign the result to variables
            $_id     = $result->id       ? $result->id       : '';
            $_key    = $result->key      ? $result->key      : '';
            $_token  = $result->token    ? $result->token    : '';
            $_secret = $result->secret   ? $result->secret   : '';
            $_string = "{$_key}:{$_secret}";
            
            // Create object array for which will be appended to the output.
            $array = (object) array();
            
            // Assign data to the array;
            $array->id      = $_id;
            $array->key     = $_key;
            $array->token   = $_token;
            $array->secret  = $_secret;
            $array->string  = $_string;
            
            // Append the output to the array;
            $output[] = (array) $array;
        }
        
        // Return the output in json format
        echo json_encode($output);
    }
    
    //--------------------------------------------------------------------------

    /**
     * Create new api key record.
     */
    public function createApiKeyAction()
    {
        // Load the RestAuth model
        $model = new Application_Model_RestAuth($this->_db);
        
        // Attempt to create new authorization (API) key
        $createAuthorizationKey = $model->create();
               
        // Output the response in json format
        echo ($createAuthorizationKey) ? json_encode($createAuthorizationKey) : json_encode(false);
    }
    
    //--------------------------------------------------------------------------

    /**
     * Delete api based upon the id supplied.
     */
    public function deleteApiKeyAction()
    {
        // Get the id parameter from the uri.
        $id = $this->_getParam('id');
        
        // Load the RestAuth model
        $model = new Application_Model_RestAuth($this->_db);
        
        // Attempt to delete the record.
        $results = $model->delete($id) ? true : false;
        
        // Return the result to the controller.
        echo json_encode((array) $results);
    }
    
    //--------------------------------------------------------------------------
}
/* End of file AjaxController.php */
/* Location: api/controllers/AjaxController.php */