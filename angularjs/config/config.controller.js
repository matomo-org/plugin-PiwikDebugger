/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    angular.module('piwikApp').controller('ConfigController', ConfigController);

    ConfigController.$inject = ['$scope', 'piwikApi'];

    function ConfigController($scope, piwikApi){

        var vm = this;
        vm.ini = {};
        vm.enableTrackerDebug = enableTrackerDebug;

        requestConfig();

        function enableTrackerDebug (enable) {
            enable = enable ? 1 : 0;

            return piwikApi.fetch({
                method: 'PiwikDebugger.enableTrackerDebug',
                enable: enable
            }).then(function (response) {
                $scope.$eval('config.ini.localConfig.Tracker.debug = ' + enable);
            }).catch(function () {
                $scope.$eval('config.ini.localConfig.Tracker.debug = ' + (enable ? 0 : 1));
            });
        }

        function requestConfig()
        {
            return piwikApi.fetch({
                method: 'PiwikDebugger.getConfig'
            }).then(function (response) {
                vm.ini = response;
            });
        }

    }
})();