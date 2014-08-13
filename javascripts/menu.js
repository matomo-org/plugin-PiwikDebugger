/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

$(document).ready(function () {

    var linksToOpenInNewWindow = ['Edit Files', 'Server Stats', 'Browse Database'];

    for (var index = 0; index < linksToOpenInNewWindow.length; index++) {
        var title = linksToOpenInNewWindow[index];
        $('.Menu-tabList ul li a:contains("' + title + '")').attr('target', '_blank');
    }
});
