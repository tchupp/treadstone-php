'use strict';

angular.module('treadstoneApp')
    .controller('DocsController', function ($scope, Docs) {

        function createUnknownError(status) {
            return {
                status: status,
                statusText: 'Internal Server Error',
                description: 'No details available'
            };
        }

        $scope.endpointsClick = function (endpoints) {
            endpoints.open = !endpoints.open;
        };

        $scope.endpointsClass = function (endpoints) {
            if (endpoints.open) {
                return 'glyphicon-chevron-down';
            } else {
                return 'glyphicon-chevron-left';
            }
        };

        $scope.endpointId = function (endpoint) {
            return (endpoint.method + endpoint.uri).replace(/\/|:/g, '');
        };

        $scope.endpointMethodClass = function (endpoint) {
            if (endpoint.method === 'GET') {
                return 'btn-primary';
            } else if (endpoint.method === 'POST') {
                return 'btn-success';
            }
        };

        $scope.responseClass = function (response) {
            if (response.status === 200 || response.status === 201) {
                return 'list-group-item-success';
            } else if (response.status === 400 || response.status === 401 || response.status === 404) {
                return 'list-group-item-danger';
            } else if (response.status === 500 || response.status === 501) {
                return 'list-group-item-warning';
            }
        };

        $scope.docs = [];
        $scope.loading = true;

        Docs.query(function (data) {
            $scope.loading = false;
            $scope.docs = data;
        }, function (data, status) {
            $scope.loading = false;
            $scope.error = data && data.description ? data : createUnknownError(status);
        });
    });

