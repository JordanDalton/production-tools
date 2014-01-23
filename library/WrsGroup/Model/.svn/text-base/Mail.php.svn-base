<?php
/**
 * A generic domain object for an e-mail
 *
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan
 */
class WrsGroup_Model_Mail extends WrsGroup_Model_DomainObject_Abstract
{
    protected $_data = array(
        'subject' => null,
        'bodyHtml' => null,
        'bodyText' => null,
        'replyToAddress' => null,
    );

    /**
     * An array of Zend_Mime_Part objects
     *
     * @var array
     */
    protected $_attachments = array();

    /**
     * @var WrsGroup_Model_RecordSet A record set of e-mail address objects
     */
    protected $_toRecipients;

    /**
     * @var WrsGroup_Model_RecordSet A record set of e-mail address objects
     */
    protected $_ccRecipients;

    /**
     * @var WrsGroup_Model_RecordSet A record set of e-mail address objects
     */
    protected $_bccRecipients;

    /**
     * @var Model_EmailAddress
     */
    protected $_from;
}