'use strict';

angular.module('treadstoneApp')
    .controller('ActivationController', function ($scope, $routeParams, Auth) {
        Auth.activateAccount(
            {key: $routeParams.key}
        ).then(function () {
            $scope.error = false;
            $scope.success = true;
        }).catch(function () {
            $scope.success = false;
            $scope.error = true;
        });
    });
