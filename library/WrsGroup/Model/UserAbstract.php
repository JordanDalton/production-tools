<?php
/**
 * An abstract user domain object
 *
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan
 */
abstract class WrsGroup_Model_UserAbstract 
    extends WrsGroup_Model_DomainObject_Abstract
    implements WrsGroup_Model_UserInterface
{
    const LETS_GEL = 'lgi';
    const WRS_GROUP = 'wrs';

    /**
     * @var bool
     */
    protected $_active;

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_password;

    /**
     * @var WrsGroup_Model_EmailAddress
     */
    protected $_emailAddress;

    /**
     * @var WrsGroup_Model_RecordSet
     */
    protected $_groups;
}
