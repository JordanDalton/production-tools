<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

require_once realpath(APPLICATION_PATH .
    '/../library/WrsGroup/Model/DomainObject/Abstract.php');

class DomainObjectConcreteTestClass extends WrsGroup_Model_DomainObject_Abstract
{
    protected $_data = array(
        'dataField1' => null,
        'dataField2' => null,
        'data_field3' => null,
    );

    protected $_dbFieldMap = array(
        'dataField1' => 'df1',
        'dataField2' => 'df2',
        'propertyField1' => 'pf1',
        'propertyField2' => 'pf2',
    );

    protected $_propertyField1;
    protected $_propertyField2;
}

class WrsGroup_Model_DomainObject_AbstractTest extends ModelTestCase
{
    public function testToArrayWithDataSetByConstructor()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'dataField1' => 'data1',
            'dataField2' => 'data2',
            'dataField3' => 'data3',
            'propertyField1' => 'propertyData1',
            'propertyField2' => 'propertyData2',
        ));
        $array = $object->toArray();
        $this->assertEquals(5, count($array));
    }

    public function testNonSpecifiedPropertiesAreJustIgnored()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'dataField1' => 'data1',
            'dataField2' => 'data2',
            'dataField95' => 'data95',
        ));
        $this->assertEquals(2, count($object->getPopulated()));
    }

    public function testIgnoreNulls()
    {
        $object = new DomainObjectConcreteTestClass(
            array(
                'dataField1' => 'data1',
                'dataField2' => null,
            ),
            array('ignoreNulls' => true)
        );
        $this->assertFalse($object->isPopulated('dataField2'));
    }

    public function testIsPopulated()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'dataField1' => 'data1',
            'dataField2' => 'data2',
            'data_field3' => 'data3',
        ));
        $this->assertTrue($object->isPopulated('dataField1'));
        $this->assertTrue($object->isPopulated('data_field1'));
        $this->assertTrue($object->isPopulated('dataField3'));
        $this->assertFalse($object->isPopulated('propertyField1'));
    }

    public function testToArrayWithPropertiesSetLater()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'dataField1' => 'data1',
            'dataField2' => 'data2',
            'dataField3' => 'data3',
        ));
        $object->setPropertyField1('propertyData1');
        $object->setPropertyField2('propertyData2');
        $array = $object->toArray();
        $this->assertEquals(5, count($array));
    }

    public function testToArrayWithUnderscoresInProperties()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'data_field1' => 'data1',
            'data_field2' => 'data2',
            'data_field3' => 'data3',
            'property_field1' => 'propertyData1',
            'property_field2' => 'propertyData2',
        ));
        $array = $object->toArray();
        $this->assertEquals(5, count($array));
        $this->assertArrayHasKey('dataField1', $array);
        $this->assertArrayHasKey('propertyField1', $array);
    }

    public function testGetMagicMethod()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'data_field1' => 'data1',
            'data_field2' => 'data2',
            'property_field1' => 'propertyData1',
            'property_field2' => 'propertyData2',
        ));
        $this->assertEquals('propertyData1', $object->propertyField1);
        $this->assertEquals('propertyData1', $object->getPropertyField1());
    }

    public function testGetPopulated()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'data_field1' => 'data1',
            'property_field1' => 'propertyData1',
        ));
        $populated = $object->getPopulated(
            WrsGroup_Model_DomainObject_Abstract::PROPERTY_FORMAT_DB);
        $this->assertType('array', $populated);
        $this->assertEquals(2, count($populated));
        $this->assertFalse(isset($populated['dataField1']));
        $this->assertFalse(isset($populated['dataField2']));
        $this->assertFalse(isset($populated['df2']));
        $this->assertTrue(isset($populated['df1']));
        $this->assertEquals('data1', $populated['df1']);
        $this->assertEquals('propertyData1', $populated['pf1']);
    }

    public function testUnpopulate()
    {
        $object = new DomainObjectConcreteTestClass(array(
            'data_field1' => 'data1',
            'property_field1' => 'propertyData1',
        ));
        $populated = $object->getPopulated();
        $this->assertArrayHasKey('dataField1', $populated);
        $populated = $object->getPopulated(
            WrsGroup_Model_DomainObject_Abstract::PROPERTY_FORMAT_DB);
        $this->assertArrayHasKey('df1', $populated);
        $object->unpopulate('dataField1');
        $populated = $object->getPopulated();
        $this->assertArrayNotHasKey('dataField1', $populated);
        $populated = $object->getPopulated(
            WrsGroup_Model_DomainObject_Abstract::PROPERTY_FORMAT_DB);
        $this->assertArrayNotHasKey('df1', $populated);
    }

    public function testPopulateWithStdClassObject()
    {
        $obj = (object) array('dataField1' => 'data1');
        $domainObj = new DomainObjectConcreteTestClass($obj);
        $this->assertEquals(1, count($domainObj->getPopulated()));
        $this->assertTrue($domainObj->isPopulated('dataField1'));
    }

    public function testPopulateWithObjectThatHasToArrayMethod()
    {
        // This is kind of weird but you can create a new domain object
        // with a domain object as the data
        $object = new DomainObjectConcreteTestClass(array(
            'data_field1' => 'data1',
            'property_field1' => 'propertyData1',
        ));
        $object2 = new DomainObjectConcreteTestClass($object);
        $this->assertInstanceOf('DomainObjectConcreteTestClass', $object2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPopulateThrowsInvalidArgumentException()
    {
        $object = new DomainObjectConcreteTestClass('some string');
    }
}

