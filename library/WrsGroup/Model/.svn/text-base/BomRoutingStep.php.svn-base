<?php
/**
 * Domain object for a BOM routing step
 *
 * Designed specifically to represent an entry in the BOMRT table
 * in the WRS Group APlus ERP system.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_BomRoutingStep extends WrsGroup_Model_Item
    implements WrsGroup_Model_ItemInterface, 
    WrsGroup_Model_BomRoutingStepInterface
{
    protected $_data = array(
        'cgcono' => null,   // Company number
        'cgcsno' => null,   // Customer number
        'cgorno' => null,   // Order number
        'cgorsq' => null,   // Order sequence number
        'cgprit' => null,   // Parent item number
        'cgrtsq' => null,   // Routing sequence
        'cgdept' => null,   // Department
        'cgwrkc' => null,   // Work center
        'cgopcd' => null,   // Operation code
        'cgopds' => null,   // Operation description
        'cgsrlh' => null,   // Labor hours
    );

    /**
     * The work center associated with this routing step
     * 
     * @var WrsGroup_Model_WorkCenter
     */
    protected $_workCenter;

    /**
     * Gets the parent item number.
     * 
     * @see WrsGroup_Model_BomRoutingStepInterface::getParentItemNumber()
     */
    public function getParentItemNumber()
    {
        return $this->cgprit;
    }

    /**
     * Gets the labor hours.
     * 
     * @see WrsGroup_Model_BomRoutingStepInterface::getLaborHours()
     */
    public function getLaborHours()
    {
        return $this->cgsrlh;
    }

    /**
     * Gets the operation description.
     * 
     * @see WrsGroup_Model_BomRoutingStepInterface::getOperationDescription()
     */
    public function getOperationDescription()
    {
        return $this->cgopds;
    }
}
