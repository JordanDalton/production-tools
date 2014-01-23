<?php
/**
 * Interface for classes that generate salts to implement
 * 
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
interface WrsGroup_SaltInterface
{
    /**
     * Gets salt generated by the salt generator
     * 
     * @return string The salt string
     */
    public function getSalt();
}
