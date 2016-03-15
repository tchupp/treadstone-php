'use strict';

angular.module('treadstoneApp')
    .directive('tsContent', function () {
        return {
            restrict: 'E',
            replace: true,
            transclude: true,
            templateUrl: 'scripts/components/content/content.html',
            scope: {},
            controller: ['$scope', '$location', 'Principal', function ($scope, $location, Principal) {
                $scope.expanded = false;
                $scope.isAuthenticated = Principal.isAuthenticated;

                $scope.location = $location;
                $scope.$watch('location.path()', function (path) {
                    $scope.page = path.substring(1);
                });
            }]
        };
    });
