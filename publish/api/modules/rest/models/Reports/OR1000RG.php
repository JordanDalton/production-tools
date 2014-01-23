<?php
/**
 *  This model is used to simulate the OR1000RG report in AS/400
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 7, 2012, 8:14:44 AM
 */
class Rest_Model_Reports_OR1000RG
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
        if(APPLICATION_ENV === 'development')
        {
            $errorArray = array
			(
                'status' => 'failed',
                'version'=> '1.0',
                'response' => array(
					'message' => 'Rest_Model_Reports_OR1000RG is only supported during production mode.'
				)
            );
            
            echo json_encode($errorArray);
            exit;
        }
        
        // Retrieve the db adapter and assign it.
        $this->_db = $db;
        
        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        //Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
               
        // Table names differ depending upon the enviornment
        $this->_name = (APPLICATION_ENV === 'development') ? null : 'orhed';
        
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('ohcono','ohorno','ohorgn');
    }
    
    //--------------------------------------------------------------------------

	/**
     * Retreive the WO1010RG report.
     * 
     * @param array The array of custom parameters passed.
     * @return mixed
     */
    public function getReport($params)
    {
		/*
		ordtot += + (odlspr * odqtor);
		shptot += + (odlspr * odqtsh);
		bkotot += + (odlspr * odboqt);
		*/
		
        $select = $this->_db
					   ->select()
                       //->setIntegrityCheck(false)
					   //->distinct(false)
                       ->from($this->_name, array(
							'order_number'            => 'ohorno',
							'order_generation_number' => 'ohorgn',
							'customer_name'           => 'trim(ohcsnm)',
					   ));
	   
        /*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%.
         % LET THE CUSTOM FILTERS BEGIN ;)
        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
        
        // If order number param is set, the attempt to use it.
        if(isSet($params['order-number']))
            $select->where('ohorno = ?', $params['order-number']);
        
        /**********************************************************************/
		
		// Add finale restrictions
        $select->where('ohcscd = ?'	, 'H')
               ->where('ohprac <> ?', ' ')
			   ->where('ohorsc <> ?', 'I')
			   ->where('ohorsc <> ?', 'I1')
               ->where('ohortp = ?'	, 'B')
               ->where('ohorgn > ?'	, '00');
			   
		// Order the results
		$select->order('order_number ASC');
        
        /*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
         % END CUSTOM FILTERS
        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
        
		/* DEBUG OUT *\/
		return $select->__toString();
		/**/
		
        // Execute the query
        $results = $select->query()->fetchAll();
		
		$outputArray = array();
		
		// Load the ORDET table model.
		$ordet_model = new Rest_Model_Tables_ORDET($this->_db);
		
		foreach($results as $result)
		{
			$array = array(
				'order_number' 				=> $result['order_number'],
				'order_generation_number' 	=> $result['order_generation_number'],
				'customer_name' 			=> $result['customer_name'],
				'item_data'					=> $ordet_model->getItemsByOrderNumber($result['order_number'])
			);
					
			$outputArray[] = $array;
			
		}
		
		return $outputArray;
    }
	
    //--------------------------------------------------------------------------

	/**
     * Retreive the WO1010RG report.
     * 
     * @param array The array of custom parameters passed.
     * @return mixed
     */
    public function getReportBackup($params)
    {
        $select = $this->_db
					   ->select()
                       //->setIntegrityCheck(false)
					   //->distinct(false)
                       ->from($this->_name, array(
							'order_number'            => 'ohorno',
							'order_generation_number' => 'ohorgn',
							'customer_name'           => 'trim(ohcsnm)',
							'entry_date'              => 'CONCAT(ohetcc, ohetdt)',
					   ))
                       ->joinLeft(
                           'ordet', 
                           'ordet.odorno = orhed.ohorno AND ordet.odcono = orhed.ohcono', 
                           array(
							'item_number'			=> 'trim(oditno)',
							'item_description' 		=> 'trim(CONCAT(oditd1, oditd2))',
							'back_order_quantity' 	=> 'odboqt',
							'quantity_ordered'		=> 'odqtor',
                            'quantity_shipped'		=> 'odqtsh',
							'line_item_type' 		=> 'odlitp',
							'list_price' 			=> 'odlspr',
							'order_total'			=> '(odlspr * odqtor)',
							'ship_total'			=> '(odlspr * odqtsh)',
							'back_order_total'		=> '(odlspr * odboqt)'
                       ))
                       ->joinLeft(
                           'itmst', 
                           'itmst.imitno = ordet.oditno', 
                           array(
							'item_number' => 'trim(imitno)',
							'code'		  => 'trim(immc01)',
                       ))
                       ->joinLeft(
                           'wohed', 
                           'wohed.esprit = itmst.imitno', 
                           array(
							'due_date' => 'CONCAT(wohed.esducc, wohed.esdudt)',
                       ))
                       ->joinLeft(
                           'podet', 
                           'podet.pditno = wohed.esprit', 
                           array(
							'due_date' => 'CONCAT(podet.pdducc, podet.pddudt)',
                       ));
        
        /*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%.
         % LET THE CUSTOM FILTERS BEGIN ;)
        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
        
        // If order number param is set, the attempt to use it.
        if(isSet($params['order-number']))
            $select->where('ohorno = ?', $params['order-number']);
        
        /**********************************************************************/
        
        // If item number param is set, the attempt to use it.
        if(isSet($params['item-number']))
            $select->where('ordet.oditno = ?', $params['item-number']);
        
        /**********************************************************************/
        
        $select->where('ohcscd = ?'	, 'H')
               ->where('ohprac <> ?', ' ')
			   ->where('ohorsc <> ?', 'I')
               ->where('ohortp = ?'	, 'B')
               ->where('ohorgn > ?'	, '00')
			   ->where('imitno <> ?', '');
        
        /*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
         % END CUSTOM FILTERS
        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
        
		/* DEBUG OUT */
		return $select->__toString();
		/**/
		
        // Execute the query
        return $select->query()->fetchAll();
    }
    
    //--------------------------------------------------------------------------
}
/* End of file OR1002RG.php */
/* Location: api/modules/rest/models/Reports/OR1002RG.php */