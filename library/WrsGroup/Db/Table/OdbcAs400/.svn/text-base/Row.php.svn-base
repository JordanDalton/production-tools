<?php
/**
 * Base class for domain objects -- i.e., entities or value objects or entities
 * that act as an aggregate root
 *
 * @deprecated Instead use OdbcDb2
 * @category WrsGroup
 * @package Model
 * @subpackage DomainObject
 * @abstract
 * @author Eugene Morgan
 */
class WrsGroup_Db_Table_OdbcAs400_Row
{
    protected $_data = array();
    protected $_trim;
    protected $_utf8Encode;

    /**
     * Constructor
     *
     * @param mixed $data Initial data
     * @param Zend_Config|array $options Configuration options
     */
    public function __construct($data, $options = null)
    {
        if (!$options) {
            if (Zend_Registry::isRegistered('config')) {
                $config = Zend_Registry::get('config');
                if (isset($config->odbcAs400->options)) {
                    $options = $config->odbcAs400->options->toArray();
                }
            } else {
                $options = array(
                    'trim' => true,
                    'utf8Encode' => false
                );
            }
        }
        $this->_trim = $options['trim'];
        $this->_utf8Encode = $options['utf8Encode'];
        $this->_populate($data);
    }

    /**
     * Populates data
     *
     * @param array $data An array of data
     */
    protected function _populate($data)
    {
        if (!is_array($data)) {
            throw new Exception('Initial data must be an array');
        }
        foreach ($data as $key => $value) {
            if ($this->_trim) {
                $value = trim($value);
            }
            if ($this->_utf8Encode) {
                $value = utf8_encode($value);
            }
            $this->$key = $value;
        }
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        if (isset($this->$name)) {
            unset($this->_data[$name]);
            return true;
        }
        return false;
    }

    /**
     * Returns data as an array
     *
     * @return array Object data as an array
     */
    public function toArray()
    {
        return $this->_data;
    }
}