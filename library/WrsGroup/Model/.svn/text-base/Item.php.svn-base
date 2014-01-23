<?php
/**
 * Domain object for an item.
 *
 * Designed specifically to represent an item as found in the ITMST table
 * in the WRS Group APlus ERP system.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_Item extends WrsGroup_Model_ItemAbstract
    implements WrsGroup_Model_ItemInterface
{
    protected $_data = array(
        'imitno' => null,   // Item number
        'imsusp' => null,   // Suspended flag
        'imbmtp' => null,   // Bill of material type
    );

    /**
     * Gets item description field 1
     * 
     * @see WrsGroup_Model_ItemInterface::getDescription1()
     */
    public function getDescription1()
    {
        return $this->imitd1;
    }

    /**
     * Gets item description field 2
     * 
     * @see WrsGroup_Model_ItemInterface::getDescription2()
     */
    public function getDescription2()
    {
        return $this->imitd2;
    }

    /**
     * Gets item number
     * 
     * @see WrsGroup_Model_ItemInterface::getItemNumber()
     */
    public function getItemNumber()
    {
        return $this->imitno;
    }
}
