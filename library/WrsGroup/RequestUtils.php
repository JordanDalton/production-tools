<?php
class WrsGroup_RequestUtils
{
    /**
     * Given an array of parameters, this method returns a string with
     * all the key value pairs of the array separated by double underscore.
     * Throws an error if any of the keys or values contains a double underscore.
     *
     * @param array $params List of parameters
     * @return string String representation of parameters
     */
    public static function stringifyParams($params)
    {
        $string = '';
        foreach ($params as $key => $value) {
            self::_validateParam($key);
            self::_validateParam($value);
            if (!$string) {
                $string = $key . '=' . $value;
                continue;
            }
            $string .= '__' . $key . '=' . $value;
        }
        return $string;
    }

    /**
     * Takes string created by stringifyParams() and converts it back
     * to an array.
     *
     * @param string $string The string to convert
     * @return array Parameters originally passed to stringifyParams()
     */
    public static function getParamsForRedirect($string)
    {
        // Defaults:
        $action = 'index';
        $controller = 'index';
        $module = 'default';

        $pairs = explode('__', $string);
        $params = array();
        foreach ($pairs as $pair) {
            $array = explode('=', $pair);
            if (count($array) < 2) {
                continue;
            }
            $params[$array[0]] = $array[1];
        }

        $otherParams = array();
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'action':
                    $action = $value;
                    break;
                case 'controller':
                    $controller = $value;
                    break;
                case 'module':
                    $module = $value;
                    break;
                default:
                    $otherParams[$key] = $value;
                    break;
            }
        }
        return array(
            'action' => $action,
            'controller' => $controller,
            'module' => $module,
            'other' => $otherParams
        );
    }

    private static function _validateParam($str)
    {
        if (strpos($str, '__') !== false || strpos($str, '=') !== false) {
            throw new Exception('Parameter ' . $str . ' contains an illegal ' .
                                'character.');
        }
    }
}