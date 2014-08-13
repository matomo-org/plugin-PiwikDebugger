<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger;

use Piwik\Piwik;

class PiwikDebugger extends \Piwik\Plugin
{
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
        );
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

}
