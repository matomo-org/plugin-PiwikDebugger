<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PiwikDebugger\Commands;

use Piwik\EventDispatcher;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugin\ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Plugin object proxy that replaces hook callbacks with another callback that records the name
 * of the proxied plugin.
 *
 * The names are pushed to an array that can later be used to determine what plugins have
 * observers for an event.
 */
class ListEventObserversPlugin
{
    /**
     * The proxied plugin object.
     *
     * @var Piwik\Plugin
     */
    private $plugin;

    /**
     * Reference to an array to add plugin names to.
     *
     * @var array
     */
    private $pluginsWithObservers;

    /**
     * Constructor.
     */
    public function __construct($plugin, &$pluginsWithObservers)
    {
        $this->plugin = $plugin;
        $this->pluginsWithObservers = &$pluginsWithObservers;
    }

    /**
     * Delegates to Plugin object methods. For all methods not overridden in this class.
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->plugin, $name), $arguments);
    }

    /**
     * Returns all hooks handled by the proxied plugin object. Replaces hook callbacks
     * with a closure that appends the plugin name to {@link $pluginsWithObservers}.
     *
     * @return callback[]
     */
    public function getListHooksRegistered()
    {
        $pluginsWithObservers = &$this->pluginsWithObservers;
        $pluginName = $this->plugin->getPluginName();

        $hooks = $this->plugin->getListHooksRegistered();
        foreach ($hooks as $hookName => &$callback) {
            $callback = function () use ($pluginName, &$pluginsWithObservers) {
                $pluginsWithObservers[] = $pluginName;
            };
        }

        return $hooks;
    }
}

/**
 * Plugin Manager proxy injected into an instance of EventDispatcher. Used to collect
 * plugin event observers as they are executed.
 *
 * This class will replace instances returned by Plugin\Manager::getPluginsLoadedAndActivated()
 * with proxies that in turn replace event handlers with separate events.
 */
class ListEventObserversPluginManager
{
    /**
     * Reference to an array to add plugin names to.
     *
     * @var array
     */
    private $pluginsWithObservers;

    /**
     * If true, only tracker plugins will be returned by getPluginsLoadedAndActivated().
     *
     * @var bool
     */
    private $onlyCheckTrackerPlugins;

    /**
     * Constructor.
     */
    public function __construct(&$pluginsWithObservers, $onlyCheckTrackerPlugins)
    {
        $this->pluginsWithObservers = &$pluginsWithObservers;
        $this->onlyCheckTrackerPlugins = $onlyCheckTrackerPlugins;
    }

    /**
     * Delegates to Plugin Manager methods. For all methods not overridden in this class.
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array(PluginManager::getInstance(), $name), $arguments);
    }

    /**
     * Calls Plugin\Manager::getPluginsLoadedAndActivated() and wraps each Plugin instance in
     * a ListEventObserversPlugin instance.
     *
     * @return ListEventObserversPlugin[]
     */
    public function getPluginsLoadedAndActivated()
    {
        $plugins = PluginManager::getInstance()->getPluginsLoadedAndActivated();

        $result = array();
        foreach ($plugins as $plugin) {
            if ($this->onlyCheckTrackerPlugins
                && !PluginManager::getInstance()->isTrackerPlugin($plugin)
            ) {
                continue;
            }

            $result[] = new ListEventObserversPlugin($plugin, $this->pluginsWithObservers);
        }
        return $result;
    }
}

/**
 * Command that lists the plugins with observers for a specific event.
 */
class ListEventObservers extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('debugger:list-event-observers');
        $this->setDescription('Lists plugins that define an event observer for a specific event.');
        $this->addArgument('event', InputArgument::REQUIRED, 'The event whose observers should be listed, ie, "AssetManager.getStylesheetFiles".');
        $this->addOption('tracker', null, InputOption::VALUE_NONE, "Whether to only check tracker plugins or not.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $event = $input->getArgument('event');
        $onlyCheckTrackerPlugins = $input->getOption('tracker');

        $pluginsWithObserver = array();

        $injectedPluginManager = new ListEventObserversPluginManager($pluginsWithObserver, $onlyCheckTrackerPlugins);
        $eventDispatcher = new EventDispatcher($injectedPluginManager);

        $eventDispatcher->postEvent($event, $params = array());

        if (empty($pluginsWithObserver)) {
            $output->writeln("<info>There are no plugins with observers for $event.</info>");
        } else {
            $output->writeln("<info>Plugins with observers for $event in order of execution:</info>");
            foreach ($pluginsWithObserver as $pluginName) {
                $output->writeln($pluginName);
            }
        }
    }
}