<?php
/**
 * Base class for domain objects -- i.e., entities or value objects or entities
 * that act as an aggregate root
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DomainObject
 * @abstract
 * @author Eugene Morgan
 */
abstract class WrsGroup_Model_DomainObject_Abstract
{
    const PROPERTY_FORMAT_DB = 'propertyFormatDb';

    /**
     * @var array The object's properties
     */
    protected $_data = array();

    /**
     * @var array New key values for converting items_id to itemsId, etc.
     */
    protected $_keyMap = array();

    /**
     * List of properties that have been populated; helps distinguish between
     * properties that have been set to null vs. those that have simply not
     * been set
     *
     * @var array List of properties that have been populated 
     */
    protected $_populatedItems = array();

    protected $_ignoreNulls = true;

    /**
     * Placeholder for an array with object attribute names as keys and
     * database field names as values; used when the object attribute names
     * are different from the database field names, which often occurs for
     * example with IBM i field names which are short and not very descriptive.
     * 
     * @var array
     */
    protected $_dbFieldMap;

    /**
     * Constructor. Added options parameter and ignoreNulls option to retain
     * backwards compatibility. By default, if an item is null, it will be
     * totally ignored. This way, if the $data array originally had some
     * properties that were removed using unset(), those properties will be
     * ignored.
     *
     * @param mixed $data Initial data
     * @param array $options An array of options
     */
    public function __construct($data, $options = array())
    {
        // Set options
        if (isset($options['ignoreNulls'])) {
            $this->setIgnoreNulls($options['ignoreNulls']);
        }

        // Create a key map so that some_property is equivalent to someProperty
        $properties = array_keys($this->_data);
        $exclude = array(
            '_data',
            '_keyMap',
            '_populatedItems',
            '_dbFieldMap',
        );
        foreach (array_keys(get_object_vars($this)) as $property) {
            if (!in_array($property, $exclude) && $property[0] == '_') {
                $properties[] = mb_substr($property, 1);
            }
        }
        foreach ($properties as $property) {
            if (mb_strpos($property, '_') === false) {
                $newKey = preg_replace(
                    '/[A-Z]/e',
                    "'_' . strtolower('\\0')",
                    $property
                );
                $this->_keyMap[$newKey] = $property;
            } else {
                $newKey = strtolower($property);
                $newKey = preg_replace('/_(\w)/e', "strtoupper('\\1')", $newKey);
                $this->_keyMap[$newKey] = $property;
            }
        }
        $this->_populate($data);
    }

    /**
     * Setter for ignore nulls
     * 
     * @param bool $value 
     */
    public function setIgnoreNulls($value)
    {
        $this->_ignoreNulls = $value;
    }

    /**
     * Populates data
     *
     * @param mixed $data
     * @throws InvalidArgumentException If data is not an array or cannot be converted to an array
     */
    protected function _populate($data)
    {
        // If data is an object, convert to an array
        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = (array) $data;
            }
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException(
                'Initial data must be an array or object');
        }
        if ($this->_ignoreNulls) {
            $newArray = array();
            foreach ($data as $key => $value) {
                if ($value !== null) {
                    $newArray[$key] = $value;
                }
            }
            $data = $newArray;
        }
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Adds a property to the populated items list
     *
     * @param string $property The property name
     */
    protected function _addPopulatedItem($property)
    {
        if (!$this->isPopulated($property)) {
            $this->_populatedItems[] = $property;
        }
    }

    /**
     * Use of magic method to set a protected property of the object
     *
     * @param string $name The name of the property to set
     * @param mixed $value The value to set the property equal to
     * @throws InvalidArgumentException If the property is not defined in the 
     *     object
     * @return WrsGroup_Model_DomainObject_Abstract Fluent interface
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_data)) {
            $this->_data[$name] = $value;
        } elseif (property_exists($this, '_' . $name)) {
            $prefixed = '_' . $name;
            $this->$prefixed = $value;
        } else {
            if (!isset($this->_keyMap[$name])) {
                // Do nothing if the property is not specified
                // in the object definition
                return;
            }
            $name = $this->_keyMap[$name];
            $this->_data[$name] = $value;
        }
        $this->_addPopulatedItem($name);
        return $this;
    }

    /**
     * Use of magic method to retrieve a protected property of the object
     *
     * @param string $name The name of the property
     * @throws InvalidArgumentException If the property given is not defined
     *     as a valid property for the object
     * @return mixed The data held by the given property
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        } elseif (property_exists($this, '_' . $name)) {
            $name = '_' . $name;
            return $this->$name;
        } else {
            if (!isset($this->_keyMap[$name])) {
                $msg = 'Invalid property "' . $name . '"';
                throw new InvalidArgumentException($msg);
            }
            $name = $this->_keyMap[$name];
            if (array_key_exists($name, $this->_data)) {
                return $this->_data[$name];
            } else {
                $name = '_' . $name;
                return $this->$name;
            }
        }
    }

    /**
     * Create getter and setter methods. See 
     * http://fedecarg.com/repositories/entry/zfdomain/Entity.php. 
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $args)
    {
        $type = substr($method, 0, 3);
        $property = strtolower($method[3]) . substr($method, 4);
        if ('get' === $type) {
            return $this->__get($property);
        } elseif ('set' === $type) {
            return $this->__set($property, $args[0]);
        }
        $message = 'Invalid method call: ' . get_class($this).'::'.$method.'()';
        throw new Exception($message);
    }

    public function __isset($name)
    {
        if (isset($this->_data[$name]) || property_exists($this, '_' . $name)) {
            return true;
        }
        return false;
    }

    public function __unset($name)
    {
        if (isset($this->_data[$name])) {
            unset($this->_data[$name]);
        } else {
            $name = '_' . $name;
            if (property_exists($this, $name)) {
                unset($this->$name);
            }
        }
    }

    /**
     * Returns just the data (i.e., non-objects) as an array
     *
     * @since 1.4 Now includes properties of the object not in $_data
     * @return array Object properties as an array
     */
    public function toArray()
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getDefaultProperties();
        $parentProperties = get_class_vars(__CLASS__);
        $return = $this->_data;
        foreach ($properties as $key => $value) {
            // Skip database field map
            if ($key == '_dbFieldMap') {
                continue;
            }
            if (!isset($parentProperties[$key])) {
                $value = $this->$key;
                if (substr($key, 0, 1) == '_') {
                    $key = substr($key, 1);
                }
                $return[$key] = $this->__get($key);
            }
        }
        return $return;
    }

    /**
     * Tells if a property has been explicitly assigned to this property
     * either when the object was constructed or by a subsequent setting of
     * the property
     *
     * @param string $property The property name
     * @return boolean True if data was assigned to this property
     */
    public function isPopulated($property)
    {
        if (in_array($property, $this->_populatedItems)) {
            return true;
        }
        if (isset($this->_keyMap[$property]) 
                && in_array($this->_keyMap[$property], $this->_populatedItems)) {
            return true;
        }
        return false;
    }

    /**
     * Gets data from the object that has actually been populated
     *
     * @param string $propertyFormat How to format the property; if null it's
     *     left as is
     * @return array An array of data that was populated
     */
    public function getPopulated($propertyFormat = null)
    {
        $data = array();
        foreach ($this->_populatedItems as $property) {
            $value = $this->__get($property);
            if ($propertyFormat == self::PROPERTY_FORMAT_DB) {
                if (isset($this->_dbFieldMap[$property])) {
                    $property = $this->_dbFieldMap[$property];
                } else {
                    $property = preg_replace(
                        '/[A-Z]/e',
                        "'_' . strtolower('\\0')",
                        $property
                    );
                }
            }
            $data[$property] = $value;
        }
        return $data;
    }

    /**
     * Unpopulates a property
     * 
     * @param string $property The property name
     */
    public function unpopulate($property)
    {
        if (!$this->__isset($property)) {
            throw new InvalidArgumentException(
                'The property ' . $property . ' is not valid for this object.');
        }
        $key = array_search($property, $this->_populatedItems);
        if ($key !== false) {
            unset($this->_populatedItems[$key]);
            return;
        }
        if (isset($this->_keyMap[$property])) {
            $key = array_search($this->_keyMap[$property], $this->_populatedItems);
            if ($key !== false) {
                unset($this->_populatedItems[$key]);
            }
        }
    }
}
