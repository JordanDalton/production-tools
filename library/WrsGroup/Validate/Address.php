<?php
/**
 * Class to validate an address. This validator should always be attached to
 * the address1 field.
 *
 * @category WrsGroup
 * @package Validate
 * @author Eugene Morgan
 */
class WrsGroup_Validate_Address extends Zend_Validate_Abstract
{
    /**
     * Constructor
     *
     * @param Zend_Config $config A config object with properties telling
     *     the form element names of the different parts of the address
     */
    public function __construct(Zend_Config $config)
    {
        $this->_elementMap = $config;
    }
    
    /**
     * (non-PHPdoc)
     * @see library/Zend/Validate/Zend_Validate_Interface#isValid()
     */
    public function isValid($value, $context = null)
    {
        // First, clear the registry so there's no way address data
        // from a previous form can be pulled by the filter later
        Zend_Registry::set('WrsGroup_Filter_Address_Data', '');
        
        if (!is_array($context)) {
            throw new Exception('Context, i.e., the rest of the form, was ' .
            	'not passed successfully.');
        }
        
        $config = Zend_Registry::get('config');
        $validator = new WrsGroup_Ups_AddressValidator($config->ups);
        
        // Set up fields for address validation
        $address = array();
        foreach ($this->_elementMap as $key => $value) {
            if (isset($context[$value])) {
                $address[$key] = $context[$value];
            }
        }
        
        if (!$newAddress = $validator->validate($address)) {
            $this->_messageTemplates = $validator->getMessageTemplates();
            $errorCode = $validator->getErrorCode();
            $this->_error($errorCode);
            return false;
        }
        
        // Save the new address in the registry for use by the filter
        Zend_Registry::set('WrsGroup_Filter_Address_Data', $newAddress);
        return true;
    }
}