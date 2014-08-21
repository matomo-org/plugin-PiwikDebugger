/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

angular.module('piwikApp').controller('SqlBrowserController', function($scope, piwikApi){

    $scope.resultSet = []
    $scope.predefinedQueries = [];

    piwikApi.fetch({
        method: 'PiwikDebugger.getTablePrefix'
    }).then(function (response) {
        var prefix = response.value;
        $scope.tablePrefix = prefix;
        $scope.predefinedQueries = [
            'select * from ' + prefix +  'option;',
            'show full processlist;',
            'show status;',
            'show session variables;',
            'show global variables;'
        ];
    })

    $scope.execQuery = function (sqlQuery) {
        $scope.isLoading = true;

        return piwikApi.fetch({
            method: 'PiwikDebugger.execQuery',
            query: sqlQuery
        }).then(function (response) {
            $scope.result = response;
        }).finally(function () {
            $scope.isLoading = false;
        });
    };

});
