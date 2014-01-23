<?php
/**
 *  Work Order Status
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 7, 2012, 2:57:41 PM
 */
class MY_WorkOrder_Status
{
	protected $_status_id;
    protected $_status_id_list = array('1','2','20','21','3','31','32','9');
	protected $_status_text;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     * @param string|int $_status_id The id for the status.
     */
    public function __construct($_status_id = false)
    {
        if($_status_id)
        {
            $this->_status_id = (int) $_status_id;

            // Determine the status text
            $this->_determineStatus();   
        }
    }

    //--------------------------------------------------------------------------
    
    /**
     * Get the get status id.
     * 
     * @return int The status id.
     */
    public function get_status_id()
    {
        return (int) $this->_status_id;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Set the status id.
     * 
     * @param string|int $_status_id 
     */
    public function set_status_id($_status_id)
    {
        $this->_status_id = (int) $_status_id;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Set the id of the status we want data for.
     * 
     * @param int|string $_status_id
     * @return MY_WorkOrder_Status 
     */
    public function id($_status_id)
    {
        $this->set_status_id($_status_id);
        
        return $this;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Get individual status text value. 
     * 
     * @return string The text name value for the id.
     */
    public function get()
    {
        $this->_determineStatus();
        
        return $this->_status_text;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     *
     * @return array The list of all work order statuses
     */
    public function getAll()
    {
        // Index array for all the result to be appended to.
        $outputArray = array();
        
        if(count($this->_status_id_list) >= 1)
        {
            foreach($this->_status_id_list as $key => $value)
            {
                // Convert value to integer
                $value = (int) $value;
                
                $outputArray[$value] = $this->id($value)->get();
            }
        }
        
        // Return the results.
        return $outputArray;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Get the text value for the id.
     * 
     * @return string The text value for the id.
     */
    public function get_status_text()
    {
        return $this->_status_text;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Override the status text.
     * 
     * @param string $_status_text 
     */
    public function set_status_text($_status_text)
    {
        $this->_status_text = $_status_text;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Determine the status text based upon the id supplied.
     */
    private function _determineStatus()
    {
        switch($this->_status_id)
        {
            //------------------------------------------------------------------
            // Release Ready
            case 1:     $status = 'Release Ready';              break;
            //------------------------------------------------------------------
            // Released
            case 2:     $status = 'Released';                   break;
            //------------------------------------------------------------------
            // Released - Mtl Short
            case 20:    $status = 'Released - Material Short';  break;
            //------------------------------------------------------------------
            // Released - Pck Ready
            case 21:    $status = 'Released - Pck Ready';       break;
            //------------------------------------------------------------------
            // Confirmed
            case 3:     $status = 'Confirmed';                  break;
            //------------------------------------------------------------------
            // Confirmed - Mtl Short
            case 31:    $status = 'Confirmed - Material Short'; break;
            //------------------------------------------------------------------
            // Confirmed - Pck Ready
            case 32:    $status = 'Confirmed - Pck Ready';      break;
            //------------------------------------------------------------------
            // Material Short
            case 9:     $status = 'Material Short';             break;
            //------------------------------------------------------------------
            // Default
            default:    $status = 'Undefined';                  break;
            //------------------------------------------------------------------
        }
        
        return $this->set_status_text($status);
    }
    
    //--------------------------------------------------------------------------
}
/* End of file Status.php */
/* Location: library/MY/WorkOrder/Status.php */