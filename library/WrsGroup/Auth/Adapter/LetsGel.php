<?php
/**
 * Adapter for authenticating Let's Gel users
 *
 * @category WrsGroup
 * @package Auth
 * @subpackage Adapter
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Auth_Adapter_LetsGel implements Zend_Auth_Adapter_Interface
{
    /**
     * @var WrsGroup_Model_Repository_LetsGelUser
     */
    protected $_userRepo;

    /**
     * $_identity - Identity value
     *
     * @var string
     */
    protected $_identity = null;

    /**
     * $_credential - Credential value
     *
     * @var string
     */
    protected $_credential = null;

    /**
     * Constructor
     * 
     * @param array $options Array of options with keys corresponding to 
     *                       setter functions
     */
    public function __construct($options = array())
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            $this->$method($value);
        }
    }

    /**
     * Sets the user repository
     *
     * @param WrsGroup_Model_Repository_LetsGelUser $userRepo
     * @return WrsGroup_Auth_Adapter_LetsGel Provides a fluent interface
     */
    public function setUserRepo($userRepo)
    {
        $this->_userRepo = $userRepo;
        return $this;
    }

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return WrsGroup_Auth_Adapter_LetsGel Provides a fluent interface
     */
    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }

    /**
     * setCredential() - set the credential value to be used, optionally can specify a treatment
     * to be used, should be supplied in parameterized form, such as 'MD5(?)' or 'PASSWORD(?)'
     *
     * @param  string $credential
     * @return WrsGroup_Auth_Adapter_LetsGel Provides a fluent interface
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    /**
     * Attempts to authenticate a user
     *
     * @see Zend_Auth_Adapter_Interface
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        if (!$this->_userRepo) {
            throw new Zend_Auth_Adapter_Exception('User repository must be set.');
        }

        // Attempt to retrieve the user, along with password salt
        $user = $this->_userRepo->getUser($this->_identity);
        if (!$user) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                $this->_identity,
                array('Incorrect username or password')
            );
        }

        // Is the password expired or is the user disabled?
        if ($user->passwordExpired || !$user->isActive()) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                $this->_identity,
                array('Your account has been disabled or '
                    . 'your password has expired.')
            );
        }

        // Hash the user's password
        $user->password = $this->_credential;
        $user->hashPassword();

        $verified = $this->_userRepo->isPasswordMatch($user);

        if ($verified) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::SUCCESS,
                $user,
                array('Authentication successful')
            );
        }
        return new Zend_Auth_Result(
            Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
            $this->_identity,
            array('Incorrect username or password')
        );
    }
}
