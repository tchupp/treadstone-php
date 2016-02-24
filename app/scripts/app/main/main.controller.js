'use strict';

angular.module('treadstoneApp')
    .controller('MainCtrl', function ($rootScope, $scope, $location, Auth, Principal) {
        $scope.isAuthenticated = Principal.isAuthenticated;

        $scope.submitLogin = function () {
            Auth.login({
                login: $scope.login,
                password: $scope.password
            }).then(function () {
                $scope.authenticationError = false;
                if ($rootScope.previousRoute.originalPath === '/register') {
                    $location.path('/');
                } else {
                    $rootScope.back();
                }
            }).catch(function () {
                $scope.authenticationError = true;
            });
        };
    });
