<?php
/**
 * Interface for all objects representing a BOM component.
 *
 * Specifically, this is for items representing the BOM components as found in
 * the BOMCO or similar tables of the A+ ERP software or related applications.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
interface WrsGroup_Model_BomComponentInterface
{
    /**
     * Gets the parent item number.
     * 
     * @return string
     */
    public function getParentItemNumber();

    /**
     * Gets the component item number.
     * 
     * @return string
     */
    public function getComponentItemNumber();

    /**
     * Gets the quantity per parent.
     *
     * @return float
     */
    public function getQuantityPerParent();
}
