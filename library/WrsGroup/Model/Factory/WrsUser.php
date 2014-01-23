<?php
/**
 * Factory for WrsUser objects
 *
 * @category WrsGroup
 * @package Model
 * @subpackage Factory
 * @author Eugene Morgan
 */
class WrsGroup_Model_Factory_WrsUser implements WrsGroup_Model_Factory_UserInterface
{
    /**
     * Creates a WrsUser object
     *
     * @param array $data An array of data
     * @return WrsGroup_Model_WrsUser A WrsUser object
     */
    public function create($data)
    {
        return new WrsGroup_Model_WrsUser($data);
    }
}