<?php
/**
 * Extending Zend's table select class to deal with some issues with IBM i
 * ODBC.
 *
 * @category WrsGroup
 * @package Db
 * @subpackage Table
 * @author Eugene Morgan
 */
class WrsGroup_Db_Table_OdbcDb2_Select extends Zend_Db_Table_Select
{
    /**
     * Overriding parent method to deal with issues that arise when using
     * Zend_Db_Table_Select on the IBM i / DB2. DB2/IBM i always has to have
     * the schema name, and it doesn't handle quoting like some of the other
     * platforms; for ex., if you select t.column from table AS "t", you'll
     * get an error because it wants you to select "t".column from table as "t".
     *
     * From parent method:
     *
     * Populate the $_parts 'join' key
     *
     * Does the dirty work of populating the join key.
     *
     * The $name and $cols parameters follow the same logic
     * as described in the from() method.
     *
     * @param  null|string $type Type of join; inner, left, and null are currently supported
     * @param  array|string|Zend_Db_Expr $name Table name
     * @param  string $cond Join on this condition
     * @param  array|string $cols The columns to select from the joined table
     * @param  string $schema The database name to specify, if any.
     * @return Zend_Db_Select This Zend_Db_Select object
     * @throws Zend_Db_Select_Exception
     */
    protected function _join($type, $name, $cond, $cols, $schema = null)
    {
        // Set integrity check to false by default
        $this->setIntegrityCheck(false);

        if (!$schema && !$name instanceof WrsGroup_Db_Table_OdbcDb2_Select
                && (!is_array($name)
                || !current($name) instanceof 
                WrsGroup_Db_Table_OdbcDb2_Select)) {
            if (isset($this->_info['schema'])) {
                $schema = $this->_info['schema'];
            }
        }
        if ($schema) {
            if (is_string($name)) {
                $name = new Zend_Db_Expr($name);
            }
            return parent::_join($type, $name, $cond, $cols, $schema);
        }
        return parent::_join($type, $name, $cond, $cols);
    }

    /**
     * For some reason, this doesn't work properly with DB2/IBM i. Effectively
     * disabling at this point.
     *
     * Render LIMIT OFFSET clause
     *
     * @param string   $sql SQL query
     * @return string
     */
    protected function _renderLimitoffset($sql)
    {
        return $sql;
    }
}