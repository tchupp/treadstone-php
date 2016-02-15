'use strict';

angular.module('treadstoneApp')
    .controller('RegisterController', function ($scope, Auth) {
        $scope.registerAccount = {};
        $scope.success = false;
        $scope.errorUserExists = false;
        $scope.errorEmailExists = false;
        $scope.doNotMatch = false;

        $scope.register = function () {
            if ($scope.registerAccount.password !== $scope.confirmPassword) {
                $scope.doNotMatch = true;
            } else {
                $scope.errorUserExists = false;
                $scope.errorEmailExists = false;
                $scope.doNotMatch = false;

                Auth.createAccount($scope.registerAccount).then(function () {
                    $scope.success = true;
                }).catch(function (response) {
                    $scope.success = false;
                    if (response.status === 400 && response.data.description === 'Login already in use') {
                        $scope.errorUserExists = true;
                    } else if (response.status === 400 && response.data.description === 'E-mail address already in use') {
                        $scope.errorEmailExists = true;
                    }
                });
            }
        };
    });
