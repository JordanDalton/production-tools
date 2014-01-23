<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Salt_Int64Test extends ModelTestCase
{
    public function testGetSalt()
    {
        $saltGenerator = new WrsGroup_Salt_Int64();

        for ($i = 0; $i < 10; $i++) {
            $salt = $saltGenerator->getSalt();

            // Test that the salt has only numeric characters
            $this->assertRegExp('/^[\d]+$/', $salt);

            // Test that the salt is not greater than a 64-bit integer
            $this->assertLessThan(strlen('9223372036854775807'), strlen($salt));
        }
    }
}
