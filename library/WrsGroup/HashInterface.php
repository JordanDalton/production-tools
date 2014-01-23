<?php
/**
 * Interface for classes that generate hashes to implement
 * 
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
interface WrsGroup_HashInterface
{
    /**
     * Gets hash generated by the hash generator
     * 
     * @return string The hash string
     */
    public function getHash($string, $salt = null);
}