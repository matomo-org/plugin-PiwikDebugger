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
use Piwik\Plugins\PiwikDebugger\Process\ShellExecProcess;
use PHPUnit_Framework_TestCase;

/**
 * @group PiwikDebugger
 * @group PiwikDebugger_ShellExecProcessTest
 */
class ShellExecProcessTest extends PHPUnit_Framework_TestCase
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

    public function getCommandsToTest()
    {
        return array( array(PIWIK_INCLUDE_PATH . "/console help", "help [--xml] [--format=\"...\"]"),
                      array("ls") );
    }

    /**
     * @dataProvider getCommandsToTest
     */
    public function testExecuteSucceeds($command, $outputContains = null)
    {
        $process = new ShellExecProcess(self::EXECUTION_ID, $command);

        $process->execute();

        $this->assertTrue(file_exists($process->getPathToProcessOutputFile()));

        while (($returnCode = $process->getReturnCode()) === null) {
            usleep(100000);
        }

        $this->assertEquals(0, $returnCode);

        if ($outputContains !== null) {
            $this->assertContains($outputContains, $process->getOutput());
        } else {
            $this->assertNotEmpty($process->getOutput());
        }
    }

    public function testExecuteFailsWhenProgramDoesntExist()
    {
        ob_start();

        $process = new ShellExecProcess(self::EXECUTION_ID, "dslkfjsd");
        $process->execute();

        while (($returnCode = $process->getReturnCode()) === null) {
            usleep(100000);
        }

        $this->assertNotEquals(0, $returnCode);

        $output = ob_get_contents();

        ob_end_clean();

        $this->assertEmpty($output);
    }

    private function deleteCommandOutput()
    {
        Filesystem::unlinkRecursive(Process::getPathToProcessOutputDir(), $deleteRoot = true);
    }
}