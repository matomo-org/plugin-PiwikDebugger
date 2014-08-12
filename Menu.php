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

class Menu extends \Piwik\Plugin\Menu
{
    public function configureAdminMenu(MenuAdmin $menu)
    {
        $menu->add('Debugger', '', array('module' => 'PiwikDebugger', 'action' => ''), true, $orderId = 30);
        $menu->add('Debugger', 'Files', array('module' => 'PiwikDebugger', 'action' => 'editFiles'), true, $orderId = 10);
        $menu->add('Debugger', 'Database', array('module' => 'PiwikDebugger', 'action' => 'queryDb'), true, $orderId = 20);
    }
}
