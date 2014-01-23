<?php
/**
 * Domain object for WRS user
 *
 * @package Model
 * @author Eugene Morgan
 */
class WrsGroup_Model_WrsUser extends WrsGroup_Model_UserAbstract
    implements WrsGroup_Model_UserInterface
{
    protected $_data = array(
        'ldapData' => null,
    );

    /**
     * Gets the user's name in format to be displayed
     *
     * If the display name is not present in LDAP data (i.e., if the user 
     * has been deleted), just displays the username.
     *
     * @return string The name to display
     */
    public function getDisplayName()
    {
        if (isset($this->ldapData['displayname'][0])) {
            return $this->ldapData['displayname'][0];
        }
        return $this->getUsername();
    }

    /**
     * Gets the e-mail address
     *
     * @return WrsGroup_Model_EmailAddress The e-mail address
     */
    public function getEmailAddress() 
    {
        if (!$this->_emailAddress) {
            $address = $this->ldapData['mail'][0];
            $this->_emailAddress = new WrsGroup_Model_EmailAddress(array(
                'address' => $address,
                'name' => $this->getDisplayName()
            ));
        }
        return $this->_emailAddress;
    }

    /**
     * Gets the first name
     *
     * @return string The user's first name
     */
    public function getFirstName() 
    {
        return $this->ldapData['givenname'][0];
    }

    /**
     * Gets the last name
     *
     * @return string The user's last name
     */
    public function getLastName() 
    {
        return $this->ldapData['sn'][0];
    }

    /**
     * Gets the username
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Groups are currently not available for Let's Gel users
     *
     * @return WrsGroup_Model_RecordSet A record set of group objects
     */
    public function getUserGroups()
    {
        if (!$this->_groups) {
            if (!isset($this->ldapData['memberof'])) {
                $cleanData = array();
            } else {
                $rawData = $this->ldapData['memberof'];
                $cleanData = array();
                foreach ($rawData as $value) {
                    $array = explode(',', $value);
                    $cleanData[] = array(
                        'group' => str_replace('CN=', '', $array[0])
                    );
                }
            }
            $this->_groups = new WrsGroup_Model_RecordSet(
                $cleanData,
                'WrsGroup_Model_Group'
            );
        }
        return $this->_groups;
    }

    /**
     * Tells if the user is a member of the given group.
     * 
     * @param string $groupName The name of the group
     * @return bool Whether the user is a member of the group
     */
    public function isMemberOf($groupName)
    {
        $groups = $this->getUserGroups();
        $result = $groups->findOneBy('group', $groupName);
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * Tells whether the user is active
     *
     * @return bool Whether the user is active
     */
    public function isActive()
    {
        // If LDAP data is present, check if the account is disabled
        if ($this->ldapData && isset($this->ldapData['useraccountcontrol'])) {
            if ($this->ldapData['useraccountcontrol'][0] == '514') {
                return false;
            }
            return true;
        }
        return (bool) $this->active;
    }

    /**
     * Gets the organization name 
     *
     * @return string The organization name
     */
    public function getOrganization()
    {
        return 'WRS Group, Ltd.';
    }

    /**
     * Gets the user type
     *
     * @return string The user type
     */
    public function getUserType()
    {
        return self::WRS_GROUP;
    }
}
