<?php
/**
 * Base class for factories for creating domain objects.
 *
 * NOTE: No longer recommending the use of this class; doesn't really do much
 *
 * @deprecated
 * @abstract
 * @category WrsGroup
 * @package Model
 * @subpackage Factory
 * @author Eugene Morgan
 */
abstract class WrsGroup_Model_Factory_Abstract
{
    /**
     * Must be provided by the child class
     *
     * @var string The class name of the domain object this factory is creating
     */
    protected $_domainObjectClass;

    /**
     * Basic factory method for creating a domain object;
     * can be overwritten by the child class
     *
     * @param array Data to use for value object
     * @return object Value object
     */
    public function create($data)
    {
        $className = $this->getDomainObjectClass();
        return new $className($data);
    }

    /**
     * Factory method for creating a record set of domain objects
     *
     * @param mixed $resultSet Can be an array or Zend_Db_Table_Rowset
     * @param string $factoryMethod Name of the method for creating an object;
     *     default is 'create'
     * @return WrsGroup_Model_RecordSet Record set
     */
    public function createRecordSet($resultSet, $factoryMethod = 'create')
    {
        $className = $this->getDomainObjectClass();
        return new WrsGroup_Model_RecordSet($resultSet, $className, $this,
            $factoryMethod);
    }

    /**
     * Gets the name of the domain object class being used with this factory
     *
     * @return string The class name
     */
    public function getDomainObjectClass()
    {
        if (!$this->_domainObjectClass) {
            throw new Exception('When extending the factory class, you must ' .
                                'set the name of the domain object class as ' .
                                'a property of the child factory class.');
        }
        return $this->_domainObjectClass;
    }
}