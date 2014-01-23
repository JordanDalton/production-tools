<?php
/**
 * Abstract domain object for an item.
 *
 * Designed specifically to represent an item as found in the ITMST table,
 * or similar tables, in the WRS Group APlus ERP system.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
abstract class WrsGroup_Model_ItemAbstract 
    extends WrsGroup_Model_DomainObject_Abstract
    implements WrsGroup_Model_ItemInterface
{
    /**
     * @var string Item description 1
     */
    protected $_imitd1;

    /**
     * @var string Item description 2
     */
    protected $_imitd2;

    /**
     * Retrieves basic item description - just concatenates the two item
     * description fields
     * 
     * @return string The item description
     */
    public function getDescription()
    {
        return $this->getDescription1() . $this->getDescription2();
    }
}
