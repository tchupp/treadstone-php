'use strict';

angular.module('treadstoneApp')
    .directive('tsSidemenu', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/sidemenu/sidemenu.html',
            scope: {
                expanded: '='
            },
            controller: ['$scope', 'Principal', function ($scope, Principal) {
                $scope.isAuthenticated = Principal.isAuthenticated;
            }]
        };
    });
