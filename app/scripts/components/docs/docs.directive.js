'use strict';

angular.module('treadstoneApp')
    .directive('tsEndpoints', function () {
        return {
            replace: true,
            restrict: 'E',
            templateUrl: 'scripts/components/docs/endpoints.html',
            scope: {
                resource: '=',
                endpoints: '='
            },
            controller: ['$scope', function ($scope) {
                $scope.arrow = {
                    'glyphicon-chevron-down': $scope.endpoints.open,
                    'glyphicon-chevron-left': !$scope.endpoints.open
                };
            }]
        };
    })
    .directive('tsEndpoint', function () {
        return {
            replace: true,
            restrict: 'E',
            templateUrl: 'scripts/components/docs/endpoint.html',
            scope: {
                endpoint: '='
            },
            controller: ['$scope', function ($scope) {
                var methods = {'GET': 'btn-primary', 'POST': 'btn-success'};

                $scope.id = ($scope.endpoint.method + $scope.endpoint.uri).replace(/\/|:/g, '');
                $scope.method = methods[$scope.endpoint.method];

                $scope.responseStatus = function (response) {
                    var statusType = Math.floor(response.status / 100);
                    if (statusType === 2) {
                        return 'list-group-item-success';
                    } else if (statusType === 4) {
                        return 'list-group-item-danger';
                    } else if (statusType === 5) {
                        return 'list-group-item-warning';
                    }
                };
            }]
        };
    });
