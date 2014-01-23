<?php
/**
 *  Interface with the ORDET table inside AS/400.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 7, 2012, 2:41:12 PM
 */
class Rest_Model_Tables_ORDET
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
	 * @var mixed
	 */
	protected $_primary;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function __construct($db)
    {
        // Retrieve the db adapter and assign it.
        $this->_db = $db;
        
        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        //Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
               
        // Table names differ depending upon the enviornment
        $this->_name = (APPLICATION_ENV === 'development') ? new Exception('Not Supported') : 'ordet';
                
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('odcono','odorno', 'odorgn', 'odorsq');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Get the material short items based upon work order that has a status
     * of 9 (material short) and backorder quantities != 0
     * 
     * @param string $item_number The item number we're requesting data for.
     * @return object
     */
    public function getMSItemsByWON($work_order_number)
    {
        $select =  $this->_db->select()
                        //->setIntegrityCheck(false)
                        ->from($this->_name)
                        ->where( 'odorno = ?' , $work_order_number)
                        ->where( 'odorst = ?' , 9)
                        ->where('odboqt <> ?' , 0);
        
        // Execute the query
        $results = $select->query()->fetchObject();
        
        // retun the results to the controller.
        return $results;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Get the back order value for a particular item number.
     * @param string $item_number The target item number.
     */
    public function getBackOrderValueByItemNumber($item_number)
    {
        $select =  $this->_db->select()
                        //->setIntegrityCheck(false)
                        ->from($this->_name, array(
                           'back_order_value' => 'SUM(odlnvl)'
                        ))
                        ->where('ordet.oditno = ?', $item_number);
		
        return $select->query()->fetchAll();
    }
    
    //--------------------------------------------------------------------------
	
    /**
     * Retrieve items based upon the work order number for the OR1000RG report.
     * 
     * @param string $orderNumber
     * @return array;
     */
    public function getItemsByOrderNumber($orderNumber)
    {	
        $select =  $this->_db
						->select()
                        ->from($this->_name,array(
							'item_number'			=> 'trim(oditno)',
							'item_description' 		=> 'trim(CONCAT(oditd1, oditd2))',
							'back_order_quantity' 	=> 'odboqt',
							'quantity_ordered'		=> 'odqtor',
                            'quantity_shipped'		=> 'odqtsh',
							'line_item_type' 		=> 'odlitp',
							'list_price' 			=> 'odlspr',
							'order_total'			=> '(odlspr * odqtor)',
							'ship_total'			=> '(odlspr * odqtsh)',
							'back_order_total'		=> '(odlspr * odboqt)',
						))
						->joinLeft(
							'itmst', 
							'itmst.imitno = ordet.oditno', 
							array(
								'item_number' => 'trim(imitno)',
								'code'		  => 'trim(immc01)',
                       ))
						->joinLeft(
							'orhed', 
							'orhed.ohorno = ordet.odorno', 
							array(
								'entry_date' => 'CONCAT(ohetcc, ohetdt)',
                       ))
					   ->where('odorno = ?', $orderNumber)
					   ->where('imitno <> ?', '');
        
		// Order the results
		$select->order(array(
			'trim(oditno) ASC', 
			'trim(imitno) ASC'
		));
		
        // Execute the query
        $results = $select->query()->fetchAll();
		
		// Create index array for output
		$outputArray = array();
		
		// Load the WOHED table model.
		$wohed_model = new Rest_Model_Tables_WOHED($this->_db);
		
		// Load the PODET table model
		$podet_model = new Rest_Model_Tables_PODET($this->_db);
		
		// Loop throughout all the results
		foreach($results as $result)
		{
			// Query WOHED for the due date
			$getWohedDueDate = $wohed_model->getDueDate('itemNumber', $result['item_number']);
			
			// Query PODET for the due date
			$getPodetDueDate = $podet_model->getDueDate($result['item_number']);
			
			// If WOHED due date is not found, then query
			$setDueDate = $getWohedDueDate 
						? isSet($getWohedDueDate[0]['due_date']) ? $getWohedDueDate[0]['due_date'] : '' 
						: isSet($getPodetDueDate[0]['due_date']) ? $getPodetDueDate[0]['due_date'] : '';
		
			$array = array(
				'item_number' 			=> $result['item_number'],
				'item_description' 		=> $result['item_description'],
				'back_order_quantity' 	=> $result['back_order_quantity'],
				'quantity_ordered' 		=> $result['quantity_ordered'],
				'quantity_shipped' 		=> $result['quantity_shipped'],
				'line_item_type' 		=> $result['line_item_type'],
				'list_price' 			=> $result['list_price'],
				'order_total' 			=> $result['order_total'],
				'ship_total' 			=> $result['ship_total'],
				'back_order_total' 		=> $result['back_order_total'],
				'code' 					=> $result['code'],
				'entry_date' 			=> $result['entry_date'],
				'due_date'				=> $setDueDate,
			//	'wohed'					=> $getWohedDueDate,
			//	'podet'					=> $getPodetDueDate
			);
			
			// Merge the array into the $outputArray
			$outputArray[] = $array;
		}
		
		// Return the $outputArray to the controller
		return $outputArray;
    }
    
    //--------------------------------------------------------------------------
}
/* End of file ORDET.php */
/* Location: api/modules/rest/models/Tables/ORDET.php */