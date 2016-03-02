'use strict';

angular.module('treadstoneApp')
    .directive('tsContent', function () {
        return {
            restrict: 'E',
            replace: true,
            transclude: true,
            templateUrl: 'scripts/components/content/content.html',
            scope: {},
            controller: ['$scope', function ($scope) {
                $scope.expandedSideMenu = false;
            }]
        };
    });
