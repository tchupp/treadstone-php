'use strict';

angular.module('treadstoneApp')
    .controller('LandingController', function ($rootScope, $scope, Auth, Router) {
        $scope.submitLogin = function () {
            Auth.login({
                login: $scope.login,
                password: $scope.password
            }).then(function () {
                $scope.authenticationError = false;
                if ($rootScope.previousRouteName === '/register') {
                    Router.toDashboard();
                } else {
                    $rootScope.back();
                }
            }).catch(function () {
                $scope.authenticationError = true;
            });
        };
    });
