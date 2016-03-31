'use strict';

angular.module('treadstoneApp')
    .directive('tsEndpoints', function () {
        return {
            replace: true,
            restrict: 'E',
            templateUrl: 'scripts/components/admin/docs/endpoints.html',
            scope: {
                resource: '=',
                endpoints: '=',
                parent: '@'
            },
            controller: ['$scope', function ($scope) {
                $scope.arrow = function () {
                    return $scope.endpoints.open ? 'glyphicon-chevron-down' : 'glyphicon-chevron-left';
                };
            }]
        };
    })
    .directive('tsEndpoint', function () {
        return {
            replace: true,
            restrict: 'E',
            templateUrl: 'scripts/components/admin/docs/endpoint.html',
            scope: {
                resource: '=',
                endpoint: '='
            },
            controller: ['$scope', function ($scope) {
                var methods = {'GET': 'btn-primary', 'POST': 'btn-success', 'DELETE': 'btn-danger'};

                $scope.id = ($scope.endpoint.method + $scope.endpoint.uri).replace(/\/|:/g, '');
                $scope.method = methods[$scope.endpoint.method];

                $scope.responseClass = function (response) {
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
