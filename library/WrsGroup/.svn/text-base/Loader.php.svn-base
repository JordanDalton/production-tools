<?php
/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Class to allow Zend_Loader to handle autoloading of models in such a way
 * that ModuleName_Model_ClassName will map to /modulename/models/ClassName.php
 *
 * In order for this to work, we must have added the path to "/models/" for each
 * module to the include path. This is done in a controller plugin.
 *
 * @category WrsGroup
 * @author Eugene Morgan
 */
class WrsGroup_Loader extends Zend_Loader
{
    protected static $_moduleBasePath;

    /**
     * @var array Flipped array of module names (module names are keys)
     */
    protected static $_moduleNames;

    /**
     * Solves case sensitivity/plural issues when trying to autoload class
     * from within models directory of a module. Documentation below is from
     * Zend.
     *
     * Loads a class from a PHP file.  The filename must be formatted
     * as "$class.php".
     *
     * If $dirs is a string or an array, it will search the directories
     * in the order supplied, and attempt to load the first matching file.
     *
     * If $dirs is null, it will split the class name at underscores to
     * generate a path hierarchy (e.g., "Zend_Example_Class" will map
     * to "Zend/Example/Class.php").
     *
     * If the file was not found in the $dirs, or if no $dirs were specified,
     * it will attempt to load it from PHP's include_path.
     *
     * @param string $class      - The full class name of a Zend component.
     * @param string|array $dirs - OPTIONAL Either a path or an array of paths
     *                             to search.
     * @return void
     * @throws Zend_Exception
     */
    public static function loadClass($class, $dirs = null)
    {
        // Same as Zend ... if class exists, do nothing
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }

        // Same as Zend ... throw exception if dirs is not in right format
        if ((null !== $dirs) && !is_string($dirs) && !is_array($dirs)) {
            require_once 'Zend/Exception.php';
            $msg = 'Directory argument must be a string or an array';
            throw new Zend_Exception($msg);
        }

        // Custom code ... get all module names
        self::$_moduleBasePath = APP_ROOT . DIRECTORY_SEPARATOR . 'application' .
                                 DIRECTORY_SEPARATOR . 'modules';
        if (!self::$_moduleNames) {
            self::$_moduleNames = self::_getModuleNames();
        }

        // If first two parts of class match a module plus the word 'Model',
        // change the class file name accordingly and tell Zend_Loader where
        // to load the class
        $parts = explode('_', $class);
        if (!isset($parts[0]) && !isset($parts[1])) {
            // Not a match ... do the normal thing
            parent::loadClass($class, $dirs);
            return;
        }

        // Load generic models not specific to a module
        if ($parts[0] == 'Model') {
            $dirs = APP_ROOT . DIRECTORY_SEPARATOR . 'application' .
                    DIRECTORY_SEPARATOR . 'models';
            unset($parts[0]);
            $class = implode('_', $parts);
            parent::loadClass($class, $dirs);
            return;
        }
        
        $firstPart = self::_formatModuleName($parts[0]);
        if (isset(self::$_moduleNames{$firstPart}) && $parts[1] == 'Model') {
            // Match!
            $dirs = self::$_moduleBasePath . DIRECTORY_SEPARATOR . $firstPart .
                    DIRECTORY_SEPARATOR . 'models';
            unset($parts[0]);
            unset($parts[1]);
            $class = implode('_', $parts);
        }
        parent::loadClass($class, $dirs);
    }

    /**
     * Had to bring this over exactly as is from Zend_Loader so that autoload
     * would use this child class and not go directly to the parent.
     *
     * Documentation below is from Zend.
     *
     * spl_autoload() suitable implementation for supporting class autoloading.
     *
     * Attach to spl_autoload() using the following:
     * <code>
     * spl_autoload_register(array('Zend_Loader', 'autoload'));
     * </code>
     *
     * @param string $class
     * @return string|false Class name on success; false on failure
     */
    public static function autoload($class)
    {
        try {
            self::loadClass($class);
            return $class;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Gets module names for a given application
     *
     * @return array Array with module names as keys -- for faster searching
     */
    protected static function _getModuleNames()
    {
        $moduleNames = array();
        $di = new DirectoryIterator(self::$_moduleBasePath);
        foreach ($di as $node) {
            if (!$node->isDir()) {
                continue;
            }
            if (!$node->isDot() && $node->getFilename() != '.svn') {
                $moduleNames[] = $node->getFilename();
            }
        }
        // Flip the array
        return array_flip($moduleNames);
    }

    /**
     * Converts module name from CamelCase to lower-case
     *
     * @param string $string The module name
     * @return string The converted module name
     */
    protected static function _formatModuleName($string)
    {
        $lower = strtolower($string);
        $module = $lower;
        for ($i = 1; $i < strlen($string); $i++) {
            // Insert hyphen where any uppercase characters were
            if (substr($string, $i, 1) != substr($lower, $i, 1)) {
                $reversePos = strlen($string) - $i;
                $module = substr_replace($module, '-', -$reversePos, 0);
            }
        }
        return $module;
    }
}