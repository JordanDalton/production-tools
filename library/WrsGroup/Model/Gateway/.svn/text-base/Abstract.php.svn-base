<?php
/**
 * Base class for classes that follow a gateway pattern
 *
 * @abstract
 * @package WrsGroup
 * @subpackage Model
 * @author Eugene Morgan
 */
abstract class WrsGroup_Model_Gateway_Abstract
{
    /**
     * @var object Data provider object
     */
    protected $_dataProvider;

    /**
     * Usually should be provided by the child class
     *
     * @var string The class name of data provider class
     */
    protected $_dataProviderClass;

    /**
     * Usually should be provided by the child class
     *
     * @var string The value object class name
     */
    protected $_valueObjectClass;

    /**
     * Constructor
     *
     * @param string $dataProviderClass If provided, will use a different data
     *                                  provider than the default
     */
    public function __construct($dataProviderClass = null)
    {
        if (null === $dataProviderClass) {
            if (!$this->_dataProviderClass) {
                $msg = 'You cannot instantiate a gateway object without ' .
                'providing the name of the data provider class, either in ' .
                'the constructor or as a property of the child class.';
                throw new Exception($msg);
            }
        } else {
            $this->_dataProviderClass = $dataProviderClass;
        }
    }

    /**
     * Lazy-load data provider class
     *
     * @return Zend_Db_Table_Abstract|object An instance of the data
     *                                       provider class, usually a
     *                                       Zend_Db_Table
     */
    protected function _getDataProvider()
    {
        if (!$this->_dataProvider) {
            $this->_dataProvider = new $this->_dataProviderClass();
        }
        return $this->_dataProvider;
    }
    
    /**
     * Un-lazy-loads the data provider class; this may need to be done if
     * trying to serialize the object, for example
     *
     */
    public function unloadDataProvider()
    {
        $this->_dataProvider = null;
    }

    /**
     * Factory method for creating a value object
     *
     * @param array Data to use for value object
     * @return object Value object
     */
    public function createValueObject($data)
    {
        $className = $this->getValueObjectClass();
        return new $className($data);
    }

    /**
     * Factory method for creating a record set
     *
     * @param mixed $resultSet Can be an array or Zend_Db_Table_Rowset
     * @return object Record set
     */
    public function createRecordSet($resultSet)
    {
        return new WrsGroup_Model_RecordSet($resultSet, $this);
    }

    /**
     * Gets the name of the value object class being used with this gateway
     *
     * @return string The class name
     */
    public function getValueObjectClass()
    {
        if (!$this->_valueObjectClass) {
            throw new Exception('When extending the gateway class, you must ' .
                                'set the name of the value object class as a ' .
                                'property of the child class.');
        }
        return $this->_valueObjectClass;
    }
}