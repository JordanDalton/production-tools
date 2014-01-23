<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Hash_Sha1Test extends ModelTestCase
{
    public function testGetHashWithoutSalt()
    {
        $hashGenerator = new WrsGroup_Hash_Sha1();
        $hash = $hashGenerator->getHash('abcdefghij');
        $this->assertEquals(40, strlen($hash));
        $this->assertEquals('d68c19a0a345b7eab78d5e11e991c026ec60db63', $hash);
    }

    public function testGetHashWithSalt()
    {
        $hashGenerator = new WrsGroup_Hash_Sha1();
        $hash = $hashGenerator->getHash('abcdefghij', '1234567890');
        $this->assertEquals(40, strlen($hash));
        $this->assertEquals('787d559439cfd927780996d2c78f635acca40c37', $hash);
    }
}
