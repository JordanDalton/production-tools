<?php
/**
 * Repository class for users
 *
 * @package Model
 * @subpackage Repository
 * @author Eugene Morgan
 */
class WrsGroup_Model_Repository_WrsUser implements
    WrsGroup_Model_Repository_UserInterface
{
    /**
     * @var Zend_Ldap
     */
    protected $_ldap;

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * Sets the LDAP object to be used for retrieving the user data
     *
     * @param Zend_Ldap $ldap
     */
    public function setLdap($ldap)
    {
        $this->_ldap = $ldap;
    }

    /**
     * Sets the cache object
     *
     * @param Zend_Cache_Core $cache
     */
    public function setCache($cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Gets a WRS Group user
     *
     * @param string $username The sAMAccountName in Active Directory (i.e.,
     *  emorgan)
     * @param bool $useCache Whether to use cache to retrieve the result;
     *  default is no
     * @return WrsGroup_Model_WrsUser A user object or null if fails
     */
    public function getUser($username, $useCache = false)
    {
        // If no cache, get user straight from LDAP
        if (!$this->_cache || !$useCache) {
            return $this->_getUserFromLdap($username);
        }

        // Attempt to retrieve user from cache
        $cacheId = WrsGroup_Model_UserAbstract::WRS_GROUP . '_' . $username;
        $user = $this->_cache->load($cacheId);

        // If not in cache, retrieve user and store in cache
        if (!$user) {
            $user = $this->_getUserFromLdap($username);
            $this->_cache->save($user, $cacheId);
        }
        return $user;
    }

    /**
     * Retrieves user from LDAP directory
     *
     * @param string $username The sAMAccountName in Active Directory
     * @return WrsGroup_Model_WrsUser A user object
     */
    protected function _getUserFromLdap($username)
    {
        $filter = Zend_Ldap_Filter::equals('sAMAccountName', $username);
        $collection = $this->_ldap->search($filter);
        $factory = new WrsGroup_Model_Factory_WrsUser();
        return $factory->create(array(
            'username' => $username,
            'ldapData' => $collection->getFirst()
        ));
    }
}