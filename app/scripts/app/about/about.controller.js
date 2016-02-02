'use strict';

angular.module('treadstoneApp')
    .controller('AboutCtrl', function ($scope, Features) {

        function createUnknownError(status) {
            return {
                status: status,
                statusText: 'Internal Server Error',
                description: 'No details available'
            };
        }

        $scope.awesomeThings = [];
        $scope.loading = true;

        Features.query(function (data) {
            $scope.loading = false;
            $scope.awesomeThings = data;

            $scope.awesomeThings.forEach(function (thing) {
                thing.loading = true;

                Features.get({id: thing.id}, function (data) {
                    thing.loading = false;
                    thing.description = data.description;
                }, function (data, status) {
                    thing.loading = false;
                    thing.error = data && data.description ? data : createUnknownError(status);
                });
            });
        }, function (data, status) {
            $scope.loading = false;
            $scope.error = data && data.description ? data : createUnknownError(status);
        });
    });
