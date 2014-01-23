<?php
/**
 *  My Rest Server
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 25, 2012, 10:26:59 AM
 */
class My_Rest_Server extends Zend_Rest_Server
{
    /**
     * Recursively iterate through a struct
     *
     * Recursively iterates through an associative array or object's properties
     * to build XML response.
     *
     * @param mixed $struct
     * @param DOMDocument $dom
     * @param DOMElement $parent
     * @return void
     */
    protected function _structValue($struct, DOMDocument $dom, DOMElement $parent)
    {
        $struct = (array) $struct;

        foreach ($struct as $key => $value)
        {
            if ($value === false) {
                $value = 0;
            } elseif ($value === true) {
                $value = 1;
            }

            if (ctype_digit((string) $key) || !ctype_alnum($key)) {
                $index = $key;
                $key = 'data';
            }

            if (is_array($value) || is_object($value)) {
                $element = $dom->createElement($key);
                $this->_structValue($value, $dom, $element);
            } else {
                $element = $dom->createElement($key);
                $element->appendChild($dom->createTextNode($value));
            }

            if(isset($index))
            	$element->setAttribute('index',$index);
                $parent->appendChild($element);
        }
    }	
}
/* End of file Server.php */
/* Location: library/MY/Rest/Server.php */