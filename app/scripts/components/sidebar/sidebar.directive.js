'use strict';

angular.module('treadstoneApp')
    .directive('tsSidebar', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/sidebar/sidebar.html',
            scope: {},
            controller: ['$scope', 'Principal', function ($scope, Principal) {
                $scope.isAuthenticated = Principal.isAuthenticated;
            }]
        };
    });
