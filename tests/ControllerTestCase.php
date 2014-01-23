<?php
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

abstract class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var Zend_Application
     */
	protected $_application;
	
    /**
     * Gets the container used for dependency injection
     *
     * @return Yadif_Container
     */
    protected function _getContainer()
    {
        return $this->_application->getBootstrap()->getContainer();
    }

    protected function _loginAsUser($username)
    {
        $auth = Zend_Auth::getInstance();
        $auth->getStorage()->write($username);
        $this->assertTrue($auth->hasIdentity());
    }

 	public function setUp()
    {  	
		$this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
	
	public function appBootstrap()
	{
	  	$this->_application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
	  	$this->_application->bootstrap();
        $front = Zend_Controller_Front::getInstance();
        $front->setParam('bootstrap', $this->_application->getBootstrap());
	}
}
