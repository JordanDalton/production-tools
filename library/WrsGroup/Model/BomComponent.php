<?php
/**
 * Domain object for a BOM component
 *
 * Designed specifically to represent an item as found in the BOMCO table
 * in the WRS Group APlus ERP system.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_BomComponent extends WrsGroup_Model_Item
    implements WrsGroup_Model_ItemInterface, WrsGroup_Model_BomComponentInterface
{
    protected $_data = array(
        'bccono' => null,   // Company number
        'bccsno' => null,   // Customer number
        'bcorno' => null,   // Order number
        'bcorsq' => null,   // Order sequence number
        'bcprit' => null,   // Parent item number
        'bcbmsq' => null,   // BOM sequence number
        'bccmit' => null,   // Child item number
        'bccmum' => null,   // Unit of measure
        'bcqtpr' => null,   // Quantity per parent
        'bcstwh' => null,   // Stocking warehouse
    );

    /**
     * Gets parent item number.
     * 
     * @see WrsGroup_Model_BomComponentInterface::getParentItemNumber()
     */
    public function getParentItemNumber()
    {
        return $this->bcprit;
    }

    /**
     * Gets component item number.
     * 
     * @see WrsGroup_Model_BomComponentInterface::getComponentItemNumber()
     */
    public function getComponentItemNumber()
    {
        return $this->bccmit;
    }

    /**
     * Gets quantity per parent.
     * 
     * @see WrsGroup_Model_BomComponentInterface::getQuantityPerParent()
     */
    public function getQuantityPerParent()
    {
        return $this->bcqtpr;
    }
}
