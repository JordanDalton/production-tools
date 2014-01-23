<?php
/**
 * Simple LDAP class for querying Active Directory
 * Allows us to go beyond capabilities of the Zend Framework --
 * The Zend_Ldap class currently will let you verify username and password.
 * This class lets you verify a user's department, etc.
 *
 * @author Eugene Morgan
 * @category WrsGroup
 */
class WrsGroup_Ldap
{
	private $_conn;
	private $_baseDn;
    private $_shortName;
    private $_excludedGroups;
    private $_excludedUsers;

    /**
     * Singleton instance
     *
     * @var WrsGroup_Ldap
     */
    private static $_instance = null;

	/**
     * Constructor
     *
     */
    protected function __construct()
	{
		// Get configuration info
        $config = Zend_Registry::get('config');
        $config = $config->ldap;

        // Connect to Active Directory
		$this->_conn = ldap_connect($config->host);
		ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->_conn, LDAP_OPT_REFERRALS, 0);

		// Bind to Active Directory with the Administrator logon
		ldap_bind($this->_conn, $config->username, $config->password);

		// Store base DN and short domain name in the object
		$this->_baseDn = $config->baseDn;
        $this->_shortName = $config->accountDomainNameShort;

        // Store excluded groups and users
        $this->_excludedGroups = explode(',', $config->excludedGroups);
        $this->_excludedUsers = explode(',', $config->excludedUsers);
	}

    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton instance
     *
     * @return WrsGroup_Ldap
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	/**
	 * Verifies that a user $username belongs to a group $group
	 *
	 * @param string  Username (generally first initial, last name)
	 * @param mixed   Either a string of the exact group name or an array if
	 *                you want to verify that user belongs to at least one of
	 *                several groups.
	 * @return string Name of the group that's matched (helpful if the method
	 *                was called w/ an array of groups) if true; otherwise
	 *                boolean false if user does not belong to group(s)
	 */
	public function verifyGroup($username, $groups)
	{
		// First, verify that the user exists and get user info
		$entries = $this->verifyUser($username);
		if (!$entries) {
			return false;
		}

		// Search for the group(s) in the returned data
		if (!is_array($groups)) {
			$groups = array($groups);
		}
		foreach ($groups as $group) {
			foreach ($entries[0]['memberof'] as $memberListing) {
				if (strpos($memberListing, 'CN=' . $group) !== false) {
					return $group;
				}
			}
		}
		return false;
	}

	/**
	 * Simply verifies that a user exists on the network.
	 * Does not verify password.
	 *
	 * @param string $username Username (first initial, last name)
	 * @return string The display name if user exists; false otherwise
	 */
	public function verifyUser($username)
	{
		$filter = 'samaccountname=' . $username;
        $attribs = array('displayname');
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);
		if (!$entries['count']) {
			return false;
		}
        return $entries[0]['displayname'][0];
	}

	/**
	 * Verifies that a user with the given fullname exists on the network.
	 *
	 * @param string       the user's display name in Active Directory (should be
	 *                     first and last name
	 * @return string|bool username (usually first initial plus last name) if
	 *                     the person exists or false if not found
	 */
	public function verifyFullName($fullname)
	{
		$filter = 'displayname=' . $fullname;
        $attribs = array('samaccountname');
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);

		if (!$entries['count']) {
			return false;
		}
		return $entries[0]['samaccountname'][0];
	}

	/**
	 * Gets a user's first and last name in one string.
	 *
	 * @param string $username Username to fetch fullname for
	 * @return string Display name, i.e., usually first and last name
	 */
	public function getFullName($username)
	{
		if (!$fullname = $this->verifyUser($username)) {
            throw new WrsGroup_Ldap_Exception($username);
        }
        return $fullname;
	}

	/**
	 * Gets display names of multiple users
	 *
	 * @param array $usernames Usernames to fetch fullnames for
	 * @return array List of display names with usernames as keys
	 */
    public function getFullNames($usernames)
    {
        $filter = '(|';
        foreach ($usernames as $username) {
            $filter .= '(samaccountname=' . $username . ')';
        }
        $filter .= ')';
        $attribs = array('displayname', 'samaccountname');
        $results = ldap_list($this->_conn, $this->_baseDn, $filter, $attribs);
        $info = ldap_get_entries($this->_conn, $results);

        $return = array();
        foreach ($info as $value) {
            if ($value['displayname'][0]) {
                $username = $value['samaccountname'][0];
                $return[$username] = $value['displayname'][0];
            }
        }
        return $return;
    }

    /**
     * Attempts to bind as the user
     *
     * @param string $username First initial plus last name
     * @param string $password The password
     * @return boolean True if bind is successful or false
     */
    public function verifyPassword($username, $password)
    {
        return @ldap_bind($this->_conn,
                          $this->_shortName . '\\' . $username,
                          $password);
    }

    /**
     * Gets the groups that a user is a member of
     *
     * @param string $username First initial plus last name (usually)
     * @return array List of groups
     */
    public function getUserGroups($username)
    {
        $filter = 'samaccountname=' . $username;
        $attribs = array('memberof');
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);

        return $this->_parseGroups($entries[0]['memberof']);
    }

    /**
     * Takes an array of "memberof" listings from LDAP and finds group names
     * that begin with CN=. Returns a unique array.
     * 
     * @param array $array
     * @return array Unique list of groups in given array 
     */
    protected function _parseGroups($array)
    {
        $groups = array();
        foreach ($array as $memberListing) {
            preg_match_all('/CN=([^,]+)/i', $memberListing, $result);
            foreach ($result[1] as $value) {
                $groups[] = $value;
            }
        }
        return array_unique($groups);
    }

    /**
     * Gets all groups in the domain, except for those designated as excluded
     * in the configuration file
     *
     * @return array List of groups sorted by group name
     */
    public function getAllGroups()
    {
        $filter = 'objectclass=group';
        $attribs = array('cn');
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);

        $groups = array();
        $exclude = array_flip($this->_excludedGroups);
        foreach ($entries as $array) {
            $groupName = $array['cn'][0];
            if (isset($exclude[$groupName])) {
                continue;
            }
            if ($groupName) {
                $groups[] = $groupName;
            }
        }
        sort($groups);
        return $groups;
    }

    /**
     * Gets all domain users (shows the displayname). Does not return disabled
     * users.
     *
     * @return array List of user data rows indexed by username
     */
    public function getAllUsers()
    {
        $filter = 'objectclass=user';
        $attribs = array(
            'samaccountname',
            'displayname',
            'useraccountcontrol',
            'mail'
        );
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);

        $users = array();
        $exclude = array_flip($this->_excludedUsers);
        foreach ($entries as $array) {
            $username = $array['samaccountname'][0];
            if (isset($exclude[$username])) {
                continue;
            }
            if ($array['useraccountcontrol'][0] != 512) {
                continue;
            }
            if (!isset($array['displayname'][0])) {
                continue;
            }
            $data = $this->_createUserRow($array);
            $users[$username] = $data;
        }
        return $users;
    }

    /**
     * Gets a user's e-mail address
     *
     * @param string $username The user's username
     * @return string The e-mail address
     */
    public function getUserEmail($username)
    {
        $filter = 'samaccountname=' . $username;
        $attribs = array('mail');
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);
        if (!$entries['count']) {
            throw new WrsGroup_Ldap_Exception($username);
        }
        return $entries[0]['mail'][0];
    }

    /**
     * Gets a user's fullname and e-mail address
     *
     * @param string $username The Active Directory username, i.e., emorgan
     * @return array An array with displayname and mail properties
     */
    public function getUserInfo($username)
    {
        $filter = 'samaccountname=' . $username;
        $attribs = array('samaccountname', 'displayname', 'mail');
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);
        if (!$entries['count']) {
            return null;
        }
        return $this->_createUserRow($entries[0]);
    }

    /**
     * Gets all active users who belong to the given group
     *
     * @param string $groupName The name of the group to search
     * @return array List of data rows indexed by username
     */
    public function getUsersInGroup($groupName)
    {
        $filter = 'objectclass=user';
        $attribs = array(
            'samaccountname',
            'useraccountcontrol',
            'memberof',
            'displayname',
            'mail'
        );
		$result = ldap_search($this->_conn, $this->_baseDn, $filter, $attribs);
		$entries = ldap_get_entries($this->_conn, $result);
        
        $memberUsers = array();
        foreach ($entries as $array) {
            $username = $array['samaccountname'][0];
            if ($array['useraccountcontrol'][0] != 512) {
                continue;
            }
            if (!isset($array['displayname'][0])) {
                continue;
            }
            if (!isset($array['memberof'])) {
                continue;
            }
            $groups = $this->_parseGroups($array['memberof']);
            if (in_array($groupName, $groups)) {
                $memberUsers[$username] = $this->_createUserRow($array);
            }
        }
        return $memberUsers;
    }

    /**
     * Given an LDAP entry for a user, returns a row of data about that user
     *
     * @param array $entry An LDAP entry
     * @return array A data row
     */
    protected function _createUserRow($entry)
    {
        $data = array(
            'samaccountname' => $entry['samaccountname'][0]
        );
        if (isset($entry['displayname'][0])) {
            $data['displayname'] = $entry['displayname'][0];
        } else {
            $data['displayname'] = '';
        }
        if (isset($entry['mail'][0])) {
            $data['mail'] = $entry['mail'][0];
        } else {
            $data['mail'] = '';
        }
        return $data;
    }
}
