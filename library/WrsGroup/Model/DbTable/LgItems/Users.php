<?php
/**
 * Class for Let's Gel users table
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_LgItems_Users
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'users';
	protected $_schema = 'lgitems';
    protected $_primary = 'username';

    /**
     * Gets a Let's Gel user by username
     *
     * @param string $username The username
     * @return Zend_Db_Table_Row_Abstract A row of data
     */
    public function getUser($username)
    {
        $select = $this->select()
            ->from($this, array(
                'TRIM(username) AS username',
                'first_name',
                'last_name',
                'active',
                'password_temp',
                'password_expired',
                'salt',
                'email_address',
            ))
            ->where('username = ?', $username);
        return $this->fetchRow($select);
    }

    /**
     * Tells whether a username exists
     *
     * @param string $username The username
     * @return bool
     */
    public function exists($username)
    {
        $select = $this->select()
            ->from($this, 'COUNT(*) AS cnt')
            ->where('username = ?', $username);
        $row = $this->fetchRow($select);
        if (!$row->cnt) {
            return false;
        }
        return true;
    }

    /**
     * Gets data about all users in the table
     *
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getUsers()
    {
        $select = $this->select()
            ->from($this, array(
                'TRIM(username) AS username',
                'first_name',
                'last_name',
                'active',
                'password_temp',
                'password_expired',
                'email_address',
            ))
            ->order(array('first_name', 'last_name'));
        return $this->fetchAll($select);
    }

    /**
     * Verifies a user's password
     *
     * @param string $username The username
     * @param string $password The password
     * @return boolean Whether or not password matched
     */
    public function verifyPassword($username, $password)
    {
        $select = $this->select()
            ->from($this, 'COUNT(*) AS cnt')
            ->where('username = ?', $username)
            ->where('password = ?', $password);
        $row = $this->fetchRow($select);
        if ($row->cnt) {
            return true;
        }
        return false;
    }
}
