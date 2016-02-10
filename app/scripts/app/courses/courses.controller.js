'use strict';

angular.module('treadstoneApp')
    .controller('CoursesController', function ($scope, Courses) {
        $scope.courses = [];
        $scope.loading = true;

        function loadAll() {
            Courses.query(function (data) {
                $scope.loading = false;
                $scope.courses = data;
            }, function () {
                $scope.loading = false;
            });
        }
        loadAll();
    });
