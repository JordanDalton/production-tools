<?php
/**
 * Interface for all "item" objects to implement.
 *
 * Specifically, this is for items representing the items in WRS Group's
 * A+ ERP software.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
interface WrsGroup_Model_ItemInterface
{
    /**
     * Gets item description field 1
     *
     * @return string The item description
     */
    public function getDescription1();

    /**
     * Gets item description field 2
     *
     * @return string The item description
     */
    public function getDescription2();

    /**
     * Gets the full item description
     * 
     * @return string The full item description
     */
    public function getDescription();

    /**
     * Gets item number
     *
     * @return string The item number
     */
    public function getItemNumber();
}
