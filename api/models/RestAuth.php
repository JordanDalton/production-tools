<?php
/**
 *  RestAuth
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 27, 2012, 10:33:31 AM
 *  @see AS/400 DB Schema   ..\schemas\AS400\pt_au.sql
 *  @see MySQL  DB Schema   ..\schemas\MySQL\api_users.sql
 */
class Application_Model_RestAuth
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    /**
     * @var string DB Table Name
     */
    protected $_name;
	/**
     * @var array The columns for the database table.
     */
    protected $_columns;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function __construct($db = false)
    {        
        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        //Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
     
        // Retrieve the db adapter and assign it.
        $this->_db = $db ? $db : $this->_container->getComponent('db');;
        
        // Table names differ depending upon the enviornment
        $this->_name = (APPLICATION_ENV === 'development') ? 'api_users' : 'pt_au';
        
        // Columns Array
        $columns= array('id','key','token', 'secret');
        
        // Loop through the columns
        foreach($columns as $key => $value)
        {
            // Add them to the $_columns array.
            $this->_columns[strtolower($value)] = $value;
        }
        
        // Now update the $_columns to be an object array.
        $this->_columns = (object) $this->_columns;
    }

    //--------------------------------------------------------------------------

    /**
     * Query for all the api keys.
     * @return array
     */
    public function getAll()
    {
        return $this->_db
                    ->select()
                    ->from($this->_name)
                    ->query()
                    ->fetchAll();
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Query for record based up the api key supplied.
     * @param string $apiKey The api key of the user.
     * @return object 
     */
    public function getByKey($apiKey)
    {
        return $this->_db
                    ->select()
                    ->from($this->_name)
                    ->where("{$this->_name}.{$this->_columns->key} = ?", $apiKey)
                    ->query()
                    ->fetchObject();
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Generate a new api user record in the database.
     * 
     * @return INT|Boolean Integer on success, False boolean on failure.
     */
    public function create()
    {
        $first10    = $this->rand_sha1(10); // First 10 characters prior to the hyphen
        $last10     = $this->rand_sha1(10); // Last 10 characters post-hyphen
        $token      = $this->rand_sha1(40); // 40 character long token
        
        // Set the API key
        $apiKey     = "{$first10}-{$last10}";
        
        // Set the API token
        $apiToken   = $token;
        
        // Set the API secret
        $apiSecret     = hash_hmac('sha1', $apiToken, $apiKey);
        
        // Set the api signature
        $signature = "{$apiKey}:{$apiSecret}";    
        
        /***********************************************************************
         * Now let prepare the data for insertion into the database.
         **********************************************************************/
        
		$inputArray = array();
		$inputArray[(APPLICATION_ENV === 'development') ? $this->_db->quoteIdentifier($this->_columns->key) : $this->_columns->key] = $apiKey;
		$inputArray[$this->_columns->token] = $apiToken;
		$inputArray[$this->_columns->secret] = $apiSecret;
        
        // By default set $execute to true, which may be set to false if there is an error
        $execute = true;
        
        try {
            
            // Attempt to submit the new record into the database.
            $this->_db->insert($this->_name, $inputArray);
            
        } catch(Zend_Db_Exception $e) {
            
            // Log the failure to the error log.
            $logger = $this->_container->getComponent('logger');        
            $logger->info("*************** BEING ERROR ***************");
            $logger->info("Class: "    . get_class($this));
            $logger->info("Method: create()");
            $logger->info("Message: " . $e->getMessage());
            $logger->info("***************  END ERROR  ***************");
            $logger->info("===========================================");
            
            $execute = false;
        }

        return ($execute) ? $signature : FALSE;
    }
    
    //--------------------------------------------------------------------------
    
    public function read(){}
    
    //--------------------------------------------------------------------------
    
    public function update(){}
    
    //--------------------------------------------------------------------------
    
    /**
     * Delete api key record.
     * 
     * @param int $id The target record id.
     */
    public function delete($id)
    {        
        return $this->_db->delete($this->_name, $this->_db->quoteInto("id = ?", $id)) ? TRUE : FALSE;     
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Create random hashed strings.
     * 
     * @param int $length
     * @return string
     */
    protected function rand_sha1($length)
    {
        $max = ceil($length / 40);
        $random = '';
        
        for ($i = 0; $i < $max; $i ++)
        {
            $random .= sha1(microtime(true).mt_rand(10000,90000));
        }
        
        return substr($random, 0, $length);
    }

    //--------------------------------------------------------------------------
}
/* End of file RestAuth.php */
/* Location: api/models/RestAuth.php */