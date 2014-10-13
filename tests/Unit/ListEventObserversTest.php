<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger\tests\Unit;

use Piwik\Config;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Tests\Framework\TestCase\ConsoleCommandTestCase;

/**
 * @group PiwikDebugger
 * @group PiwikDebugger_ListEventObserversTest
 */
class ListEventObserversTest extends ConsoleCommandTestCase
{
    public function setUp()
    {
        parent::setUp();

        // only load a couple plugins so we have regular output
        Config::getInstance()->Plugins['Plugins'] = array('Actions', 'CoreConsole', 'Goals', 'UserSettings', 'PiwikDebugger');
        PluginManager::getInstance()->doNotLoadAlwaysActivatedPlugins(true);
    }

    public function tearDown()
    {
        PluginManager::unsetInstance();
    }

    public function testCommandCorrectlyCapturesPluginsWithObservers()
    {
        $result = $this->applicationTester->run(array(
            'command' => 'debugger:list-event-observers',
            'event' => 'AssetManager.getJavaScriptFiles'
        ));

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());

        $expectedOutput = "Plugins with observers for AssetManager.getJavaScriptFiles in order of execution:
Actions
Goals
PiwikDebugger";

        $this->assertContains($expectedOutput, trim($this->applicationTester->getDisplay()));
    }

    public function testCommandCorrectlyExcludesNonTrackerPluginsWhenTrackerOptionSupplied()
    {
        $result = $this->applicationTester->run(array(
            'command' => 'debugger:list-event-observers',
            'event' => 'AssetManager.getJavaScriptFiles',
            '--tracker' => true
        ));

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());

        $expectedOutput = "Plugins with observers for AssetManager.getJavaScriptFiles in order of execution:
Actions
Goals";
        $this->assertContains($expectedOutput, trim($this->applicationTester->getDisplay()));
    }

    public function testCommandCorrectlyOutputsNoPluginsWhenInvalidEventSupplied()
    {
        $result = $this->applicationTester->run(array(
            'command' => 'debugger:list-event-observers',
            'event' => 'NonExistant.Event'
        ));

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());

        $expectedOutput = "There are no plugins with observers for NonExistant.Event.";
        $this->assertContains($expectedOutput, trim($this->applicationTester->getDisplay()));
    }
}
