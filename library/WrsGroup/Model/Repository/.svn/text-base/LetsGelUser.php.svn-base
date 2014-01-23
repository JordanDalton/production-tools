<?php
/**
 * Repository class for Let's Gel users
 *
 * @package Model
 * @subpackage Repository
 * @author Eugene Morgan
 */
class WrsGroup_Model_Repository_LetsGelUser implements
    WrsGroup_Model_Repository_UserInterface
{
    /**
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @var WrsGroup_HashInterface
     */
    protected $_hashGenerator;

    /**
     * @var string
     */
    protected $_lgSchema = 'lgitems';

    /**
     * @var WrsGroup_SaltInterface
     */
    protected $_saltGenerator;

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
     * Sets the hash generator
     *
     * @param WrsGroup_HashInterface $hashGenerator
     */
    public function setHashGenerator($hashGenerator)
    {
        $this->_hashGenerator = $hashGenerator;
    }

    /**
     * Sets the Let's Gel database schema
     *
     * @param string $schema The schema name
     */
    public function setLgSchema($schema)
    {
        $this->_lgSchema = $schema;
    }

    /**
     * Sets the salt generator
     *
     * @param WrsGroup_SaltInterface $saltGenerator
     */
    public function setSaltGenerator($saltGenerator)
    {
        $this->_saltGenerator = $saltGenerator;
    }

    /**
     * Constructor
     * 
     * @param Zend_Db_Adapter_Abstract $db An instance of the db adapter
     */
    public function __construct($db)
    {
        $this->_db = $db;
    }

    /**
     * Gets a Let's Gel user
     *
     * @param string $username The username
     * @param bool $useCache Whether to use cache to retrieve the result;
     *                       default is no
     * @return WrsGroup_Model_LetsGelUser A user object or null if fails
     */
    public function getUser($username, $useCache = false)
    {
        // If no cache, get user straight from database
        if (!$this->_cache || !$useCache) {
            $user = $this->_getUserFromDb($username);

            // Set salt and hash generators
            if ($user) {
                $user->setSaltGenerator($this->_saltGenerator);
                $user->setHashGenerator($this->_hashGenerator);
            }

            return $user;
        }

        // Attempt to retrieve user from cache
        $cacheId = WrsGroup_Model_UserAbstract::LETS_GEL . '_' . $username;
        $user = $this->_cache->load($cacheId);

        // If not in cache, retrieve user and store in cache
        if (!$user) {
            $user = $this->_getUserFromDb($username);

            // Set salt and hash generators
            if ($user) {
                $user->setSaltGenerator($this->_saltGenerator);
                $user->setHashGenerator($this->_hashGenerator);
            }
            $this->_cache->save($user, $cacheId);
        }
        return $user;
    }

    /**
     * Retrieves user from database
     *
     * @param string $username The username
     * @return WrsGroup_Model_LetsGelUser A user object
     */
    protected function _getUserFromDb($username)
    {
        $select = $this->_db->select()
            ->from(
                array('u' => 'users'),
                array(
                    'username' => 'TRIM(username)',
                    'first_name',
                    'last_name',
                    'active',
                    'password_temp',
                    'password_expired',
                    'salt',
                    'email_address',
                ),
                $this->_lgSchema
            )
            ->where('username = ?', $username);
        $stmt = $select->query();
        $row = $stmt->fetchObject();
        if (!$row || !$row->username) {
            return null;
        }
        return new WrsGroup_Model_LetsGelUser($row);
    }

    /**
     * Verifies whether the given password matches what's stored in the
     * database for the given username.
     *
     * @param WrsGroup_Model_LetsGelUser $user A user object 
     * @return boolean Whether the password matched
     */
    public function isPasswordMatch($user)
    {
        $select = $this->_db->select()
            ->from(
                array('u' => 'users'),
                'password',
                $this->_lgSchema
            )
            ->where('username = ?', $user->username);
        $stmt = $select->query();
        $row = $stmt->fetchObject();
        if ($row->password == $user->password) {
            return true;
        }
        return false;
    }
}
