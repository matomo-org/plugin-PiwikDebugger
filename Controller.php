<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger;

use Piwik\View;

class Controller extends \Piwik\Plugin\ControllerAdmin
{

    public function queryDb()
    {
        return $this->renderMe('queryDb');
    }

    public function config()
    {
        return $this->renderMe('config');
    }

    public function editFiles()
    {
        header('Location: plugins/PiwikDebugger/libs/icecoder');
        exit;
    }

    public function serverStats()
    {
        header('Location: plugins/PiwikDebugger/libs/linux-dash');
        exit;
    }

    public function phpInfo()
    {
        ob_start();
        phpinfo();

        return $this->renderMe('phpInfo', array(
            'phpinfo' => ob_get_clean()
        ));
    }

    protected function renderMe($template, array $variables = array())
    {
        $view = new View('@' . $this->pluginName . '/' . $template);
        $this->setBasicVariablesView($view);

        foreach ($variables as $key => $value) {
            $view->$key = $value;
        }

        return $view->render();
    }
}
