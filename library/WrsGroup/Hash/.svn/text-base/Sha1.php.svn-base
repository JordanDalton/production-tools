<?php
/**
 * Class to generate an SHA-1 hash
 *
 * @category WrsGroup
 * @package Hash
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Hash_Sha1 implements WrsGroup_HashInterface
{
    /**
     * Generates a secure SHA-1 hash of a string
     *
     * Salt can be optionally provided.
     *
     * @param string $string The string to hash
     * @param string $salt OPTIONAL The salt to use for greater security
     * @see WrsGroup_HashInterface::getHash()
     * @return string The hash of the string
     */
    public function getHash($string, $salt = null)
    {
        if ($salt) {
            return sha1($string . $salt);
        }
        return sha1($string);
    }
}
