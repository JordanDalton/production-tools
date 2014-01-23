<?php
/**
 *  Interface with the work_center_group table inside AS/400.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 13, 2012, 2:41:06 PM
 */
class Rest_Model_Tables_WorkCenterGroup
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    /**
     * @var string DB Table Name
     */
    protected $_name;
    
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
        $this->_name = (APPLICATION_ENV === 'development') ? null : 'work_center_group';
    }

    //--------------------------------------------------------------------------
    
    /**
     * Retrieve all work center groups for given warehouse
     * 
     * @param string $warehouseId       The warehouse ID
     * @return WrsGroup_Model_RecordSet A record set of work center group objects
     */
    public function getAll($warehouseId, $params)
    {
        // Prepare our query
        $select = $this->_db->select()
                       //->setIntegrityCheck(false)
                       ->from($this->_name, array(
                           'wcg_id'		  => 'TRIM(wcg_id)',
                           'wcg_name'	  => 'TRIM(wcg_name)',
                           'wcg_staffing' => 'TRIM(wcg_staffing)',
                       ))
                       ->join(
                           'wowcm',
                           'TRIM(cwus15) = wcg_id', array(
                               'cwwhid'	=> 'TRIM(cwwhid)',
                               'cwwcid'	=> 'TRIM(cwwcid)',
                               'cwwcds' => 'TRIM(cwwcds)',
                       ))
                       ->where('cwwhid = ?', $warehouseId);

        /*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%.
         % LET THE CUSTOM FILTERS BEGIN ;)
        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
        /**********************************************************************/
        
        // If group id param is set, the attempt to use it.
        if(isSet($params['group-id']))
            $select->where('wcg_id IN (?)', explode(',', strtoupper($params['group-id'])));
        
		/**********************************************************************/	
			
        // If work center param is set, the attempt to use it.
        if(isSet($params['work-center']))
            $select->where('cwwcid IN (?)', explode(',', strtoupper($params['work-center'])));
        
        /**********************************************************************/
        
        // If order number param is set, the attempt to use it.
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
                    case 'group': 
                        $translated_column = 'wcg_id';
                    break;
                    /**********************************************************/
                    case 'name':
                        $translated_column = 'wcg_name';
                    break;
                    /**********************************************************/
                    case 'staffing':
                        $translated_column = 'wcg_staffing';
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
        
        // Use default sort
        else {
            
            $select->order('wcg_id');
        }
        
        /**********************************************************************/

        // Execute the query retrieve the results;
        $results = $select->query()->fetchAll();
		
        // Return the results to the controller.
        return $results;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Save work center group (serves for new entry and updates)
     * 
     * @params array $params The uri paramters being passed.
     * @params string|int The id of the warehouse.
     */
    public function createWorkCenterGroup($params, $warehouseId)
    {
        // See if the record already exists
        $getCount = $this->_db->select()
                              ->from($this->_name, array(
                                  'cnt' => 'COUNT(*)'
                              ))
                              ->where('wcg_id = ?', strtoupper($params['group-id']))
                              ->query();
        
        // Execute query and return the results.
        $count = $getCount->fetchColumn();
        
        // Split out the work center(s) individually
        $expolodeWorkCenters = explode(',', $params['work-center']);

        // Unassign any previous assignents of this particular group.
        $this->updateWOWCM($params, $warehouseId, true);
        
        // Update work center table.
        $this->updateWOWCM($params, $warehouseId);
        
		// Is an insert
		if($count == 0)
        {               
            $populateForInsertion = array
            (
                'wcg_id'        => strtoupper($params['group-id']),
                'wcg_name'      => $params['description'],
                'wcg_staffing'  => $params['staffing']
            );   

            // Insert new record into the database.
            $this->_db->insert(
                $this->_name,
                $populateForInsertion
            );

            return true;
		} 
        
        // It's an update
        else {
            
            // Prepare our update data.
            $populateForUpdate = array
            (
                'wcg_name'      => $params['description'],
                'wcg_staffing'  => $params['staffing']
            );   
            
            // Update work center group table.
            $this->_db->update(
                $this->_name,
                $populateForUpdate,
                $this->_db->quoteInto('wcg_id = ?', strtoupper($params['group-id']))
            );
            
            return true;
        }
        
        return false;
    }
    
    //--------------------------------------------------------------------------

    /**
     * Update WOWCM table.
     * 
     * @param array $params The uri parameters being passed.
     * @param string|int $warehouseId The id of the warehouse this applies to.
     * @param boolean $refresh
     * @return boolean
     */
    public function updateWOWCM($params, $warehouseId, $refresh = false)
    {
        // Set the group id
        $groupID = $params['group-id'];
        
        // Split out the work center(s) individually
        $expolodeWorkCenters = explode(',', $params['work-center']);
        
        switch($refresh)
        {
            /******************************************************************/
            case TRUE: 
                
                // Update work center table.
                $this->_db->update(
                    'wowcm',
                    array('cwus15' => ''),
                    array(
                        $this->_db->quoteInto('cwus15 = ?',  $groupID),
                        $this->_db->quoteInto('cwwhid = ?',  $warehouseId),
                    )
                );
                
            break;
            /******************************************************************/
            case FALSE:
                
                // Update work center table.
                $this->_db->update(
                    'wowcm',
                    array('cwus15' => $groupID),
                    array(
                        $this->_db->quoteInto('cwwcid IN (?)',  $expolodeWorkCenters),
                        $this->_db->quoteInto('cwwhid = ?',     $warehouseId),
                    )
                );
                
            break;
            /******************************************************************/
        }
        
        
        return true;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Saves a work center group -- BACKUP
     * 
     * @param WrsGroup_Model_WorkCenterGroup $workCenterGroup Domain object
     * @param string                         $warehouseId     The warehouse ID
     * @return int The number of rows affected
     */
    public function saveBU($workCenterGroup, $warehouseId)
    {
        // See if the record already exists
        $select = $this->_db->select()
                       ->from($this->_name, array(
                           'cnt' => 'COUNT(*)'
                       ))
                       ->where('wcg_id = ?', $workCenterGroup->groupId);
        
        // Execute query and return the results.
        $stmt = $select->query();
        $count = $stmt->fetchColumn();

        // Modify work center information
        if ($workCenterGroup->isPopulated('workCenters'))
        {
            $workCenters   = $workCenterGroup->getWorkCenters();
            $workCenterIds = $workCenters->getValues('workCenterId');
            
            $this->update(
                'wowcm',
                array('cwus15' => $workCenterGroup->groupId),
                array(
                    $this->_db->quoteInto('cwwcid IN (?)', $workCenterIds),
                    $this->_db->quoteInto('cwwhid = ?', $warehouseId),
                )
            );
            
            $workCenterGroup->unpopulate('workCenters');
        }

        // If it's an update
        if ($count) {
            return $this->update(
                'work_center_group',
                $workCenterGroup->getPopulatedForDb(),
                $this->_db->quoteInto('wcg_id = ?', $workCenterGroup->groupId)
            );
        }

        // If it's an insert
        return $this->insert(
            'work_center_group',
            $workCenterGroup->getPopulatedForDb()
        );
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Builds work center group objects from database data
     * 
     * @param array $result A group of data rows
     * @return array A group of work center group objects
     */
    protected function _groupDbResultForObjectCreation($result)
    {
        // Loop through result and create one work center group object
        // for each work center group, with the appropriate work centers
        // as a record set within that object
        $workCenterGroups = array();
        $groupId = '';
        
        foreach ($result as $row)
        {
            if ($row['wcg_id'] != $groupId)
            {
                $workCenterGroups[] = new WrsGroup_Model_WorkCenterGroup($row);
                $groupId = $row['wcg_id'];
                $workCenters[$groupId] = array();
            }
            
            $workCenters[$groupId][] = new WrsGroup_Model_WorkCenter($row);
        }
        
        foreach ($workCenterGroups as $key => $workCenterGroup)
        {
            $workCenterGroup->setWorkCenters(new WrsGroup_Model_RecordSet(
                $workCenters[$workCenterGroup->groupId],
                'WrsGroup_Model_WorkCenter'
            ));
            
            $workCenterGroups[$key] = $workCenterGroup;
        }
        
        return $workCenterGroups;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Deletes a work center group
     * 
     * @param integer $groupId The work center group ID
     * @return integer Number of rows affected
     */
    public function deleteWorkCenterGroup($groupId)
    {
        // First, scrub any work center records associated with the work
        // center group
        $this->_db->update(
            'wowcm',
            array('cwus15' => ''),
            $this->_db->quoteInto('cwus15 = ?', $groupId)
        );

        return $this->_db->delete(
            'work_center_group', 
            $this->_db->quoteInto('wcg_id = ?', $groupId)
        );
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WorkCenterGroup.php */
/* Location: api/modules/rest/models/Tables/WorkCenterGroup.php */