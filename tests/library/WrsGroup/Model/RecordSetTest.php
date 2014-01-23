<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

require_once realpath(APPLICATION_PATH .
    '/../library/WrsGroup/Model/DomainObject/Abstract.php');

class TestModel extends WrsGroup_Model_DomainObject_Abstract
{
    protected $_data = array(
        'camelCase' => null,
        'under_score' => null,
        'nocase' => null,
    );
}

class Model_RecordSetTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_RecordSet
     */
    protected $_recordSet;

    public function setUp()
    {
        parent::setUp();
        $results = array(
            new TestModel(array(
                'camelCase' => 'test1',
                'under_score' => 'test2',
                'nocase' => 'test3'
            )),
            new TestModel(array(
                'camelCase' => 'test4',
                'under_score' => 'test5',
                'nocase' => 'test6'
            ))
        );
        $this->_recordSet = new WrsGroup_Model_RecordSet(
            $results,
            'TestModel'
        );
    }

    public function testGet()
    {
        $subset = $this->_recordSet->get('camelCase', 'test1');
        $this->assertType('WrsGroup_Model_RecordSet', $subset);
        $this->assertEquals(1, count($subset));

        $subset = $this->_recordSet->get('camel_case', 'test1');
        $this->assertType('WrsGroup_Model_RecordSet', $subset);
        $this->assertEquals(1, count($subset));

        $subset = $this->_recordSet->get('underScore', 'test2');
        $this->assertType('WrsGroup_Model_RecordSet', $subset);
        $this->assertEquals(1, count($subset));

        $subset = $this->_recordSet->get('under_score', 'test2');
        $this->assertType('WrsGroup_Model_RecordSet', $subset);
        $this->assertEquals(1, count($subset));

        $subset = $this->_recordSet->get('nocase', 'test3');
        $this->assertType('WrsGroup_Model_RecordSet', $subset);
        $this->assertEquals(1, count($subset));
    }

    public function testGetValues()
    {
        $values = $this->_recordSet->getValues('underScore');
        $this->assertEquals(2, count($values));
        $this->assertContains('test5', $values);

        $values = $this->_recordSet->getValues('under_score');
        $this->assertEquals(2, count($values));
        $this->assertContains('test5', $values);

        // Test with standard object
        $array = array(
            new stdClass(array('underScore' => 'foo')),
            new stdClass(array('underScore' => 'bar')),
        );
        $recordSet = new WrsGroup_Model_RecordSet(
            $array,
            'TestModel'
        );
        $values = $this->_recordSet->getValues('under_score');
        $this->assertEquals(2, count($values));
        $this->assertContains('test5', $values);
    }

    public function testFindOneBy()
    {
        // With a value that matches
        $object = $this->_recordSet->findOneBy('camelCase', 'test1');
        $this->assertType('WrsGroup_Model_DomainObject_Abstract', $object);

        // With a value that doesn't match
        $result = $this->_recordSet->findOneBy('camelCase', 'abcdefg');
        $this->assertEquals(null, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindOneByThrowsException()
    {
        $object = $this->_recordSet->findOneBy('', 'test1');
    }

    public function testFindAllBy()
    {
        // With a value that matches
        $recordSet = $this->_recordSet->findAllBy('camelCase', 'test1');
        $this->assertType('WrsGroup_Model_RecordSet', $recordSet);

        // With a value that doesn't match
        $recordSet = $this->_recordSet->findAllBy('camelCase', 'abcdefg');
        $this->assertType('WrsGroup_Model_RecordSet', $recordSet);
        $this->assertEquals(0, count($recordSet));

        // Do a record set with more than one matching value
        $results = array(
            new TestModel(array(
                'camelCase' => 'test1',
                'under_score' => 'test2',
                'nocase' => 'test3'
            )),
            new TestModel(array(
                'camelCase' => 'test4',
                'under_score' => 'test5',
                'nocase' => 'test6'
            )),
            new TestModel(array(
                'camelCase' => 'test1',
                'under_score' => 'test5',
                'nocase' => 'test6'
            ))
        );
        $recordSet = new WrsGroup_Model_RecordSet($results, 'TestModel');
        $recordSet = $recordSet->findAllBy('camelCase', 'test1');
        $this->assertType('WrsGroup_Model_RecordSet', $recordSet);
        $this->assertEquals(2, count($recordSet));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindAllByThrowsException()
    {
        $recordSet = $this->_recordSet->findAllBy('', 'test1');
    }

    public function testCall()
    {
        $result = $this->_recordSet->findOneByCamelCase('test1');
        $this->assertType('WrsGroup_Model_DomainObject_Abstract', $result);

        $result = $this->_recordSet->findByCamelCase('test1');
        $this->assertType('WrsGroup_Model_DomainObject_Abstract', $result);

        $result = $this->_recordSet->findAllByCamelCase('test1');
        $this->assertType('WrsGroup_Model_RecordSet', $result);
    }
}
