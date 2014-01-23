<?php
/**
 * Pseudo-table class for our AS400/ODBC implementation. Doesn't extend
 * Zend_Db_Table or use DESCRIBE because of the overhead associated
 * with the complexity of these tables.
 *
 * @deprecated Instead use OdbcDb2
 * @category WrsGroup
 * @package Db
 * @subpackage Table
 * @author Eugene Morgan
 */
abstract class WrsGroup_Db_Table_OdbcAs400_Abstract
{
    /**
     * Zend_Db_Adapter_Abstract object
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Constructor
     *
     * Lazy-loads the database adapter, unless one is provided in the config
     * array
     *
     * @param array $config Configuration options
     */
    public function __construct($config = array())
    {
        if (isset($config['db'])) {
            $this->_db = $config['db'];
        } else {
            if (!Zend_Registry::isRegistered('odbcAs400')) {
                if (!Zend_Registry::isRegistered('odbcAs400_params')) {
                    $msg = 'Database parameters must be set in the bootstrap ' .
                    'and assigned to the registry as "odbcAs400_params" ' .
                    'before the odbcAs400 table object can be constructed.';
                    throw new Exception($msg);
                }
                $params = Zend_Registry::get('odbcAs400_params');
                $db = new WrsGroup_Db_Adapter_Pdo_OdbcAs400($params);
                Zend_Registry::set('odbcAs400', $db);
            }
            $this->_db = Zend_Registry::get('odbcAs400');
        }
    }
    
    /**
     * Gets the adapter object
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_db;
    }
    
    /**
     * Gets a select object to use with this adapter
     *
     * @return Zend_Db_Select A select object
     */
    public function select()
    {
        return $this->_db->select();
    }
    
    /**
     * This is a totally custom method used as a shortcut to format a table name
     * correctly in a query.
     *
     * @param string $tableName The table name
     * @param string $schema The schema name
     * @return Zend_Db_Expr A formatted SQL expression
     */
    public function getTable($tableName = false, $schema = false)
    {
        if (!$tableName) {
            if (!$this->_name) {
                throw new Exception('You must define a name for this table ' .
                'with the _name property or else pass the tableName as a ' .
                'parameter when using getTable().');
            }
            $tableName = $this->_name;
        }
        return $this->getAdapter()->getTable($tableName, $schema);
    }
    
    /**
     * Fetches all rows in the query
     *
     * @param Zend_Db_Select $select A select object
     * @param array $options OPTIONAL Options such as trim, utf-8
     * @return WrsGroup_Db_Table_OdbcAs400_Rowset A rowset object
     */
    public function fetchAll($select, $options = null)
    {
        $array = $this->_db->fetchAll($select);
        if (!$array) {
            return null;
        }
        return new WrsGroup_Db_Table_OdbcAs400_Rowset($array, $options);
    }
    
    /**
     * Fetches the first row
     *
     * @param Zend_Db_Select $select A select object
     * @param array $options OPTIONAL Options such as trim, utf-8
     * @return object A standard class object with the row data
     */
    public function fetchRow($select, $options = null)
    {
        $row = $this->_db->fetchRow($select);
        if (!$row) {
            return null;
        }
        return new WrsGroup_Db_Table_OdbcAs400_Row($row, $options);
    }
}