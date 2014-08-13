/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

$(document).ready(function () {

    // always open the editor in a new window
    $('.Menu-tabList ul li a:contains("Edit Files")').attr('target', '_blank');
    $('.Menu-tabList ul li a:contains("Server Stats")').attr('target', '_blank');
});
