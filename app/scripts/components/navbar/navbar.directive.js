'use strict';

angular.module('treadstoneApp')
    .directive('tsNavbar', function() {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/navbar/navbar.html',
            scope: {},
            controller: ['$scope', '$location', function($scope, $location) {
                $scope.location = $location;
            }]
        };
    });
