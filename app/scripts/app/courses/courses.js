'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/courses', {
            templateUrl: 'scripts/app/courses/courses.html',
            controller: 'CoursesController',
            data: {
                roles: ['ROLE_USER'],
                pageTitle: 'Courses'
            }
        });
    });
