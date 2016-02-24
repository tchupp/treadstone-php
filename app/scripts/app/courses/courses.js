'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        var coursesRoute = {
            templateUrl: 'scripts/app/courses/courses.html',
            controller: 'CoursesController',
            data: {
                roles: ['ROLE_USER'],
                pageTitle: 'Courses'
            }
        };
        $routeProvider
            .when('/courses', coursesRoute);
            //.when('/courses/:semester', coursesRoute)
            //.when('/courses/:semester/:subject', coursesRoute)
            //.when('/courses/:semester/:subject/:section', coursesRoute);
    });
