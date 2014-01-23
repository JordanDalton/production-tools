<?php
/**
 * Abstract class for item decorator
 * 
 * @uses ItemInterface
 * @abstract
 * @category WrsGroup
 * @package Decorator
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
abstract class WrsGroup_Decorator_ItemAbstract 
    implements WrsGroup_Model_ItemInterface
{
    /**
     * @var ItemInterface
     */
    protected $_item;

    /**
     * Constructor
     * 
     * @param WrsGroup_Model_ItemInterface $item An object that implements the item interface
     */
    public function __construct(WrsGroup_Model_ItemInterface $item)
    {
        $this->_item = $item;
    }

    /**
     * @see WrsGroup_Model_ItemInterface::getDescription1()
     */
    public function getDescription1()
    {
        return $this->_item->getDescription1();
    }

    /**
     * @see WrsGroup_Model_ItemInterface::getDescription2()
     */
    public function getDescription2()
    {
        return $this->_item->getDescription2();
    }

    /**
     * @see WrsGroup_Model_ItemInterface::getItemNumber()
     */
    public function getItemNumber()
    {
        return $this->_item->getItemNumber();
    }
}
