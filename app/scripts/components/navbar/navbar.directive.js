'use strict';

angular.module('treadstoneApp')
    .directive('tsNavbar', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/navbar/navbar.html',
            scope: {},
            controller: ['$scope', 'Auth', 'Principal', function ($scope, Auth, Principal) {
                Principal.identity().then(function () {
                    $scope.isAuthenticated = Principal.isAuthenticated;
                });

                $scope.logout = function () {
                    Auth.logout();
                };
            }]
        };
    });
