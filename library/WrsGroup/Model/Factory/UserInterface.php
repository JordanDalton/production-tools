<?php
/**
 * Factory interface for user objects
 *
 * @category WrsGroup
 * @package Model
 * @subpackage Factory
 * @author Eugene Morgan
 */
interface WrsGroup_Model_Factory_UserInterface
{
    /**
     * Creates a user object
     *
     * @param array $data An array of data
     * @return WrsGroup_Model_UserAbstract A user object
     */
    public function create($data);
}