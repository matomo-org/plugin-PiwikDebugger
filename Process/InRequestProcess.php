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
use Piwik\Console;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Exception;

/**
 * Implementation of Process that will execute Piwik Console commands within the
 * HTTP request.
 */
class InRequestProcess extends Process
{
    public function execute()
    {
        // TODO: overcome request time limit w/ process forking if possible
        // TODO: known issue w/ core:archive, since CronArchive echos directly, the screen output is not outputted
        // to the output file. need to refactor CronArchive.
        $fileOutput = fopen($this->getPathToProcessOutputFile(), 'w');

        try {
            $command = substr($this->command, strpos($this->command, ' '));

            $input = new StringInput($command);
            $output = new StreamOutput($fileOutput);

            $console = new Console();
            $console->setAutoExit(false);
            $returnCode = $console->run($input, $output);

            fclose($fileOutput);
        } catch (Exception $ex) {
            fclose($fileOutput);
            throw $ex;
        }

        file_put_contents($this->getPathToProcessReturnCodeFile(), $returnCode);
    }
}