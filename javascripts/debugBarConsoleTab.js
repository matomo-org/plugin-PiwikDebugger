/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

require("piwik.DebugBarWidgets").PiwikConsole = PhpDebugBar.Widget.extend({
    className: 'debug-bar-piwik-console',

    render: function () {
        this.$el.html('<div ng-include="\'plugins/PiwikDebugger/angularjs/piwikconsole/piwikconsole.html\'"></div>');
    }
});