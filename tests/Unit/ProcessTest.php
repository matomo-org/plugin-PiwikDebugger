<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger\tests\Unit;

use Piwik\Filesystem;
use Piwik\Plugins\PiwikDebugger\Process;
use PHPUnit_Framework_TestCase;

/**
 * @group PiwikDebugger
 * @group PiwikDebugger_ProcessTest
 */
class ProcessTest extends PHPUnit_Framework_TestCase
{
    const EXECUTION_ID = 12345;

    public function setUp()
    {
        $this->deleteCommandOutput();
    }

    public function tearDown()
    {
        $this->deleteCommandOutput();
    }

    public function testFactoryMethodCreatesCorrectClassWhenCommandIsNew()
    {
        $process = Process::factory(self::EXECUTION_ID, "./console help");

        $this->assertInstanceOf("Piwik\\Plugins\\PiwikDebugger\\Process\\ShellExecProcess", $process);
    }

    public function testFactoryMethodCreatesCorrectClassWhenCommandExists()
    {
        Filesystem::mkdir(Process::getPathToProcessOutputDir());
        file_put_contents(Process::getPathToProcessOutputDir() . "/" . self::EXECUTION_ID . ".out", "");

        $process = Process::factory(self::EXECUTION_ID); // no pid file should result in InRequestProcess being created

        $this->assertInstanceOf("Piwik\\Plugins\\PiwikDebugger\\Process\\InRequestProcess", $process);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid execution ID
     */
    public function testFactoryMethodFailsWhenCommandIsSupposedToExistButDoesnt()
    {
        $process = Process::factory(self::EXECUTION_ID);
    }

    private function deleteCommandOutput()
    {
        Filesystem::unlinkRecursive(Process::getPathToProcessOutputDir(), $deleteRoot = true);
    }
}