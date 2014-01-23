<?php
interface WrsGroup_Model_Repository_UserInterface
{
    /**
     * Gets a user
     *
     * @param string $username The username
     * @param bool $useCache Whether to use cache to retrieve the result;
     *  default is no
     * @return WrsGroup_Model_UserAbstract A user object
     */
    public function getUser($username, $useCache = false);

    /**
     * Sets the cache object
     *
     * @param Zend_Cache_Core $cache A cache object
     */
    public function setCache($cache);
}