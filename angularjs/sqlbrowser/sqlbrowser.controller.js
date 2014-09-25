/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    angular.module('piwikApp').controller('SqlBrowserController', SqlBrowserController);

    SqlBrowserController.$inject = ['piwikApi'];

    function SqlBrowserController(piwikApi){

        var vm = this;
        vm.resultSet = [];
        vm.predefinedQueries = [];
        vm.execQuery = execQuery;

        fetchTablePrefix();

        function fetchTablePrefix()
        {
            piwikApi.fetch({
                method: 'PiwikDebugger.getTablePrefix'
            }).then(function (response) {
                vm.tablePrefix = response.value;
                vm.predefinedQueries = getPredefinedQueries(vm.tablePrefix);
            });
        }

        function getPredefinedQueries(prefix)
        {
            return [
                'select * from ' + prefix +  'option;',
                'select VERSION();',
                'show full processlist;',
                'show status;',
                'show session variables;',
                'show global variables;'
            ];
        }

        function execQuery(sqlQuery) {
            vm.isLoading = true;

            return piwikApi.fetch({
                method: 'PiwikDebugger.execQuery',
                query: sqlQuery
            }).then(function (response) {
                vm.result = response;
            }).finally(function () {
                vm.isLoading = false;
            });
        }

    }
})();
