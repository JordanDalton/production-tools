<?php
/**
 * Pseudo-rowset class for our AS400/ODBC implementation. All it really does
 * is cast rows as objects when iterating through the rowset
 *
 * @deprecated Instead use OdbcDb2
 * @category WrsGroup
 * @package Db
 * @subpackage Table
 * @author Eugene Morgan
 */
class WrsGroup_Db_Table_OdbcAs400_Rowset implements Countable, Iterator
{
    protected $_count;
    protected $_resultSet;
    protected $_options;

    /**
     * Constructor
     *
     * @param array $results A rowset of data rows
     * @param array $options Configuration options
     */
    public function __construct($results, $options = null)
    {
        $this->_options = $options;
        $this->_resultSet = $results;
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
     * @return WrsGroup_Db_Table_OdbcAs400_Row A row object
     */
    public function current()
    {
        $result = current($this->_resultSet);
        if ($result) {
            return new WrsGroup_Db_Table_OdbcAs400_Row($result, $this->_options);
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
}