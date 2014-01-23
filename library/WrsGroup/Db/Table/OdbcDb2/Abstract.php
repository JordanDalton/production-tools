<?php
/**
 * Custom wrapper for Zend_Db_Table_Abstract. The reason for using this class
 * is because with DB2 on the IBM i, if a column is prefixed by a table name
 * it must also be prefixed by the schema name, and the current Zend_Db
 * implementation just doesn't support this, so a wrapper also had to be created
 * for Zend_Db_Table_Select.
 *
 * Important things to note:
 * - In table classes, the primary key always needs to be specified with
 * the _primary property, because the adapter can't auto-discover primary keys.
 * - The schema can be specified in the class as well, but for APlus tables
 * it's best to inject this when constructing the object so that one can more
 * easily switch back and forth between the training and live environments (or
 * upgrade APlus versions).
 */
class WrsGroup_Db_Table_OdbcDb2_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Returns an instance of a WrsGroup_Db_Table_Select object.
     *
     * @param bool $withFromPart Whether or not to include the from part of the
     *     select based on the table
     * @return WrsGroup_Db_Table_Select
     */
    public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART)
    {
        require_once 'WrsGroup/Db/Table/OdbcDb2/Select.php';
        return new WrsGroup_Db_Table_OdbcDb2_Select($this);
    }
}