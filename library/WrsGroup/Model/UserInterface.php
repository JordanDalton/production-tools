<?php
/**
 * Interface for all user objects to implement.
 *
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
interface WrsGroup_Model_UserInterface
{
    /**
     * Gets a record set of groups the user belongs to
     *
     * @return WrsGroup_Model_RecordSet
     */
    public function getUserGroups();

    /**
     * Gets the user's name in format to be displayed
     *
     * @return string The name to display
     */
    public function getDisplayName();

    /**
     * Gets the first name
     *
     * @return string The user's first name
     */
    public function getFirstName();

    /**
     * Gets the last name
     *
     * @return string The user's last name
     */
    public function getLastName();

    /**
     * Gets the username
     *
     * @return string The username
     */
    public function getUsername();

    /**
     * Gets the e-mail address
     *
     * @return WrsGroup_Model_EmailAddress The e-mail address
     */
    public function getEmailAddress();

    /**
     * Tells whether the user is active
     *
     * @return bool Whether or not the user is active
     */
    public function isActive();

    /**
     * Gets the name of the user's organization
     *
     * @return string The organization name
     */
    public function getOrganization();

    /**
     * Should return a value representing the user type that matches
     * the value of a constant defined for this class. I.e., for the Let's Gel
     * user type, this function will return the value of self::LETS_GEL.
     *
     * @return string Value for the user type
     */
    public function getUserType();
}
