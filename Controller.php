<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger;

use Piwik\Db;
use Piwik\View;

class Controller extends \Piwik\Plugin\ControllerAdmin
{

    public function queryDb()
    {
        $view = new View('@PiwikDebugger/queryDb.twig');
        $this->setBasicVariablesView($view);
        $view->answerToLife = '42';

        return $view->render();
    }

    public function editFiles()
    {
        $view = new View('@PiwikDebugger/editFiles.twig');
        $this->setBasicVariablesView($view);
        $view->answerToLife = '42';

        return $view->render();
    }
}
