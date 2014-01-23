<?php
/**
 * Description of Event
 *
 * @category WrsGroup
 * @package Event
 * @author Eugene Morgan
 */
class WrsGroup_Event
{
    protected $_name;
    protected $_subject;
    protected $_info;
    
    /**
     * Constructor
     *
     * @param object $subject The object firing the event
     * @param string $name The name of the event being fired
     * @param mixed $info Additional information about the event
     */
    public function __construct($subject, $name, $info = null)
    {
        $this->_name = $name;
        $this->_subject = $subject;
        $this->_info = $info;
    }

    /**
     * Gets the name of the event that was fired
     *
     * @return string The event name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Gets the subject (object that fired the event)
     *
     * @return object Gets the object that fired the event
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Gets additional information that may have optionally been passed when
     * the event was fired
     *
     * @return mixed Additional information about the event
     */
    public function getInfo()
    {
        return $this->_info;
    }
}
