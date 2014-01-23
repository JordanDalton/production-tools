<?php
/**
 * Interface for all objects representing a BOM parent.
 *
 * Specifically, this is for items representing the BOM parents as found in
 * the BOMPR or similar tables of the A+ ERP software or related applications.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
interface WrsGroup_Model_BomParentInterface
{
    /**
     * Gets the parent item number.
     * 
     * @return string
     */
    public function getParentItemNumber();

    /**
     * Gets the labor hours.
     * 
     * @return float 
     */
    public function getLaborHours();
}
