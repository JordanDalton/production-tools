<?php
/**
 * UPS address validation service
 *
 * @package WrsGroup
 * @subpackage Ups
 * @author Eugene Morgan
 */
class WrsGroup_Ups_AddressValidator extends WrsGroup_Ups_Abstract
{
    const MISSING_FIELDS = 'missingFields';
    const NO_MATCH = 'noMatch';
    const UPS_ERROR = 'upsError';
    
    protected $_messageTemplates = array(
        self::MISSING_FIELDS => 'There were not enough required fields to validate the address.',
        self::NO_MATCH => 'No match could be found for the given address.',
        self::UPS_ERROR => 'Address validation error'
    );
    
    protected $_supportedCountries = array(
        'US',
        'UNITED_STATES',
        'PR',
        'PUERTO_RICO'
    );
    
    protected $_addressAttributes = array(
        'address1',
        'address2',
        'city',
        'stateProvince',
        'postalCode',
        'countrySymbol'
    );
    
    protected $_error;
    
    /**
     * @var DOMDocument
     */
    protected $_dom;
    
    /**
     * Constructor
     *
     * @param Zend_Config $upsConfig A config object with connection
     *     parameters
     */
    public function __construct(Zend_Config $upsConfig)
    {
        $dtdName = 'ADDRESSVALIDATEREQUEST';
        $dtdFile = 'AddressValidateRequest.dtd';
        parent::__construct($upsConfig, $dtdName, $dtdFile);
    }
    
    /**
     * Validate an address
     *
     * @param array $address Address values with keys
     * @return string XML data for corrected address or false if invalid
     */
    public function validate($address)
    {
        if (empty($address['countrySymbol'])) {
            $address['countrySymbol'] = 'US';
        }
        
        if ($address['countrySymbol'] != 'US' && $address['countrySymbol'] != 'PR') {
            return true;
        }
        
        if (!$this->_checkFields($address)) {
            $this->_setError(self::MISSING_FIELDS);
            return false;
        }
        
        $this->_generateXml($address);
        $xml = $this->_dom->saveXML();
        $this->_client->setRawData($xml, 'text/xml');
        $response = $this->_client->request('POST');
        
        if (!$addressData = $this->_processResponse($response->getBody())) {
            return false;
        }
        return $addressData;
    }
    
    /**
     * Checks if there are enough fields to send the validation request
     *
     * @param array $address Address data
     * @return boolean True if enough; else false
     */
    protected function _checkFields($address)
    {
        if (empty($address['countrySymbol'])) {
            return false;
        }
        
        if (empty($address['stateProvince']) && empty($address['postalCode'])) {
            return false;
        }
        
        if (empty($address['address1'])) {
            return false;
        }
        return true;
    }
    
    /**
     * Gets the message templates for outputting error messages
     *
     * @return array Message templates
     */
    public function getMessageTemplates()
    {
        return $this->_messageTemplates;
    }
    
    /**
     * Gets the error code of the current error message
     *
     * @return string An error code
     */
    public function getErrorCode()
    {
        return $this->_error;
    }
    
    /**
     * Sets the current error as the given error code
     *
     * @param string $errorCode The error code
     */
    protected function _setError($errorCode)
    {
        $this->_error = $errorCode;
    }
    
    protected function _generateXml($address)
    {
        $addressValidateRequest =
            $this->_dom->createElement('ADDRESSVALIDATEREQUEST');
        
        $login = $this->_createLoginElement();
        $addressValidateRequest->appendChild($login);
        
        $addressData = $this->_dom->createElement('ADDRESS_DATA');
        
        foreach ($this->_addressAttributes as $attr) {
            if (empty($address[$attr])) {
                $text = $this->_dom->createTextNode('');
            } else {
                $text = $this->_dom->createTextNode($address[$attr]);
            }
            $element = $this->_dom->createElement(strtoupper($attr));
            $element->appendChild($text);
            $addressData->appendChild($element);
        }
        
        $addressValidateRequest->appendChild($addressData);
        $this->_dom->appendChild($addressValidateRequest);
    }
    
    /**
     *
     *
     * @param string $xmlString The xml response as a string
     * @return string The corrected address if only one candidate; false otherwise
     */
    protected function _processResponse($xmlString)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xmlString);
        $addressData = $dom->getElementsByTagName('ADDRESS_DATA')->item(0);
        $errorCode = $addressData->getElementsByTagName('ERRORCODE')->item(0);
        
        // Return false if there is an error
        if ($errorCode->textContent) {
            $msg = $addressData->getElementsByTagName('ERRORDESCRIPTION')
                ->item(0)->textContent;
            $this->_messageTemplates[self::UPS_ERROR] .= ': ' . $msg;
            $this->_setError(self::UPS_ERROR);
            return false;
        }
        
        // Count number of candidate addresses
        $candidates = $addressData->getElementsByTagName('CANDIDATE_ADDRESS');
        if (count($candidates) > 1) {
            // do something
            
            return false;
        }
        
        // If there is one candidate address, return that info
        // (Evaluates to true)
        return $dom->saveXML($candidates->item(0));
    }
}
