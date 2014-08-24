<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger\Process;

use Piwik\Plugins\PiwikDebugger\Process;

/**
 * Implementation of Process that will execute a command using shell_exec. The recording of output and
 * of the return code is done by the ./plugins/PiwikDebuger/misc/execute_command.sh script.
 */
class ShellExecProcess extends Process
{
    public function execute()
    {
        shell_exec("sh " . PIWIK_INCLUDE_PATH . "/plugins/PiwikDebugger/misc/execute_command.sh '"
            . addslashes($this->command) . "' '"
            . $this->getPathToProcessOutputFile() . "' '"
            . $this->getPathToProcessReturnCodeFile() . "' &");
    }
}