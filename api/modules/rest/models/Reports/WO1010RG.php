<?php
/**
 *  This model is used to simulate the WO1010RG report in AS/400
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 7, 2012, 8:14:44 AM
 */
class Rest_Model_Reports_WO1010RG
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
					'message' => 'Rest_Model_Reports_WO1010RG is only supported during production mode.'
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
        $this->_name = (APPLICATION_ENV === 'development') ? null : 'wohed';
        
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('escono', 'esorno', 'esorgn');
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
        $select = $this->_db->select()
                       //->setIntegrityCheck(false)
					   //->distinct(false)
                       ->from($this->_name, array(
                            //'escono',
                            'due_date'       => 'CONCAT(wohed.esducc, wohed.esdudt)',
							//'order_age'      => '(CONCAT(wohed.esducc, wohed.esdudt) - ' . date('Ymd') . ')',
                            'order_number'   => 'esorno',
                            'order_status'   => 'esorst',
                            'item_number'    => 'esprit',
                            'release_date'   => 'CONCAT(esstcc, esstdt)',
                            'order_quantity' => 'esqtor',
                       ))
                       ->joinLeft(
                           'wordt', 
                           'wordt.czorno = wohed.esorno', 
                           array(
                               'build_time_hours'	=> '((esqtor * czsrlh) / 60)',
                               'run_labor_hours'  	=> 'czsrlh',
                               'work_center'      	=> 'czwrkc'
                       ))
					   ->joinLeft(
							'itmst', 
                            'itmst.imitno = wohed.esprit', 
                            array(
                                'item_description' => 'imitd1'
                       ))
                       ->joinLeft(
                           'itbal', 
                           'itbal.ibitno = wohed.esprit', 
                           array(
                               'back_order_quantity' => 'ibboq1'
                       ))
                       ->joinLeft(
                           'orhed', 
                           'orhed.ohorno = wohed.esorno', 
                           array(
							//'order_age'     => '(CONCAT(orhed.ohetcc, orhed.ohetdt) - ' . date('Ymd') . ')',
							'order_age'     => '('. date('Ymd') . '- CONCAT(orhed.ohetcc, orhed.ohetdt))',
							'entry_date'	=> '(CONCAT(orhed.ohetcc, orhed.ohetdt))'
                       ));
					   
        /*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%.
         % LET THE CUSTOM FILTERS BEGIN ;)
        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
        /**********************************************************************/
        
        // If order number param is set, the attempt to use it.
        if(isSet($params['order-number']))
            $select->where('wohed.esorno = ?', $params['order-number']);
        
        /**********************************************************************/
        
        // If order status param is set, the attempt to use it.
        if(isSet($params['order-status']))
            $select->where('wohed.esorst IN (?)', explode(',', $params['order-status']));
        
        /**********************************************************************/
		
        // If work-center param is set, the attempt to use it.
        if(isSet($params['work-center']))
			$select->where('wordt.czwrkc IN (?)', explode(',', $params['work-center']));

        /**********************************************************************/
        
        // If item number param is set, the attempt to use it.
        if(isSet($params['item-number']))
            $select->where('wohed.esprit = ?', $params['item-number']);
        
        /**********************************************************************/
		
        // If order param is set, attempt to use it
        if(isSet($params['order-by']))
        {
			// Create index array for all sort filters to be appended to.
			$orderByFilter = array();
		
            // Get all the order params from the uri
			$orderBys = explode(',', $params['order-by']);
			
            // Loop through them
			foreach($orderBys as $orderBy)
			{
				// Separate the columns from the sort distinction
				$split_column_from_sort = explode(':', $orderBy);
				
                // Get the column name
				$column = $split_column_from_sort[0];
				
                // Get the sort destinction
				$asc_or_desc = strtoupper($split_column_from_sort[1]);
				
                // Default to false...will be overridden in the switch case.
                $translated_column = FALSE;
                
                // Translate the column names
                switch($column)
                {
                    /**********************************************************/
                    case 'back-order-quantity': 
                        $translated_column = 'itbal.ibboq1';
                    break;
                    /**********************************************************/
                    case 'order-number':
                        $translated_column = 'wohed.esorno';
                    break;
                    /**********************************************************/
                    case 'order-quantity':
                        $translated_column = 'wohed.esqtor';
                    break;
                    /**********************************************************/
                    case 'order-status':
                        $translated_column = 'wohed.esorst';
                    break;
					/**********************************************************/
					// Default to false
					default: $translated_column = false; break;
                    /**********************************************************/
                }

                // Append to the $orderByFilter
				if($translated_column)
				{
					$orderByFilter[] = "{$translated_column} {$asc_or_desc}";
				}
			}
			
            // Merge all the orders into one.
			if(count($orderByFilter) >= 1) $select->order(implode(' , ', $orderByFilter));
            
        }
        
        // Default to default sort
        else {
			/*
			$select->order(array(
				'wohed.esorst ASC',
				'(CONCAT(wohed.esducc, wohed.esdudt) - ' . date('Ymd') . ') DESC',
				'wohed.esorno ASC',
				'CONCAT(wohed.esducc, wohed.esdudt) ASC',
			));
			*/
			
			$select->order(array(
				'wohed.esorst ASC',
				'('. date('Ymd') . '- CONCAT(orhed.ohetcc, orhed.ohetdt)) ASC',
				'wohed.esorno ASC',
				'CONCAT(wohed.esducc, wohed.esdudt) ASC',
			));
			
        }
        
        /**********************************************************************/
        
        // If limit is set, attempt to use it
        if(isSet($params['limit']))
            $select->limit((int) $params['limit']);
        
        /**********************************************************************/
        /*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
         % END CUSTOM FILTERS
        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
		
		/* DEBUG OUT *\/
		return $select->__toString();
		/**/
		
        // Execute the query
        return $select->query()->fetchAll();
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WO1010RG.php */
/* Location: api/modules/rest/models/Reports/WO1010RG.php */