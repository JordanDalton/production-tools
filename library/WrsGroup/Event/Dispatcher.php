<?php
/**
 * Event dispatcher class for event-driven architecture
 * This is a singleton. Similar to PEAR class Event_Dispatcher.
 *
 * @category WrsGroup
 * @package Event
 * @author Eugene Morgan
 */
class WrsGroup_Event_Dispatcher
{
    protected $_observers;

    /**
     * Singleton instance
     *
     * @var WrsGroup_Ldap
     */
    private static $_instance = null;

    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton instance
     *
     * @return EventDispatcher
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Adds an observer
     *
     * @param mixed $callback Currently must be an array with first item an
     *     instance of an object and second item the name of a callback method
     * @param string $eventName The name of the event to observe
     */
    public function addObserver($callback, $eventName)
    {
        if (!isset($this->_observers[$eventName])) {
            $this->_observers[$eventName] = array();
        }
        $this->_observers[$eventName][] = $callback;
    }

    /**
     * Creates an event and calls "event handler" methods on observers
     * 
     * @param object $subject The object that is firing the event
     * @param string $eventName The name of the event
     * @param mixed $info Optional information passed about the event
     */
    public function fireEvent($subject, $eventName, $info = null)
    {
        // Create an event object
        $event = new WrsGroup_Event($subject, $eventName, $info);

        // Call the callback method for each observer. Kind of like in
        // JavaScript, its only parameter is the event itself.
        $observers = $this->_observers[$eventName];
        foreach ($observers as $callback) {
            $object = $callback[0];
            $method = $callback[1];
            $object->$method($event);
        }
    }
}
