<?php
require_once 'Zend/Application.php';
require_once 'PHPUnit/Framework/TestCase.php';

abstract class ModelTestCase extends PHPUnit_Framework_TestCase
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

 	public function setUp()
    {  	
		$this->appBootstrap();
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
