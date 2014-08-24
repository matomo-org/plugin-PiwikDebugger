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
use Piwik\Plugins\PiwikDebugger\Process\InRequestProcess;
use PHPUnit_Framework_TestCase;

/**
 * @group PiwikDebugger
 * @group PiwikDebugger_InRequestProcessTest
 */
class InRequestProcessTest extends PHPUnit_Framework_TestCase
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

    public function testExecuteSucceeds()
    {
        $process = new InRequestProcess(self::EXECUTION_ID, "./console help");

        $process->execute();

        $this->assertTrue(file_exists($process->getPathToProcessOutputFile()));

        $this->assertContains("help [--xml] [--format=\"...\"]", $process->getOutput());
        $this->assertEquals(0, $process->getReturnCode());
    }

    private function deleteCommandOutput()
    {
        Filesystem::unlinkRecursive(Process::getPathToProcessOutputDir(), $deleteRoot = true);
    }
}