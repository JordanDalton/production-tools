<?php
/**
 * An e-mail address domain object
 *
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan
 */
class WrsGroup_Model_EmailAddress extends WrsGroup_Model_DomainObject_Abstract
{
    protected $_data = array(
        'address' => null,
        'name' => null
    );
}
