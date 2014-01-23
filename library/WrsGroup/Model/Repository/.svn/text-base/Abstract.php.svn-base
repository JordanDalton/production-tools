<?php
/**
 * Base class for domain repositories
 *
 * @category WrsGroup
 * @package Model
 * @subpackage Repository
 * @abstract
 * @author Eugene Morgan
 */
abstract class WrsGroup_Model_Repository_Abstract
{
    /**
     * A dependency injection container
     *
     * @var Yadif_Container
     */
    protected $_container;

    /**
     * Constructor
     */
    public function __construct()
    {
        $front = Zend_Controller_Front::getInstance();
        $this->_container = $front->getParam('bootstrap')->getContainer();
    }

    /**
     * This use of the magic method __call() provides dependency injection
     * capability for the repository. Inspired by
     * http://blog.fedecarg.com/2009/03/22/zend-framework-domain-driven-design/
     * but ultimately makes use of Yadif_Container to accomplish dependency
     * injection.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $classIdentifier = strtolower($method[3]) . substr($method, 4);
        return $this->_container->getComponent($classIdentifier);
    }

    /**
     * Generic handler for saving a domain object to a table and returns the
     * object, with new id if applicable
     *
     * @param WrsGroup_Model_DomainObject_Abstract $object A domain object
     * @param Zend_Db_Table_Abstract $table A table object
     */
    protected function _saveToTable($object, $table)
    {
        $populated = $object->getPopulated(
            WrsGroup_Model_DomainObject_Abstract::PROPERTY_FORMAT_DB
        );

        // Filter out objects and arrays
        foreach ($populated as $key => $value) {
            if (is_object($value) || is_array($value)) {
                unset($populated[$key]);
            }
        }
        $primaryKeys = $table->info(Zend_Db_Table_Abstract::PRIMARY);
        $primaryKeyValues = array();
        foreach ($primaryKeys as $primaryKey) {
            if (isset($populated[$primaryKey])) {
                $primaryKeyValues[$primaryKey] = $populated[$primaryKey];
            }
        }
        if (count($primaryKeyValues) == count($primaryKeys)) {
            // This may be an update; query to see
            $select = $table->select()
                ->from($table, 'COUNT(*) AS cnt');
            foreach ($primaryKeyValues as $primaryKey => $value) {
                $select->where($primaryKey . ' = ?', $value);
            }
            $row = $table->fetchRow($select);
            if ($row->cnt) {
                $where = array();
                foreach ($primaryKeyValues as $primaryKey => $value) {
                    $where[] = $table->getAdapter()->quoteInto(
                        $primaryKey . ' = ?',
                        $value
                    );
                }
                $table->update($populated, $where);
            } else {
                $table->insert($populated);
            }
            return $object;
        }
        if (count($primaryKeyValues) < count($primaryKeys) - 1) {
            $message = 'No more than one primary key value can be missing.';
            throw new InvalidArgumentException($message);
        }

        // There is a primary key missing, which we assume to be an
        // auto_increment key. Do an insert, get the id auto-generated and
        // add it to the object, and return the object.
        $table->insert($populated);

        // Find name of missing column
        foreach ($primaryKeys as $primaryKey) {
            if (!isset($primaryKeyValues[$primaryKey])) {
                $column = $primaryKey;
                break;
            }
        }
        $id = $table->getAdapter()->lastInsertId(
            $table->info(Zend_Db_Table_Abstract::NAME),
            $column
        );
        $method = 'set' . ucfirst($column);
        $object->$method($id);
        return $object;
    }
}