<?php
/**
 * Class to generate a 64-bit integer salt
 *
 * The output is a string so as not to cause issues on 32-bit systems. 
 * However, it can be saved into a database in an integer field (if the 
 * field is big enough).
 * 
 * @category WrsGroup
 * @package Salt
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Salt_Int64 implements WrsGroup_SaltInterface
{
    /**
     * Gets the salt as a string representation of an integer up to 64 bits
     * in size.
     *
     * @see WrsGroup_SaltInterface::getSalt()
     * @return string
     */
    public function getSalt()
    {
        // Generate a 32-bit integer
        $integer1 = mt_rand(10000000, 2147483647);

        // Generate another 32-bit integer
        $integer2 = mt_rand(10000000, 2147483647);

        // Join the integers
        $string = $integer1 . $integer2;

        // Drop last two characters to ensure will be under 64-bit limit
        $string = substr($string, 0, strlen($string) - 2);

        return $string;
    }
}
