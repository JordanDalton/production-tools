<?php
/**
 * Zend_Db_Adapter_Pdo_Abstract
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';

/**
 * Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';

/**
 * Class for connecting to IBM DB2 Databases with ODBC and performing common
 * operations.
 *
 * @deprecated Instead use OdbcDb2
 * @category   WrsGroup
 * @package    Db
 * @subpackage Adapter
 */
class WrsGroup_Db_Adapter_Pdo_OdbcAs400 extends Zend_Db_Adapter_Pdo_Abstract
{
    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'odbc';

    /**
     * Keys are UPPERCASE SQL datatypes or the constants
     * Zend_Db::INT_TYPE, Zend_Db::BIGINT_TYPE, or Zend_Db::FLOAT_TYPE.
     *
     * Values are:
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal
     *
     * @var array Associative array of datatypes to values 0, 1, or 2.
     */
    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INTEGER'                => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'DEC'                => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE,
        'NUMERIC'            => Zend_Db::FLOAT_TYPE,
        'REAL'               => Zend_Db::FLOAT_TYPE,
        'DOUBLE PRECISION'   => Zend_Db::FLOAT_TYPE,
        'DECFLOAT'           => Zend_Db::FLOAT_TYPE,
        'DOUBLE'             => Zend_Db::FLOAT_TYPE,
        'NUM'                => Zend_Db::FLOAT_TYPE
    );

    /**
     * This is a totally custom method used as a shortcut to format a table name
     * correctly in a query.
     *
     * @param string $tableName The table name
     * @param string $schema The schema name
     * @return Zend_Db_Expr A formatted SQL expression
     */
    public function getTable($tableName, $schema = false)
    {
        if (!$schema) {
            if (!Zend_Registry::isRegistered('odbcAs400_schema')) {
                throw new Exception('You must pass a schema to getTable() ' .
                	'unless you have set a default schema in the registry ' .
                	'as "odbcAs400_schema".');
            }
            $schema = Zend_Registry::get('odbcAs400_schema');
        }
        return new Zend_Db_Expr($schema . '.' . strtoupper($tableName));
    }
    
    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        // baseline of DSN parts
        $dsn = $this->_config;

        // don't pass the username and password in the DSN
        unset($dsn['username']);
        unset($dsn['password']);
        unset($dsn['driver_options']);
        unset($dsn['port']);

        // this driver supports multiple DSN prefixes
        // @see http://www.php.net/manual/en/ref.pdo-dblib.connection.php
        if (isset($dsn['pdoType'])) {
            switch (strtolower($dsn['pdoType'])) {
                default:
                    $this->_pdoType = 'odbc';
                    break;
            }
            unset($dsn['pdoType']);
        }

        if (isset($dsn['dbname'])) {
            $dsn = $this->_pdoType . ':' . $dsn['dbname'];
        } else {
            // use all remaining parts in the DSN
            foreach ($dsn as $key => $val) {
                $dsn[$key] = "$key=$val";
            }

            $dsn = $this->_pdoType . ':' . implode(';', $dsn);
        }
        return $dsn;
    }

    /**
     * @return void
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }
        parent::_connect();

        // From PDO IBM class
        $this->_connection->setAttribute(Zend_Db::ATTR_STRINGIFY_FETCHES,
                                         true);



        // Commenting out this line from ODBC class for MSSQL
        // $this->_connection->exec('SET QUOTED_IDENTIFIER ON');
    }

    /**
     * Took this from the Oracle OCI driver. Seems to have fixed 'quote'
     * problem.
     *
     * Quote a raw string.
     * Most PDO drivers have an implementation for the quote() method,
     * but the Oracle OCI driver must use the same implementation as the
     * Zend_Db_Adapter_Abstract class.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        // From PDO DB2 class
        $sql = "SELECT tabname "
        . "FROM SYSCAT.TABLES ";
        return $this->fetchCol($sql);

        // Commenting out below from ODBC MSSQL class
        // $sql = "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
        // return $this->fetchCol($sql);
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     * PRIMARY_AUTO     => integer; position of auto-generated column in primary key
     *
     * @todo Discover column primary key position.
     * @todo Discover integer unsigned property.
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {

        // From PDO IBM DB2 class
        $sql = "SELECT DISTINCT C.SYSTEM_TABLE_SCHEMA, C.TABLE_NAME, C.COLUMN_NAME, C.ORDINAL_POSITION,
                C.DATA_TYPE, C.COLUMN_DEFAULT, C.IS_NULLABLE, C.LENGTH, C.NUMERIC_SCALE,
                C.IS_IDENTITY, TC.CONSTRAINT_TYPE AS TABCONSTTYPE, K.COLUMN_POSITION
                FROM QSYS2.SYSCOLUMNS C
                LEFT JOIN (QSYS2.SYSKEYCST K JOIN QSYS2.TABLE_CONSTRAINTS TC
                 ON (K.TABLE_SCHEMA = TC.TABLE_SCHEMA
                   AND K.TABLE_NAME = TC.TABLE_NAME
                   AND TC.CONSTRAINT_TYPE = 'PRIMARY KEY'))
                 ON (C.TABLE_SCHEMA = K.TABLE_SCHEMA
                 AND C.TABLE_NAME = K.TABLE_NAME
                 AND C.COLUMN_NAME = K.COLUMN_NAME)
            WHERE "
            . $this->quoteInto('UPPER(C.TABLE_NAME) = UPPER(?)', $tableName);
        if ($schemaName) {
            $sql .= $this->quoteInto(' AND UPPER(C.SYSTEM_TABLE_SCHEMA) = UPPER(?)',
                                     $schemaName);
        }
        $sql .= " ORDER BY C.ORDINAL_POSITION";

        $desc = array();
        $stmt = $this->query($sql);

        /**
         * To avoid case issues, fetch using FETCH_NUM
         */
        $result = $stmt->fetchAll(Zend_Db::FETCH_NUM);

        /**
         * The ordering of columns is defined by the query so we can map
         * to variables to improve readability
         */
        $tabschema      = 0;
        $tabname        = 1;
        $colname        = 2;
        $colno          = 3;
        $typename       = 4;
        $default        = 5;
        $nulls          = 6;
        $length         = 7;
        $scale          = 8;
        $identityCol    = 9;
        $tabconstype    = 10;
        $colseq         = 11;

        foreach ($result as $key => $row) {
            list ($primary, $primaryPosition, $identity) = array(false, null, false);
            if ($row[$tabconstype] == 'P') {
                $primary = true;
                $primaryPosition = $row[$colseq];
            }
            /**
             * In IBM DB2, an column can be IDENTITY
             * even if it is not part of the PRIMARY KEY.
             */
            if ($row[$identityCol] == 'Y') {
                $identity = true;
            }

            $desc[$this->foldCase($row[$colname])] = array(
            'SCHEMA_NAME'      => $this->foldCase($row[$tabschema]),
            'TABLE_NAME'       => $this->foldCase($row[$tabname]),
            'COLUMN_NAME'      => $this->foldCase($row[$colname]),
            'COLUMN_POSITION'  => $row[$colno]+1,
            'DATA_TYPE'        => $row[$typename],
            'DEFAULT'          => $row[$default],
            'NULLABLE'         => (bool) ($row[$nulls] == 'Y'),
            'LENGTH'           => $row[$length],
            'SCALE'            => $row[$scale],
            'PRECISION'        => ($row[$typename] == 'DECIMAL' ? $row[$length] : 0),
            'UNSIGNED'         => false,
            'PRIMARY'          => $primary,
            'PRIMARY_POSITION' => $primaryPosition,
            'IDENTITY'         => $identity
            );
        }

        return $desc;
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @link http://lists.bestpractical.com/pipermail/rt-devel/2005-June/007339.html
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
     * @return string
     */
     public function limit($sql, $count, $offset = 0)
     {
        $count = intval($count);
        if ($count <= 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument offset=$offset is not valid");
        }

        $orderby = stristr($sql, 'ORDER BY');
        if ($orderby !== false) {
            $sort = (stripos($orderby, ' desc') !== false) ? 'desc' : 'asc';
            $order = str_ireplace('ORDER BY', '', $orderby);
            $order = trim(preg_replace('/\bASC\b|\bDESC\b/i', '', $order));
        }

        $sql = preg_replace('/^SELECT\s/i', 'SELECT TOP ' . ($count+$offset) . ' ', $sql);

        $sql = 'SELECT * FROM (SELECT TOP ' . $count . ' * FROM (' . $sql . ') AS inner_tbl';
        if ($orderby !== false) {
            $sql .= ' ORDER BY ' . $order . ' ';
            $sql .= (stripos($sort, 'asc') !== false) ? 'DESC' : 'ASC';
        }
        $sql .= ') AS outer_tbl';
        if ($orderby !== false) {
            $sql .= ' ORDER BY ' . $order . ' ' . $sort;
        }
        return $sql;
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * Microsoft SQL Server does not support sequences, so the arguments to
     * this method are ignored.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     * @throws Zend_Db_Adapter_Exception
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $sql = 'SELECT SCOPE_IDENTITY()';
        return (int)$this->fetchOne($sql);
    }

}