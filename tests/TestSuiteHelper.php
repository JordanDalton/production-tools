<?php
/**
 * Helper class for more easily creating PHPUnit test suites
 *
 */
class TestSuiteHelper
{
    /**
     * @var PHPUnit_Framework_TestSuite 
     */
    protected $_suite;

    /**
     * Constructor
     *
     * @param PHPUnit_Framework_TestSuite $suite 
     */
    public function __construct($suite)
    {
        $this->_suite = $suite;
    }

    public function addTestFilesRecursively($directory)
    {
        $it = new RecursiveDirectoryIterator($directory);
        $files = array();
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if (preg_match('/\.php$/', $file->getFilename())) {
                if ($file->getFilename() == 'AllTests.php') {
                    continue;
                }
                $files[] = $file->getPath() . '/' . $file->getFilename();
            }
        }
        $this->_suite->addTestFiles($files);
    }
}
