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

/**
 * This class allows you to add, remove or rename menu items.
 * To configure a menu (such as Admin Menu, Reporting Menu, User Menu...) simply call the corresponding methods as
 * described in the API-Reference http://developer.piwik.org/api-reference/Piwik/Menu/MenuAbstract
 */
class Menu extends \Piwik\Plugin\Menu
{
    public function configureAdminMenu(MenuAdmin $menu)
    {
        // with custom category
        $menu->add('Debugger', '', array('module' => 'PiwikDebugger', 'action' => ''), true, $orderId = 30);
        $menu->add('Debugger', 'Files', array('module' => 'PiwikDebugger', 'action' => 'editFiles'), true, $orderId = 30);
        $menu->add('Debugger', 'Database', array('module' => 'PiwikDebugger', 'action' => 'queryDb'), true, $orderId = 30);

        // or reusing an existing category
        // $menu->addSettingsItem('My Admin Item', array('module' => 'PiwikDebugger', 'action' => ''), $orderId = 30);
        // $menu->addPlatformItem('My Admin Item', array('module' => 'PiwikDebugger', 'action' => ''), $orderId = 30);
    }
}
