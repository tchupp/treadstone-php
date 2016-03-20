'use strict';

angular.module('treadstoneApp')
    .directive('tsProfile', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/profile/profile.dropdown.html',
            controller: ['$scope', 'Account', function ($scope, Account) {
                Account.get().$promise.then(function (account) {
                    $scope.accountName = account.firstName + ' ' + account.lastName;
                    $scope.email = account.email;

                    angular.element('#profile-img').jdenticon(md5(account.login + account.email));
                });
            }]
        };
    });
