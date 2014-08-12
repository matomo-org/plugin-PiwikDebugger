/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

angular.module('piwikApp').controller('SqlBrowserController', function($scope, piwikApi){

    $scope.resultSet = [];

    $scope.predefinedQueries = [
        'select * from piwik_option;'
    ];

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
