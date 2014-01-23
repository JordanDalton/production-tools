<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_PasswordGeneratorTest extends ModelTestCase
{
    public function testLowerOnly()
    {
        $options = new Zend_Config(array(
            'lower' => true,
            'length' => 10
        ));
        $generator = new WrsGroup_PasswordGenerator($options);
        $password = $generator->getPassword();
        $this->assertEquals(10, strlen($password));
        $this->assertRegExp('/^[a-z]+$/', $password);
    }

    public function testUpperOnly()
    {
        $options = new Zend_Config(array(
            'upper' => true,
            'lower' => false,
            'length' => 12
        ));
        $generator = new WrsGroup_PasswordGenerator($options);
        $password = $generator->getPassword();
        $this->assertEquals(12, strlen($password));
        $this->assertRegExp('/^[A-Z]+$/', $password);
    }

    public function testUpperAndLower()
    {
        $options = new Zend_Config(array(
            'upper' => true,
            'lower' => true,
            'length' => 8
        ));
        $generator = new WrsGroup_PasswordGenerator($options);
        $password = $generator->getPassword();
        $this->assertEquals(8, strlen($password));
        $this->assertRegExp('/^[A-Za-z]+$/', $password);
    }

    public function testSymbolOnly()
    {
        $options = new Zend_Config(array(
            'symbol' => true,
            'lower' => false,
            'length' => 9
        ));
        $generator = new WrsGroup_PasswordGenerator($options);
        $password = $generator->getPassword();
        $this->assertEquals(9, strlen($password));
        $this->assertRegExp('/^[\!\@\#\$\%\^\&\*\(\)\:]+$/', $password);
    }

    public function testNumericOnly()
    {
        $options = new Zend_Config(array(
            'numeric' => true,
            'lower' => false,
            'length' => 11,
        ));
        $generator = new WrsGroup_PasswordGenerator($options);
        $password = $generator->getPassword();
        $this->assertEquals(11, strlen($password));
        $this->assertRegExp('/^[0-9]+$/', $password);
    }

    /**
     * @expectedException Exception 
     */
    public function testGetPasswordThrowsExceptionIfWronglyConfigured()
    {
        $options = array(
            'numeric' => false,
            'lower' => false,
            'symbols' => false,
            'upper' => false,
        );
        $generator = new WrsGroup_PasswordGenerator($options);
        $password = $generator->getPassword();
    }

    /**
     * @expectedException Exception 
     */
    public function testSetOptionsThrowsExceptionWithUnknownKey()
    {
        $options = array(
            'someKey' => true,
        );
        $generator = new WrsGroup_PasswordGenerator($options);
    }
}
