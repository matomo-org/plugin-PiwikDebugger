<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger;

use Piwik\Menu\MenuAdmin;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureAdminMenu(MenuAdmin $menu)
    {
        if (!Piwik::hasUserSuperUserAccess()) {
            return;
        }

        $menu->addDiagnosticItem('Config', array('module' => 'PiwikDebugger', 'action' => 'config'), $orderId = 80);
        $menu->addDiagnosticItem('Edit Files', array('module' => 'PiwikDebugger', 'action' => 'editFiles'), $orderId = 81);
        $menu->addDiagnosticItem('Query Database', array('module' => 'PiwikDebugger', 'action' => 'queryDb'), $orderId = 82);
        $menu->addDiagnosticItem('PHP Info', array('module' => 'PiwikDebugger', 'action' => 'phpInfo'), $orderId = 83);
        $menu->addDiagnosticItem('Server Stats', array('module' => 'PiwikDebugger', 'action' => 'serverStats'), $orderId = 84);
    }
}
