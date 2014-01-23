<?php
/**
 * Domain object for Let's Gel user
 *
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_LetsGelUser extends WrsGroup_Model_UserAbstract
    implements WrsGroup_Model_UserInterface
{
    protected $_data = array(
        'firstName' => null,
        'lastName' => null,
        'passwordTemp' => null,
        'passwordExpired' => null,
        'salt' => null,
    );

    /**
     * @var WrsGroup_HashInterface
     */
    protected $_hashGenerator;

    /**
     * @var WrsGroup_SaltInterface
     */
    protected $_saltGenerator;

    /**
     * Gets the user's name in format to be displayed
     *
     * @return string The name to display
     */
    public function getDisplayName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Gets the e-mail address
     *
     * @return WrsGroup_Model_EmailAddress The e-mail address
     */
    public function getEmailAddress() 
    {
        if (!$this->_emailAddress instanceof WrsGroup_Model_EmailAddress) {
            $address = $this->emailAddress;
            $this->_emailAddress = new WrsGroup_Model_EmailAddress(array(
                'address' => $address,
                'name' => $this->getDisplayName()
            ));
        }
        return $this->_emailAddress;
    }

    /**
     * Gets the first name
     *
     * @return string The user's first name
     */
    public function getFirstName() 
    {
        return $this->firstName;
    }

    /**
     * Gets the last name
     *
     * @return string The user's last name
     */
    public function getLastName() 
    {
        return $this->lastName;
    }

    /**
     * Gets the username
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Groups are currently not available for Let's Gel users
     *
     * @return WrsGroup_Model_RecordSet A record set of group objects
     */
    public function getUserGroups()
    {
        return null;
    }

    /**
     * Tells whether the user is active
     *
     * @return bool Whether the user is active
     */
    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * Disables the user
     *
     */
    public function disable()
    {
        $this->active = 0;
    }

    /**
     * Gets the organization name (Let's Gel)
     *
     * @return string The organization name
     */
    public function getOrganization()
    {
        return 'Let\'s Gel, Inc.';
    }

    /**
     * Gets the user type
     *
     * @return string The user type
     */
    public function getUserType()
    {
        return self::LETS_GEL;
    }

    /**
     * Hashes the password stored in the object
     *
     */
    public function hashPassword()
    {
        if (!$this->salt) {
            if (!$this->_saltGenerator) {
                throw new Exception('Salt generator has not been set.');
            }
            $this->salt = $this->_saltGenerator->getSalt();
        }
        if (!$this->_hashGenerator) {
            throw new Exception('Hash generator has not been set.');
        }
        $this->password = $this->_hashGenerator->getHash(
            $this->password,
            $this->salt
        );
    }
}
