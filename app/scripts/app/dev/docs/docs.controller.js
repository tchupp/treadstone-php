'use strict';

angular.module('treadstoneApp')
    .controller('DocsController', function ($scope, Docs) {
        $scope.docs = [];
        $scope.loading = true;

        function loadAll() {
            Docs.query(function (data) {
                $scope.loading = false;
                $scope.docs = data;
            }, function () {
                $scope.loading = false;
            });
        }
        loadAll();
    });

