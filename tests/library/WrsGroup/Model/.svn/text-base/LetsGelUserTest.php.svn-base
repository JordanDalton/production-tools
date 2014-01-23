<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_LetsGelUserTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_LetsGelUser
     */
    protected $_letsGelUser;

    public function setUp()
    {
        parent::setUp();
        $this->_letsGelUser = new WrsGroup_Model_LetsGelUser(array(
            'first_name' => 'Joe',
            'last_name' => 'Henson',
            'email_address' => 'joehenson@test.com',
            'username' => 'jhenson',
            'active' => 1,
        ));
    }

    public function testGetDisplayName()
    {
        $this->assertEquals(
            'Joe Henson',
            $this->_letsGelUser->getDisplayName()
        );
    }

    public function testGetFirstName()
    {
        $this->assertEquals(
            'Joe',
            $this->_letsGelUser->getFirstName()
        );
    }

    public function testGetLastName()
    {
        $this->assertEquals(
            'Henson',
            $this->_letsGelUser->getLastName()
        );
    }

    public function testGetUsername()
    {
        $this->assertEquals(
            'jhenson',
            $this->_letsGelUser->getUsername()
        );
    }

    public function testGetEmailAddress()
    {
        $emailAddress = $this->_letsGelUser->getEmailAddress();
        $this->assertType('WrsGroup_Model_EmailAddress', $emailAddress);
        $this->assertEquals(
            'joehenson@test.com',
            $emailAddress->address
        );
    }

    public function testIsActive()
    {
        $user = $this->_letsGelUser;
        $this->assertTrue($user->isActive());
        $this->assertType(
            PHPUnit_Framework_Constraint_IsType::TYPE_BOOL,
            $user->isActive()
        );
    }

    public function testDisable()
    {
        $user = $this->_letsGelUser;
        $this->assertTrue($user->isActive());
        $user->disable();
        $this->assertFalse($user->isActive());
    }

    public function testGetOrganization()
    {
        $org = $this->_letsGelUser->getOrganization();
        $this->assertEquals("Let's Gel, Inc.", $org);
    }

    public function testGetUserType()
    {
        $userType = $this->_letsGelUser->getUserType();
        $this->assertEquals(WrsGroup_Model_UserAbstract::LETS_GEL, $userType);
    }

    public function testGetUserGroups()
    {
        $this->assertNull($this->_letsGelUser->getUserGroups());
    }

    /**
     * @expectedException Exception
     */
    public function testHashPasswordThrowsExceptionIfNoSaltGenerator()
    {
        $this->_letsGelUser->hashPassword();
    }

    /**
     * @expectedException Exception
     */
    public function testHashPasswordThrowsExceptionIfNoHashGenerator()
    {
        $this->_letsGelUser->setSaltGenerator(new WrsGroup_Salt_Int64());
        $this->_letsGelUser->hashPassword();
    }

    public function testHashPassword()
    {
        $this->_letsGelUser->salt = '1234567890';
        $this->_letsGelUser->setHashGenerator(new WrsGroup_Hash_Sha1());
        $this->_letsGelUser->password = 'abcdefghij';
        $this->_letsGelUser->hashPassword();
        $this->assertEquals(
            '787d559439cfd927780996d2c78f635acca40c37',
            $this->_letsGelUser->password
        );
    }
}
