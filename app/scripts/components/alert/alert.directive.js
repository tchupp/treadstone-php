'use strict';

angular.module('treadstoneApp')
    .directive('tsAlertToast', function ($rootScope, AlertService) {
        return {
            restrict: 'E',
            templateUrl: 'scripts/components/alert/alert.toast.html',
            controller: ['$scope', function ($scope) {
                $scope.alerts = AlertService.get();

                $rootScope.$on('treadstoneApp.httpError', function (event, httpResponse) {
                    addErrorAlert(httpResponse.data.description);
                });

                $scope.$on('$destroy', function () {
                });

                var addErrorAlert = function (message) {
                    AlertService.error(message);
                };
            }]
        };
    });
