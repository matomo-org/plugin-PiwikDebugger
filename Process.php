<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger;

use Piwik\Plugins\PiwikDebugger\Process\ShellExecProcess;
use Piwik\Plugins\PiwikDebugger\Process\InRequestProcess;
use Piwik\CliMulti\CliPhp;
use Piwik\Filesystem;
use Exception;

/**
 * The base type representing processes that have been or will be executed by the Web Shell
 * component of the PiwikDebugger plugin.
 *
 * This class and its descendants provide asynchronous program execution logic and the logic
 * to retrieve a program's output and return code while it's being executed.
 */
class Process
{
    /**
     * A unique ID representing the process execution. This will normally be the time
     * the program was executed.
     *
     * @var string
     */
    protected $executionId;

    /**
     * The shell command to execute. Can be null in order to reference a running process.
     *
     * @var string|null
     */
    protected $command;

    /**
     * Constructor.
     *
     * @param string $executionId See {@link $executionid}.
     * @param string|null $command See {@link $command}.
     */
    public function __construct($executionId, $command = null)
    {
        $this->executionId = $executionId;
        $this->command = $command;

        $this->makeProcessOutputTmpDir();
    }

    /**
     * Cleans up execution resources including the file containing the process' output
     * and the file containing the process' return code.
     */
    public function finalizeExecution()
    {
        @unlink($this->getPathToProcessOutputFile());
        @unlink($this->getPathToProcessReturnCodeFile());
    }

    /**
     * Creates a process that can be executed in the best way possible. If shell_exec
     * is available, the result is an instance of ShellExecProcess. If it is not available
     * and the command is a Piwik Console command, then the result is an instance of
     * InRequestProcess.
     *
     * If $command is null, the result is a Process instance which can only be used
     * to reference a running process.
     *
     * @param string $executionId See {@link $executionid}
     * @param string|null $command See {@link $command}
     * @return Process
     * @throws Exception if $command is not a Piwik Console command and shell_exec is disabled or
     *                   if shell_exec is enabled, $command is a Piwik Console command and the PHP binary cannot be found
     */
    public static function factory($executionId, $command = null)
    {
        if ($command === null) {
            return new Process($executionId);
        } else {
            return self::makeNewProcessInstance($executionId, $command);
        }
    }

    private static function makeNewProcessInstance($executionId, $command)
    {
        list($command, $commandExecutable) = self::normalizeCommandAndExecutablePath($command);

        $isPiwikConsoleCommand = self::isPiwikConsoleCommand($commandExecutable);

        if (function_exists('shell_exec')) {
            // if running console script, make sure to prepend command w/ correct PHP binary
            if ($isPiwikConsoleCommand) {
                $command = self::findPhpBinary() . " " . $command;
            }

            return new ShellExecProcess($executionId, $command);
        } else if ($isPiwikConsoleCommand) {
            return new InRequestProcess($executionId, $command);
        } else {
            throw new Exception("Cannot run '$commandExecutable' since shell_exec() is not available.");
        }
    }

    /**
     * Returns as much of the process' output as is available.
     *
     * @param int $outputStart The position in the output to return in the result.
     * @return string
     */
    public function getOutput($outputStart = 0)
    {
        $pathToOutput = $this->getPathToProcessOutputFile($this->executionId);

        if (!file_exists($pathToOutput)) {
            return '';
        }

        return file_get_contents($pathToOutput, $use_include_path = false, $context = null, (int)$outputStart);
    }

    /**
     * Executes the process. Implemented by descendants.
     */
    public function execute()
    {
        throw new Exception("Not implemented.");
    }

    /**
     * Returns the processes return code, or null if it is still running.
     *
     * @param int|null
     */
    public function getReturnCode()
    {
        $returnCodeFilePath = $this->getPathToProcessReturnCodeFile();
        if (!file_exists($returnCodeFilePath)) {
            return null;
        }

        $returnCode = trim(file_get_contents($returnCodeFilePath));
        if (strlen($returnCode) === 0) {
            return null;
        } else {
            return (int)$returnCode;
        }
    }

    /**
     * Returns the path to the file that contains this process' return code.
     *
     * @return string
     */
    public function getPathToProcessReturnCodeFile()
    {
        return self::getPathToProcessOutputDir() . "/{$this->executionId}.rc";
    }

    /**
     * Returns the path to the file that contains this process' return code.
     *
     * @return string
     */
    public function getPathToProcessOutputFile()
    {
        return self::getPathToProcessOutputDir() . "/{$this->executionId}.out";
    }

    private function makeProcessOutputTmpDir()
    {
        Filesystem::mkdir(self::getPathToProcessOutputDir());
    }

    public static function getPathToProcessOutputDir()
    {
        return PIWIK_INCLUDE_PATH . '/tmp/debugger_process_out';
    }

    private static function normalizeCommandAndExecutablePath($command)
    {
        $command = trim($command);

        $commandParts = explode(' ', $command);

        // make sure the path to the executable is relative to PIWIK_INCLUDE_PATH
        $commandExecutable = $commandParts[0];
        if (substr($commandExecutable, 0, 1) == '.') {
            $commandExecutable = PIWIK_INCLUDE_PATH . '/' . $commandExecutable;
        }
        $commandParts[0] = $commandExecutable;

        $command = implode(' ', $commandParts);

        return array($command, $commandExecutable);
    }

    private static function isPiwikConsoleCommand($commandExecutable)
    {
        return realpath($commandExecutable) == PIWIK_INCLUDE_PATH . '/console';
    }

    private static function findPhpBinary()
    {
        $cliPhp = new CliPhp();

        $phpBinary = $cliPhp->findPhpBinary();
        if (empty($phpBinary)) {
            throw new Exception("Cannot find PHP executable!");
        }

        return $phpBinary;
    }
}