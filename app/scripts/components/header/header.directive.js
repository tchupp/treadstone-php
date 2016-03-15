'use strict';

angular.module('treadstoneApp')
    .directive('tsHeader', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/header/header.html',
            scope: {
                sideMenuExpanded: '=expanded'
            },
            controller: ['$scope', '$window', 'Auth', 'Principal', function ($scope, $window, Auth, Principal) {
                $scope.headerExpanded = false;
                $scope.isAuthenticated = Principal.isAuthenticated;

                $scope.logout = function () {
                    Auth.logout();
                };

                $scope.toggleSideMenu = function () {
                    $scope.sideMenuExpanded = !$scope.sideMenuExpanded;
                };

                $scope.toggleHeader = function () {
                    $scope.headerExpanded = !$scope.headerExpanded;
                };

                $scope.window = $window;
                $scope.$watch('window.document.title', function (title) {
                    $scope.title = title.substring(0, title.indexOf(' |'));
                });
            }]
        };
    })
    .directive('tsAffix', function ($window) {
        var checkPosition = function (offsetTop, element) {
            var reset = 'affix affix-top affix-bottom', affix;

            if ($window.pageYOffset <= offsetTop) {
                affix = 'top';
            } else {
                affix = false;
            }

            element.removeClass(reset).addClass('affix' + (affix ? '-' + affix : ''));
        };

        return {
            restrict: 'A',
            link: function (scope, element, attributes) {
                angular.element($window).bind('scroll', function () {
                    checkPosition(attributes.tsAffix, element);
                });
            }
        };
    });
