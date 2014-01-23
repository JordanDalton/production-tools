<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   WrsGroup
 * @package    Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Db_Adapter_Pdo_Abstract
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';

/**
 * Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';

/**
 * Class for connecting to ODBC System databases and performing common
 * operations. Adapted from http://framework.zend.com/issues/browse/ZF-905
 * specifically for connecting to the IBM i using ODBC. Differences include
 * describe table functionality and commenting out a command for setting
 * QUOTED_IDENTIFER.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class WrsGroup_Db_Adapter_Pdo_OdbcDb2 extends Zend_Db_Adapter_Pdo_Abstract
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
        'INT'                => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'TINYINT'            => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE,
        'MONEY'              => Zend_Db::FLOAT_TYPE,
        'NUMERIC'            => Zend_Db::FLOAT_TYPE,
        'REAL'               => Zend_Db::FLOAT_TYPE,
        'SMALLMONEY'         => Zend_Db::FLOAT_TYPE
    );

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
        // $this->_connection->exec('SET QUOTED_IDENTIFIER ON');
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
        return $this->fetchCol($sql);
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
        /**
         * Discover metadata information about this table.
         */
        $sql = "select * from qsys2.syscolumns where table_schema = " .
            strtoupper($this->quote($schemaName)) .
            " AND table_name = " . strtoupper($this->quote($tableName));
        //$sql = "exec sp_columns @table_name = " . $this->quoteIdentifier($tableName, true);
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        $stmt->closeCursor();
        
        $table_name  = 'TABLE_NAME';
        $column_name = 'COLUMN_NAME';
        $type_name   = 'DATA_TYPE';
        $precision   = 'NUMERIC_PRECISION';
        $length      = 'LENGTH';
        $scale       = 'NUMERIC_SCALE';
        $nullable    = 'IS_NULLABLE';
        $schema      = 'TABLE_SCHEMA';
        $column_def  = 'COLUMN_DEFAULT';
        $column_position = 'ORDINAL_POSITION';

        /**
         * Discover primary key column(s) for this table.
         */
        //$sql = "exec sp_pkeys @table_name = " . $this->quoteIdentifier($tableName, true);
        //$stmt = $this->query($sql);
        //$primaryKeysResult = $stmt->fetchAll(Zend_Db::FETCH_NUM);
        //$stmt->closeCursor();
        //$pkey_column_name = 3;
        //$pkey_key_seq = 4;
        //foreach ($primaryKeysResult as $pkeysRow) {
        //    $primaryKeyColumn[$pkeysRow[$pkey_column_name]] = $pkeysRow[$pkey_key_seq];
        //}

        if (!isset($primaryKeyColumn)) {
            $primaryKeyColumn = array();
        }
        $desc = array();
        $p = 1;
        foreach ($result as $key => $row) {
            foreach ($row as $rowKey => $rowValue) {
                $newKey = strtoupper($rowKey);
                $row[$newKey] = $rowValue;
            }
            $identity = false;
            $words = explode(' ', $row[$type_name], 2);
            if (isset($words[0])) {
                $type = $words[0];
                if (isset($words[1])) {
                    $identity = (bool) preg_match('/identity/', $words[1]);
                }
            }

            $isPrimary = array_key_exists($row[$column_name], $primaryKeyColumn);
            if ($isPrimary) {
                $primaryPosition = $primaryKeyColumn[$row[$column_name]];
            } else {
                $primaryPosition = null;
            }

            $desc[$this->foldCase($row[$column_name])] = array(
                'SCHEMA_NAME'      => $row[$schema],
                'TABLE_NAME'       => $this->foldCase($row[$table_name]),
                'COLUMN_NAME'      => $this->foldCase($row[$column_name]),
                'COLUMN_POSITION'  => (int) $row[$column_position],
                'DATA_TYPE'        => $type,
                'DEFAULT'          => $row[$column_def],
                'NULLABLE'         => (bool) $row[$nullable],
                'LENGTH'           => $row[$length],
                'SCALE'            => $row[$scale],
                'PRECISION'        => $row[$precision],
                'UNSIGNED'         => null, // @todo
                'PRIMARY'          => $isPrimary,
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
            $sort = (stripos($orderby, 'desc') !== false) ? 'desc' : 'asc';
            $order = str_ireplace('ORDER BY', '', $orderby);
            $order = trim(preg_replace('/ASC|DESC/i', '', $order));
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
     * Adapted this from Zend DB2 adapter
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     * @throws Zend_Db_Adapter_Exception
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if ($tableName === null) {
            $sql = 'SELECT IDENTITY_VAL_LOCAL() AS VAL FROM qsys2.qsqptabl';
            $value = $this->fetchOne($sql);
            return $value;
        }

        return $this->fetchOne('SELECT IDENTITY_VAL_LOCAL() from ' . $this->quoteIdentifier($tableName));
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
}
