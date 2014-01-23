<?php
/**
 * Domain object for a BOM parent
 *
 * Designed specifically to represent an item as found in the BOMPR table
 * in the WRS Group APlus ERP system.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_BomParent extends WrsGroup_Model_Item
    implements WrsGroup_Model_ItemInterface, WrsGroup_Model_BomParentInterface
{
    protected $_data = array(
        'bpcono' => null,   // Company number
        'bpcsno' => null,   // Customer number
        'bporno' => null,   // Order number
        'bporsq' => null,   // Order sequence number
        'bpprit' => null,   // Parent item number
        'bpbmtp' => null,   // BOM type
        'bpcmct' => null,   // Component count
        'bph4sq' => null,   // Highest sequence number
        'bplbhr' => null,   // Labor hours
        'bplbcd' => null,   // Labor rate code
    );

    /**
     * Gets the parent item number.
     * 
     * @see WrsGroup_Model_BomParentInterface::getParentItemNumber()
     */
    public function getParentItemNumber()
    {
        return $this->bpprit;
    }

    /**
     * Gets the labor hours.
     * 
     * @see WrsGroup_Model_BomParentInterface::getLaborHours()
     */
    public function getLaborHours()
    {
        return $this->bplbhr;
    }
}
