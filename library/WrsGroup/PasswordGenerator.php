<?php
/**
 * Password generator class
 *
 * @category WrsGroup
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_PasswordGenerator
{
    protected $_lower = true;

    protected $_upper;

    protected $_numeric;

    protected $_symbol;

    /**
     * @var integer
     */
    protected $_length = 8;

    public function setLower($lower)
    {
        $this->_lower = $lower;
    }

    public function setUpper($upper)
    {
        $this->_upper = $upper;
    }

    public function setNumeric($numeric)
    {
        $this->_numeric = $numeric;
    }

    public function setSymbol($symbol)
    {
        $this->_symbol = $symbol;
    }

    public function setLength($length)
    {
        $this->_length = $length;
    }

    /**
     * Constructor
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set object state from config object
     *
     * @param  Zend_Config $config
     * @return WrsGroup_PasswordGenerator
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Set object state from options array
     *
     * @param  array $options
     * @return WrsGroup_PasswordGenerator
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $normalized = ucfirst($key);
            $method = 'set' . $normalized;
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new Exception('Unknown option: ' . $key);
            }
        }
        return $this;
    }

    /**
     * Gets a generated password based on options set in class
     *
     * @return string The generated password
     */
    public function getPassword()
    {
        // Set up characters to work with
        $characters = '';
        if ($this->_lower) {
            $characters .= 'abcdefghjklmnpqrstuvwxyz';
        }
        if ($this->_upper) {
            $characters .= 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        }
        if ($this->_numeric) {
            $characters .= '23456789';
        }
        if ($this->_symbol) {
            $characters .= '!@#$%^&*():';
        }
        if (!$characters) {
            throw new Exception('Password generator must be configured to ' .
                'accept at least one of lower case, upper case, numbers, ' .
                'or symbols');
        }

        $string = '';
        for ($i = 0; $i < $this->_length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
}
