<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Validate_MatchTest extends ModelTestCase
{
    public function testIsValid()
    {
        $context = array('password' => 'abc123');
        $validator = new WrsGroup_Validate_Match();
        $this->assertTrue($validator->isValid('abc123', $context));
        $this->assertFalse($validator->isValid('abc456', $context));
    }

    public function testIsValidWithDifferentField()
    {
        $context = array('someField' => 'abc123');
        $validator = new WrsGroup_Validate_Match(
            new Zend_Config(array('fieldToMatch' => 'someField'))
        );
        $this->assertTrue($validator->isValid('abc123', $context));
        $this->assertFalse($validator->isValid('abc456', $context));
    }
}
