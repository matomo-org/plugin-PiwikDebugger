/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    // TODO: would be much better w/ websockets.... maybe can make it better by polling ALL executions w/ one request
    angular.module('piwikApp').controller('PiwikConsoleTabController', PiwikConsoleTabController);

    PiwikConsoleTabController.$inject = ['$scope', '$timeout', 'piwikApi'];

    function PiwikConsoleTabController($scope, $timeout, piwikApi) {
        $scope.commands = [];
        $scope.commandToExecute = "";

        var Command = function (commandText) {
            this.commandText = commandText;
            this.commandOutput = "";
            this.returnCode = null;
            this.isLoading = false;
            this.isExpanded = false;
            this.commandId = this.generateCommandId();
        };

        Command.prototype.generateCommandId = function () {
            return (new Date()).getTime();
        };

        Command.prototype.startExecution = function () {
            this.isLoading = true;
            this.startTime = new Date();

            var self = this,
                request = piwikApi.fetch({
                    method: 'PiwikDebugger.startCommandExecution',
                    commandText: this.commandText,
                    commandId: this.commandId
                }).then(function (response) {
                    if (!response || response.result == 'success') {
                        self.startPolling();
                    } else {
                        self.commandError = response;
                        self.isLoading = false;
                    }
                }).catch(function (errorMessage) {
                    if (errorMessage) {
                        self.commandError = errorMessage;
                        self.isLoading = false;
                    } else {
                        self.startPolling();
                    }
                });

            // abort client side request so we won't wait for a long-running process to finish, then...
            $timeout(function () {
                request.abort();
            }, 4000);
        };

        Command.prototype.startPolling = function () {
            this.pollStatus();
        };

        Command.prototype.pollStatus = function () {
            var self = this;
            piwikApi.fetch({
                method: 'PiwikDebugger.pollCommandStatus',
                commandId: this.commandId,
                outputStart: this.commandOutput.length
            }).then(function (response) {
                if (response.output === undefined) {
                    console.log("Invalid polling response: " + JSON.stringify(response));
                    return;
                }

                self.commandOutput += response.output;

                if (response.returnCode !== null
                    && response.returnCode !== undefined
                ) {
                    self.finishExecution(response.returnCode);
                } else {
                    $timeout(function () {
                        self.pollStatus();
                    }, 2000);
                }
            });
        };

        Command.prototype.finishExecution = function (returnCode) {
            this.returnCode = returnCode;
            this.isLoading = false;
            this.finishTime = new Date();
            this.elapsedTime = this.finishTime - this.startTime;
        };

        $scope.executeCommand = function (commandText) {
            var command = new Command(commandText);
            command.startExecution();
            $scope.commands.push(command);

            $scope.commandToExecute = "";
        };
    }
})();