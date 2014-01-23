<?php
class WrsGroup_Ldap_Exception extends Exception
{
    /**
     * Constructor
     *
     * @param string $username The LDAP username
     */
    public function __construct($username)
    {
        parent::__construct('Username ' . $username . ' could not be found ' .
                            'in the LDAP directory.');
    }
}