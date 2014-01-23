<?php

/**
 * A basic record set pattern for use with entities/value objects.
 * The main benefit of a record set over an array is that if you have a bunch
 * of results from a database, you don't have to instantiate all the objects
 * at once; you just put the array in the record set and lazy-load objects
 * as needed, through the factory class that you pass in the constructor.
 *
 * @category   WrsGroup
 * @package    Model
 * @author Eugene Morgan
 */
class WrsGroup_Model_RecordSet implements Iterator, Countable
{

    /**
     * @var int
     */
    protected $_count;

    /**
     * @var array
     */
    protected $_resultSet;

    /**
     * @var string The class name of the object we are creating a record set of
     */
    protected $_domainObjectClass;

    /**
     * @var object A factory object
     */
    protected $_factory;

    /**
     * @var string
     */
    protected $_factoryMethod;

    /**
     * @deprecated
     */
    protected $_gateway;

    /**
     * @var array Indexes that can be created by property value
     */
    protected $_indexes = array();

    /**
     * Constructor
     *
     * @param mixed $results List of iterable results; could be rowset or array
     * @param mixed $domainObjectClass The name of the domain object class. For
     *     backwards compatibility, can also be an instance of a corresponding
     *     gateway object
     * @param object $factory OPTIONAL An instance of the factory class to use
     *     for creating objects; if given, objects will not be constructed
     *     using new and $domainObjectClass.
     * @param string $factoryMethod The method of the factory class to call
     * 	   when creating an object; default is 'create'
     */
    public function __construct(
        $results,
        $domainObjectClass,
        $factory = null,
        $factoryMethod = 'create'
    )
    {
        if ($domainObjectClass instanceof WrsGroup_Model_Gateway_Abstract) {
            $this->_gateway = $domainObjectClass;
        } else {
            $this->_domainObjectClass = $domainObjectClass;
        }
        $this->_factory = $factory;
        $this->_factoryMethod = $factoryMethod;

        if ($results instanceof Zend_Db_Table_Rowset_Abstract ||
            $results instanceof WrsGroup_Db_Table_OdbcAs400_Rowset) {
            $results = $results->toArray();
        }
        $this->_resultSet = $results;

        // For child classes
        $this->init();
    }

    /**
     * For child classes
     */
    public function init() {}

    /**
     * Creates an index of the given property, if one has not already been
     * created
     *
     * @param string $property Property to index by
     */
    protected function _index($property)
    {
        if (!isset($this->_indexes[$property])) {
            $this->_indexes[$property] = array();
            foreach ($this as $object) {
                $this->_indexes[$property][$object->$property][] = $object;
            }
        }
    }

    /**
     * @see Countable::count()
     */
    public function count()
    {
        if (null === $this->_count) {
            $this->_count = count($this->_resultSet);
        }
        return $this->_count;
    }

    /**
     * @see Iterator::current()
     */
    public function current()
    {
        $result = current($this->_resultSet);
        $className = $this->getObjectClass();

        if ($result && !$result instanceof $className) {
            $key = key($this->_resultSet);
            if ($this->_gateway) {
                $result = $this->_gateway->createValueObject($result);
            } elseif ($this->_factory) {
                $method = $this->_factoryMethod;
                $result = $this->_factory->{$method}($result);
            } else {
                $result = new $className($result);
            }
            $this->_resultSet[$key] = $result;
        }
        return $result;
    }

    /**
     * @see Iterator::key()
     */
    public function key()
    {
        return key($this->_resultSet);
    }

    /**
     * @see Iterator::next()
     */
    public function next()
    {
        return next($this->_resultSet);
    }

    /**
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        return reset($this->_resultSet);
    }

    /**
     * @see Iterator::valid()
     */
    public function valid()
    {
        return (bool) $this->current();
    }

    /**
     * Returns all records as an array
     *
     * @return array Array of records
     */
    public function toArray()
    {
        if (is_array($this->_resultSet)) {
            return $this->_resultSet;
        }
        $array = array();
        foreach ($this->_resultSet as $result) {
            $array[] = $result;
        }
        return $array;
    }

    /**
     * Get all records where the given attribute is equal to the given value
     *
     * @param string $attribute The name of the attribute
     * @param mixed $value The value to match
     * @param boolean $caseSensitive Whether search is case sensitive -- default
     *     is true
     * @return WrsGroup_Model_RecordSet A record set object or null if
     *     no results
     */
    public function get($attribute, $value, $caseSensitive = true)
    {
        // If conditions are right, handle this with the new findAllBy() method
        if ($caseSensitive && !$this->_gateway) {
            return $this->findAllBy($attribute, $value);
        }

        $results = array();
        $className = $this->getObjectClass();
        foreach ($this->_resultSet as $result) {
            if ($result instanceof $className) {
                if ($caseSensitive) {
                    if ($result->$attribute == $value) {
                        $results[] = $result;
                    }
                } else {
                    if (strtolower($result->$attribute) == strtolower($value)) {
                        $results[] = $result;
                    }
                }
            } else {
                if (!isset($result[$attribute])) {
                    continue;
                }
                if ($caseSensitive) {
                    if ($result[$attribute] == $value) {
                        $results[] = $result;
                    }
                } else {
                    if (strtolower($result[$attribute] == strtolower($value))) {
                        $results[] = $result;
                    }
                }
            }
        }
        if (!count($results)) {
            return null;
        }
        if ($this->_gateway) {
            return new $this($results, $this->_gateway);
        } elseif ($this->_factory) {
            return new WrsGroup_Model_RecordSet(
                $results,
                $this->getObjectClass(),
                $this->_factory,
                $this->_factoryMethod
            );
        }
        return new $this($results, $this->getObjectClass());
    }

    /**
     * Get all values for the given attribute in the record set
     *
     * @param string $attribute The attribute name
     * @return array List of values
     */
    public function getValues($attribute)
    {
        $values = array();
        foreach ($this->_resultSet as $result) {
            if (is_object($result)) {
                $values[] = $result->$attribute;
            } else {
                $values[] = $result[$attribute];
            }
        }
        return $values;
    }

    /**
     * Gets class name of the domain object used in the record set
     *
     * @return string The class name
     */
    public function getObjectClass()
    {
        if ($this->_gateway) {
            return $this->_gateway->getValueObjectClass();
        }
        return $this->_domainObjectClass;
    }

    /**
     * Gets the gateway instance, helpful if you don't know the name
     * of the gateway class or don't wish to instantiate it
     *
     * @deprecated
     * @return WrsGroup_Model_Gateway_Abstract The gateway object
     */
    public function getGateway()
    {
        return $this->_gateway;
    }

    /**
     * "Subtracts" another record set from this record set and creates a new
     * record set. By "subtract" we mean it removes any records that exactly
     * match records in the original record set.
     *
     * @param WrsGroup_Model_RecordSet $recordSet Record set to subtract
     * @return WrsGroup_Model_RecordSet The new record set or false if no
     * 									records left
     */
    public function subtract($recordSet)
    {
        $newResults = array();
        foreach ($this as $vo) {
            if ($recordSet->_hasMatchingValueObject($vo)) {
                continue;
            }
            $newResults[] = $vo;
        }
        if (!count($newResults)) {
            return false;
        }
        if ($this->_gateway) {
            return new $this($newResults, $this->_gateway);
        } elseif ($this->_factory) {
            return new WrsGroup_Model_RecordSet(
                $newResults,
                $this->getObjectClass(),
                $this->_factory,
                $this->_factoryMethod
            );
        }
        return new $this($newResults, $this->getObjectClass());
    }

    /**
     * Tells whether an object exists in the record set that has the same
     * attributes and values as the given object
     *
     * @param WrsGroup_Model_DomainObject_Abstract $obj The object to search for
     * @return boolean True if the object exists in the record set
     */
    protected function _hasMatchingObject($obj)
    {
        foreach ($this as $value) {
            if ($value == $obj) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets records where values match given values for given attributes
     *
     * @param array $filters Object attributes to match
     * @return WrsGroup_Model_RecordSet A record set of objects
     */
    public function find($filters)
    {
        $results = array();
        foreach ($this as $object) {
            foreach ($filters as $attribute => $value) {
                if ($object->$attribute != $value) {
                    continue 2;
                }
            }
            $results[] = $object;
        }
        return new $this($results, $this->getObjectClass());
    }

    /**
     * Find the first object in the record set where the given property
     * matches the given value
     *
     * @param  string $property Name of property to match against
     * @param  mixed  $value Value to match property against
     * @return WrsGroup_Model_DomainObject_Abstract An object
     */
    public function findOneBy($property, $value)
    {
        if (!is_string($property) || empty($property)) {
            throw new InvalidArgumentException('Invalid property specified.');
        }
        $this->_index($property);
        if (isset($this->_indexes[$property][$value])) {
            return $this->_indexes[$property][$value][0];
        }
        return null;
    }

    /**
     * Find all objects in the record set where the given property
     * matches the given value, or return an empty record set if no
     * records are found
     *
     * @param  string $property Name of property to match against
     * @param  mixed  $value Value to match property against
     * @return WrsGroup_Model_RecordSet A record set of objects
     */
    public function findAllBy($property, $value)
    {
        $found = array();

        if (!is_string($property) || empty($property)) {
            throw new InvalidArgumentException('Invalid property specified.');
        }

        $this->_index($property);
        if (isset($this->_indexes[$property][$value])) {
            $found = $this->_indexes[$property][$value];
        }

        $class = get_class();
        return new $class(
            $found,
            $this->_domainObjectClass,
            $this->_factory,
            $this->_factoryMethod
        );
    }

    /**
     * Returns object(s) matching $property == $value
     *
     * @param  string $property  name of property to match against
     * @param  mixed  $value     value to match property against
     * @param  bool   $all       [optional] whether an array of all matching
     *                           pages should be returned, or only the first.
     *                           If true, an array will be returned, even if not
     *                           matching pages are found. If false, null will
     *                           be returned if no matching page is found.
     *                           Default is false.
     * @return WrsGroup_Model_DomainObject_Abstract|null matching object or null
     */
    public function findBy($property, $value, $all = false)
    {
        if ($all) {
            return $this->findAllBy($property, $value);
        } else {
            return $this->findOneBy($property, $value);
        }
    }

    /**
     * Magic overload: Proxy calls to finder methods
     *
     * Examples of finder calls:
     * <code>
     * // METHOD                    // SAME AS
     * $recordSet->findByLabel('foo');    // $recordSet->findOneBy('label', 'foo');
     * $recordSet->findOneByLabel('foo'); // $recordSet->findOneBy('label', 'foo');
     * $recordSet->findAllByClass('foo'); // $recordSet->findAllBy('class', 'foo');
     * </code>
     *
     * @param  string $method             method name
     * @param  array  $arguments          method arguments
     * @throws Zend_Navigation_Exception  if method does not exist
     */
    public function __call($method, $arguments)
    {
        if (@preg_match('/(find(?:One|All)?By)(.+)/', $method, $match)) {
            $property = strtolower(substr($match[2], 0, 1)) .
                substr($match[2], 1);
            return $this->{$match[1]}($property, $arguments[0]);
        }

        throw new Exception(sprintf(
            'Bad method call: Unknown method %s::%s',
            get_class($this),
            $method
        ));
    }

}
