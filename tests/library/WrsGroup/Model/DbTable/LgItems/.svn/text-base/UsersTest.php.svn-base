<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class Model_DbTable_LgItems_UsersTest extends ModelTestCase
{
	public function testGetUser()
	{
        $table = $this->_getContainer()->getComponent('usersTable');
        $row = $table->getUser('rhenson');
        $this->assertType('Zend_Db_Table_Row_Abstract', $row);
        $this->assertEquals('rhenson', $row->username);
        $this->assertEquals('Henson', $row->last_name);
	}

    public function testExists()
    {
        $table = $this->_getContainer()->getComponent('usersTable');
        $this->assertTrue($table->exists('rhenson'));
        $this->assertFalse($table->exists('dasfasdfbzxcv'));
    }

    public function testGetUsers()
    {
        $table = $this->_getContainer()->getComponent('usersTable');
        $users = $table->getUsers();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $users);
        $this->assertGreaterThan(0, count($users));
    }

    public function testVerifyPassword()
    {
        $table = $this->_getContainer()->getComponent('usersTable');
        $result = $table->verifyPassword('nonexistentuser', 'somepass');
        $this->assertFalse($result);
    }
}
