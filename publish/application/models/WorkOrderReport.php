<?php
/**
 *  Work Order Report
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 14, 2012, 9:40:01 AM
 *  @see AS/400 DB Schema   ..\schemas\AS400\pt_wor.sql
 *  @see MySQL  DB Schema   ..\schemas\MySQL\gui_wor.sql
 */
class Application_Model_WorkOrderReport
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
	public function __construct($db)
	{
        // Retrieve the db adapter and assign it.
        $this->_db = $db;
        
        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        // Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
        
        // Table names differ depending upon the enviornment
        $this->_name = (APPLICATION_ENV === 'development') ? 'gui_wor' : 'pt_wor';

        // Columns Array
        $columns= array(
            'id', 
            'bo_value', 
            'author', 
            'created', 
            'headings', 
            'record_count', 
            'report', 
            'rows', 
            'updated',
            'work_centers',
            'work_center_groups'
        );
        
        // Loop through the columns
        foreach($columns as $key => $value)
        {
            // Add them to the $_columns array.
            $this->_columns[strtolower($value)] = $value;
        }
        
        // Now update the $_columns to be an object array.
        $this->_columns = (object) $this->_columns;
        
        // Check the user existence in the session.
        $identity   = Zend_Auth::getInstance()->getIdentity();
        $userRepo   = $this->_container->getComponent('wrsUserRepo');
        $user       = $userRepo->getUser($identity);
        
        $this->_user = $user->getDisplayName();
	}

    //--------------------------------------------------------------------------

    /**
     * Get table report.
     * 
     * @param string|int|array $id  The record id of the table report. 
     * @return bool|array           False boolean on failure, Array on success
     */
    public function getTableReport($id)
    {
        // If $id is empty....BAIL!!!
        if(empty($id)) return false; 
                
        // Convert $id to an integer if it is not an array.
        $id = (is_array($id)) ? implode(',', $id) : implode(',', array($id));
        
        // Did we successfully retreive results? Default to yes (true).
        $results = true;
        
        try {
            
            // Attempt to select records from the db.
            $results = $this->_db->select()
                                 ->from($this->_name)
                                 ->where("{$this->_columns->id} IN (?)", explode(',', $id))
                                 ->query()
                                 ->fetchAll();
            
        } catch (Exception $exc) {
            
            //echo $exc->getTraceAsString();
            
            // Since there was a failure, override $results
            $results = false;
        }       
            
        // Return the results to the controller.
        return $results;
    }
    
    //--------------------------------------------------------------------------

    /**
     * Get table reports from the last two weeks.
     */
    public function getTwoWeeksOfTableReports()
    {
        // Get the current timestamp.
        $now = time();
        
        // Create instance of Zend_Date
        $zd = new Zend_Date();
        
        // Get the time stamp from 2 weeks ago
        $twoWeeksAgo = $zd->set($now)->subWeek(2)->toValue();        
        
        // Did we successfully retreive results? Default to yes (true).
        $results = true;
        
        try {
            
            // Attempt to select records from the db.
            $results = $this->_db->select()
                                 ->from($this->_name, array(
                                     $this->_columns->id,
                                     $this->_columns->author,
                                     $this->_columns->bo_value,
                                     $this->_columns->created,
                                     $this->_columns->record_count,
                                     $this->_columns->report,
                                     $this->_columns->work_centers,
                                     $this->_columns->work_center_groups
                                 ))
                                 ->where("{$this->_columns->created} <= ?", time())
                                 ->where("{$this->_columns->created} >= ?", $twoWeeksAgo)
                                 ->query()
                                 ->fetchAll();
            
        } catch (Exception $exc) {
            
            //echo $exc->getTraceAsString();
            
            // Since there was a failure, override $results
            $results = false;
        }       
            
        // Return the results to the controller.
        return $results;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Save table report to the database.
     * 
     * @param string $report  The name of the report that this data is for.
     * @param array $headings The column headings for the table.
     * @param array $rowData  The row data for the table. 
     */
    public function saveTableReport($report, $headings, $rowData, $misc = false)
    {
        // If $headings or $rowData is empty....bail!!!
        if(empty($report) || empty($headings) || empty($rowData)) return false;
        
        // If $headings or $rowData are not in array format...bail!!
        if(!is_array($headings) || !is_array($rowData)) return false;
        
        // If $misc is set and is not an array....bail!!!
        if(isSet($misc) && !is_array($misc)) return false;
        
        // JSON encode the headings
        $json_headings = serialize($headings);
        
        // JSON encode the row data.
        $json_rowData = serialize($rowData);
        
        // Prepare the insert data
        $insertData = array(
            'author'             => $this->_user,
            'bo_value'           => isSet($misc['bo_value']) ? $misc['bo_value'] : 0,
            'created'            => time(),
            'headings'           => $json_headings,
            'record_count'       => isSet($misc['record_count']) ? $misc['record_count'] : 0,
            'report'             => $report,
            'rows'               => $json_rowData,
            'work_centers'       => isSet($misc['work_centers']) ? $misc['work_centers'] : null,
            'work_center_groups' => isSet($misc['work_center_groups']) ? $misc['work_center_groups'] : null,
        );

        // Was our insertion into the database successful? Default to yes.
        $insertionSuccessful = true;
        
        // Lets attempt to insert the data.
        try {
            
            $this->_db->insert($this->_name, $insertData);
            
        } catch (Exception $exc) {
            
            // Since there was a failure, override $insertionSuccessful
            $insertionSuccessful = false;
            
            //echo $exc->getTraceAsString();
        }
        
        // Return results to the controller.
        return $insertionSuccessful;
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WorkOrderReport.php */