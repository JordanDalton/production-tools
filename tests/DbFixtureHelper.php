<?php
/**
 * Helper class for more easily creating PHPUnit test suites
 *
 */
class DbFixtureHelper
{
    /**
     * The default/current schema from the database adapter
     * 
     * @var string
     */
    protected $_defaultSchema;

    /**
     * Database adapter
     *
     * @var Zend_Db_Adapter_Abstract 
     */
    protected $_db;

    /**
     * Contents of database fixture file
     * 
     * @var string
     */
    protected $_fileContents;

    /**
     * The schema desired for the queries in the fixture file
     * 
     * @var string
     */
    protected $_schema;

    /**
     * Constructor
     *
     * @param string $file Path to database fixture file
     * @param Zend_Db_Adapter_Abstract $db A database adapter
     * @param string $schema OPTIONAL Optionally, the name of the database schema
     */
    public function __construct($file, $db, $schema = null)
    {
        $this->_fileContents = file_get_contents($file);
        $this->_db = $db;
        if ($schema) {
            $sql = 'SELECT DATABASE()';
            $stmt = $this->_db->query($sql);
            $stmt->setFetchMode(Zend_Db::FETCH_OBJ);
            $this->_defaultSchema = $stmt->fetchColumn(0);
            $this->_schema = $schema;
        }
    }

    /**
     * Clears all tables in the same schema as the fixture file
     * 
     */
    public function clearAll()
    {
        if ($this->_schema) {
            $sql = 'USE `' . $this->_schema . '`';
            $this->_db->query($sql);
        }
        $select = $this->_db->select()
            ->from('tables', 'table_name', 'information_schema')
            ->where('table_schema = (SELECT DATABASE())');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        foreach ($result as $row) {
            $sql = 'TRUNCATE `' . $row['table_name'] . '`';
            $this->_db->query($sql);
        }
        if ($this->_schema) {
            $sql = 'USE `' . $this->_defaultSchema . '`';
            $this->_db->query($sql);
        }
    }

    /**
     * Perform all inserts in the fixture file
     * 
     */
    public function insertAll()
    {
        if ($this->_schema) {
            $sql = 'USE `' . $this->_schema . '`';
            $this->_db->query($sql);
        }
        $regex = '/insert\s+into[^;]+;/i';
        preg_match_all($regex, $this->_fileContents, $matches);
        foreach ($matches[0] as $sql) {
            $this->_db->query($sql);
        }
        if ($this->_schema) {
            $sql = 'USE `' . $this->_defaultSchema . '`';
            $this->_db->query($sql);
        }
    }
}
