'use strict';

angular.module('treadstoneApp')
    .controller('LandingController', function ($rootScope, $scope, $location, Auth) {
        $scope.submitLogin = function () {
            Auth.login({
                login: $scope.login,
                password: $scope.password
            }).then(function () {
                $scope.authenticationError = false;
                if ($rootScope.previousRoute && $rootScope.previousRoute.originalPath === '/register') {
                    $location.path('/dashboard');
                } else {
                    $rootScope.back();
                }
            }).catch(function () {
                $scope.authenticationError = true;
            });
        };
    });
