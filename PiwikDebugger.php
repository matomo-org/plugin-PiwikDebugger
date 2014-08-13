<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger;

use Piwik\Config;
use Piwik\Db;
use Piwik\Log;
use Piwik\Piwik;
use DebugBar\StandardDebugBar;
use DebugBar\JavascriptRenderer;

include PIWIK_INCLUDE_PATH . '/plugins/PiwikDebugger/vendor/autoload.php';

class PiwikDebugger extends \Piwik\Plugin
{
    /**
     * @var JavascriptRenderer
     */
    private $debugBarRenderer;

    /**
     * @var StandardDebugBar
     */
    private $debugBar;

    /**
     * @see Piwik\Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'API.Request.dispatch'            => 'checkApiPermission',
            'Request.dispatch'                => 'checkControllerPermission',
            'Request.dispatch.end'            => 'renderFooter',
            'Log.getAvailableWriters'         => 'addDebugBarWriter',
            'Request.dispatchCoreAndPluginUpdatesScreen' => 'initDebugBar'
        );
    }

    public function addDebugBarWriter(&$writers)
    {
        $writers['debugbar'] = array($this, 'logMessage');
    }

    public function logMessage($level, $tag, $datetime, $message)
    {
        if (empty($this->debugBar)) {
            return;
        }

        $levelToLabel = array(Log::DEBUG => 'debug', Log::ERROR => 'error', Log::INFO => 'info',
                              Log::NONE => 'none', Log::VERBOSE => 'verbose', Log::WARN => 'warning');

        $label = 'debug';
        if (array_key_exists($level, $levelToLabel)) {
            $label = $levelToLabel[$level];
        }

        $this->debugBar["messages"]->addMessage($message, $label, is_string($message));
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/PiwikDebugger/stylesheets/debugger.less";
        $stylesheets[] = "plugins/PiwikDebugger/angularjs/config/config.less";
        $stylesheets[] = "plugins/PiwikDebugger/angularjs/sqlbrowser/sqlbrowser.less";
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "plugins/PiwikDebugger/angularjs/sqlbrowser/sqlbrowser-controller.js";
        $jsFiles[] = "plugins/PiwikDebugger/angularjs/config/config-controller.js";
        $jsFiles[] = "plugins/PiwikDebugger/javascripts/menu.js";
    }

    public function initDebugBar()
    {
        $this->enableLoggingToDebugBarAndDisableAllOthers();

        $this->debugBar = new StandardDebugBar();

        $this->addDatabaseCollector();
        $this->addTwigCollector();
        $this->addConfigCollector();
    }

    public function renderFooter(&$string)
    {
        if (empty($this->debugBar)) {
            return;
        }

        $posEndBody = strrpos($string, '</body>');

        if (false !== $posEndBody) {
            $debugBarRenderer = $this->debugBar->getJavascriptRenderer();
            $debugBarRenderer->setBaseUrl('plugins/PiwikDebugger/vendor/maximebf/debugbar/src/DebugBar/Resources/');
            $debugBar  = $debugBarRenderer->renderHead();
            $debugBar .= $debugBarRenderer->render();

            $string = substr($string, 0, $posEndBody) . $debugBar . substr($string, $posEndBody);
        } else {
            $this->debugBar->sendDataInHeaders();
        }
    }

    public function checkApiPermission(&$parameters, $pluginName, $methodName)
    {
        if ($pluginName == 'PiwikDebugger') {
            $this->checkPermission();
        }
    }

    public function checkControllerPermission($module, $action)
    {
        if ($module != 'PiwikDebugger') {
            return;
        }

        $this->checkPermission();
    }

    private function checkPermission()
    {
        Piwik::checkUserHasSuperUserAccess();
    }

    private function enableLoggingToDebugBarAndDisableAllOthers()
    {
        $log = Config::getInstance()->log;
        $log['log_writers'] = array('debugbar');
        $log['log_level'] = 'VERBOSE';
        Config::getInstance()->log = $log;
    }

    private function addDatabaseCollector()
    {
        try {

            if (!Db::get()) {
                return;
            }

            $pdo = new \DebugBar\DataCollector\PDO\TraceablePDO(Db::get()->getConnection());
            $this->debugBar->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($pdo));

        } catch (\Exception $e) {

        }
    }

    private function addTwigCollector()
    {
        // We'd have to do this in core :(
        // $piwikTwig = new Twig();
        // $env = new \DebugBar\Bridge\Twig\TraceableTwigEnvironment($piwikTwig->getTwigEnvironment());
        // $this->debugbar->addCollector(new \DebugBar\Bridge\Twig\TwigCollector($env));
    }

    private function addConfigCollector()
    {
        $config = _parse_ini_file(Config::getInstance()->getLocalPath());
        $this->debugBar->addCollector(new \DebugBar\DataCollector\ConfigCollector($config));
    }

}
