<?php
/**
 * Service stub class for working with UPS ConnectShip web service
 *
 * @category WrsGroup
 * @package Ups
 * @author Eugene Morgan
 */
abstract class WrsGroup_Ups_Abstract
{
    /**
     * @var Zend_Config_Ini
     */
    protected $_config;
    
    /**
     * @var Zend_Http_Client
     */
    protected $_client;
    
    /**
     * @var DOMDocument
     */
    protected $_dom;
    
    /**
     * Constructor
     *
     * @param Zend_Config $upsConfig A config object with connection parameters
     * @param string $dtdName The DTD name
     * @param string $dtdFile The filename of the DTD
     */
    public function __construct (Zend_Config $upsConfig, $dtdName, $dtdFile)
    {
        $this->_config = $upsConfig;
        $this->_client = new Zend_Http_Client($upsConfig->uri);
        
        $impl = new DOMImplementation();
        $url = $upsConfig->dtdDirectory . '/' . $dtdFile;
        $dtd = $impl->createDocumentType($dtdName, '', $url);
        $this->_dom = $impl->createDocument('', '', $dtd);
        $this->_dom->encoding = 'UTF-8';
    }
    
    /**
     * Creates a login element
     *
     * @return DOMElement A 'login' element
     */
    protected function _createLoginElement()
    {
        $text = $this->_dom->createTextNode($this->_config->username);
        $loginId = $this->_dom->createElement('LOGINID');
        $loginId->appendChild($text);
        
        $text = $this->_dom->createTextNode($this->_config->password);
        $password = $this->_dom->createElement('PASSWORD');
        $password->appendChild($text);
        
        $login = $this->_dom->createElement('LOGIN');
        $login->appendChild($loginId);
        $login->appendChild($password);
        
        return $login;
    }
}