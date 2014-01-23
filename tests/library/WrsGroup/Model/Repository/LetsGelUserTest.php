<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');
require_once realpath(APPLICATION_PATH . '/../tests/DbFixtureHelper.php');

class WrsGroup_Model_Repository_LetsGelUserTest extends ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $db = $this->_getContainer()->db;

        $lgItemsSchema = 'lgitemsts';
        $file = dirname(__FILE__) . '/fixture/lgitems-users.sql';
        $helper = new DbFixtureHelper($file, $db, $lgItemsSchema);
        $helper->clearAll();
        $helper->insertAll();

        $this->_repo = new WrsGroup_Model_Repository_LetsGelUser($db);
        $this->_repo->setHashGenerator(new WrsGroup_Hash_Sha1());
        $this->_repo->setSaltGenerator(new WrsGroup_Salt_Int64());
        $this->_repo->setLgSchema($lgItemsSchema);
    }

    public function testGetUserWithoutCache()
    {
        $user = $this->_repo->getUser('jmorgan');
        $this->assertInstanceOf('WrsGroup_Model_LetsGelUser', $user);
        $this->assertEquals('jmorgan', $user->getUsername());
        $this->assertEquals('Joseph', $user->getFirstName());
    }

    public function testGetUserWithEmptyCache()
    {
        // Set up empty cache
        $mockEmptyCache = $this->getMock(
            'Zend_Cache_Core',
            array('load', 'save')
        );
        $mockEmptyCache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));
        $mockEmptyCache->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));
        $this->_repo->setCache($mockEmptyCache);

        $user = $this->_repo->getUser('jmorgan', true);
        $this->assertInstanceOf('WrsGroup_Model_LetsGelUser', $user);
        $this->assertEquals('jmorgan', $user->getUsername());
        $this->assertEquals('Joseph', $user->getFirstName());
    }

    public function testGetUserWithFullCache()
    {
        $user = new WrsGroup_Model_LetsGelUser(array(
            'username' => 'cuser',
        ));
        $mockCache = $this->getMock(
            'Zend_Cache_Core',
            array('load', 'save')
        );
        $mockCache->expects($this->any())
            ->method('load')
            ->will($this->returnValue($user));
        $mockCache->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));
        $this->_repo->setCache($mockCache);

        $user = $this->_repo->getUser('jmorgan', true);
        $this->assertInstanceOf('WrsGroup_Model_LetsGelUser', $user);
        $this->assertEquals('cuser', $user->getUsername());
    }

    public function testUserWhenNoUserFound()
    {
        $user = $this->_repo->getUser('xmartinez');
        $this->assertNull($user);
    }

    public function testIsPasswordMatchWhenTrue()
    {
        $user = new WrsGroup_Model_LetsGelUser(array(
            'username' => 'jmorgan',
            'password' => '787d559439cfd927780996d2c78f635acca40c37',
        ));
        $this->assertTrue($this->_repo->isPasswordMatch($user, $user->password));
    }

    public function testIsPasswordMatchWhenFalse()
    {
        $user = new WrsGroup_Model_LetsGelUser(array(
            'username' => 'jmorgan',
            'password' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ));
        $this->assertFalse($this->_repo->isPasswordMatch($user, $user->password));
    }
}
