<?php
/**
 * Class for validating that two strings match; set up by default
 * for use with validating that a password confirmation field matches
 * the password field
 *
 * @see Zend_Validate
 * @category WrsGroup
 * @package Validate
 * @author Eugene Morgan
 */
class WrsGroup_Validate_Match extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    /**
     * @var string
     */
    protected $_fieldToMatch = 'password';

    /**
     * Setter for field to match
     *
     * @param string $fieldName The name of the field
     */
    public function setFieldToMatch($fieldName)
    {
        $this->_fieldToMatch = $fieldName;
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
     * Set form state from config object
     *
     * @param  Zend_Config $config
     * @return Zend_Form
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Set form state from options array
     *
     * @param  array $options
     * @return Zend_Form
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
    }

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context[$this->_fieldToMatch])
                && ($value == $context[$this->_fieldToMatch]))
            {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}