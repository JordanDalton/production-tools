<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Auth_Adapter_LetsGelTest extends ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_mockRepo = $this->getMock(
            'WrsGroup_Model_Repository_LetsGelUser',
            array('getUser', 'isPasswordMatch'),
            array(''),
            '',
            false
        );
        $this->_adapter = new WrsGroup_Auth_Adapter_LetsGel();
        $this->_adapter->setUserRepo($this->_mockRepo);
    }

    /**
     * @expectedException Zend_Auth_Adapter_Exception
     */
    public function testNoUserRepoThrowsException()
    {
        $adapter = new WrsGroup_Auth_Adapter_LetsGel(array(
            'identity' => 'jmorgan',
            'credential' => 'abcdefghij',
        ));
        $adapter->authenticate();
    }

    public function testUserNotFound()
    {
        $this->_mockRepo->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue(null));

        $this->_adapter
            ->setIdentity('username')
            ->setCredential('password');
        $result = $this->_adapter->authenticate();
        $this->assertEquals(
            Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
            $result->getCode()
        );
    }

    public function testPasswordExpired()
    {
        $this->_mockRepo->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue(
                new WrsGroup_Model_LetsGelUser(array(
                    'username' => 'testUser',
                    'passwordExpired' => 1,
                ))
            ));

        $this->_adapter
            ->setIdentity('username')
            ->setCredential('password');
        $result = $this->_adapter->authenticate();
        $this->assertEquals(
            Zend_Auth_Result::FAILURE_UNCATEGORIZED,
            $result->getCode()
        );
    }

    public function testInactiveUser()
    {
        $mockUser = $this->getMock(
            'WrsGroup_Model_LetsGelUser',
            array('isActive'),
            array(''),
            '',
            false
        );
        $mockUser->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(false));

        $this->_mockRepo->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($mockUser));

        $this->_adapter
            ->setIdentity('username')
            ->setCredential('password');
        $result = $this->_adapter->authenticate();
        $this->assertEquals(
            Zend_Auth_Result::FAILURE_UNCATEGORIZED,
            $result->getCode()
        );
    }

    public function testPasswordIncorrect()
    {
        $mockUser = $this->getMock(
            'WrsGroup_Model_LetsGelUser',
            array('isActive', 'hashPassword'),
            array(''),
            '',
            false
        );
        $mockUser->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(true));
        $mockUser->expects($this->any())
            ->method('hashPassword')
            ->will($this->returnValue('abcdefghij'));

        $this->_mockRepo->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($mockUser));
        $this->_mockRepo->expects($this->any())
            ->method('isPasswordMatch')
            ->will($this->returnValue(false));

        $this->_adapter
            ->setIdentity('username')
            ->setCredential('password');
        $result = $this->_adapter->authenticate();
        $this->assertEquals(
            Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
            $result->getCode()
        );
    }

    public function testUserSuccessful()
    {
        $mockUser = $this->getMock(
            'WrsGroup_Model_LetsGelUser',
            array('isActive', 'hashPassword'),
            array(''),
            '',
            false
        );
        $mockUser->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(true));
        $mockUser->expects($this->any())
            ->method('hashPassword')
            ->will($this->returnValue('abcdefghij'));

        $this->_mockRepo->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($mockUser));
        $this->_mockRepo->expects($this->any())
            ->method('isPasswordMatch')
            ->will($this->returnValue(true));

        $this->_adapter
            ->setIdentity('username')
            ->setCredential('password');
        $result = $this->_adapter->authenticate();
        $this->assertEquals(
            Zend_Auth_Result::SUCCESS,
            $result->getCode()
        );
        $this->assertInstanceOf(
            'WrsGroup_Model_LetsGelUser',
            $result->getIdentity()
        );
    }
}
