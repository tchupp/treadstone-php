'use strict';

angular.module('treadstoneApp')
    .directive('tsProfile', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/profile/profile.dropdown.html',
            controller: ['$scope', 'Account', function ($scope, Account) {
                Account.get().$promise.then(function (account) {
                    $scope.accountName = account.data.firstName + ' ' + account.data.lastName;
                    $scope.email = account.data.email;
                    // angular.element('#profile-img').jdenticon(account.data.hash);
                });
            }]
        };
    });
