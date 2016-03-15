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

                $scope.toggleSideMenu = function () {
                    $scope.expanded = !$scope.expanded;
                };
            }]
        };
    })
    .directive('tsActiveLink', function ($location) {
        return {
            restrict: 'A',
            link: function (scope, element, attributes) {
                var path = attributes.tsActiveLink;

                scope.location = $location;
                scope.$watch('location.path()', function(newPath) {
                    newPath = newPath.substring(1);
                    if (path === newPath) {
                        element.addClass('active');
                    } else {
                        element.removeClass('active');
                    }
                });
            }
        };
    });
