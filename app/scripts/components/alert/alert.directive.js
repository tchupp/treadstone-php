'use strict';

angular.module('treadstoneApp')
    .directive('tsAlertToast', function ($rootScope, AlertService, Principal) {
        return {
            restrict: 'E',
            templateUrl: 'scripts/components/alert/alert.toast.html',
            controller: ['$scope', function ($scope) {
                $scope.alerts = AlertService.get();

                $rootScope.$on('treadstoneApp.httpError', function (event, httpResponse) {
                    if (Principal.hasAuthority('ROLE_DEV')) {
                        addErrorAlert(httpResponse.description);
                    }
                });

                $scope.$on('$destroy', function () {
                });

                var addErrorAlert = function (message) {
                    AlertService.error(message);
                };
            }]
        };
    });
