'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/about', {
            templateUrl: 'scripts/app/dev/about/about.html',
            controller: 'AboutController',
            data: {
                roles: ['ROLE_DEV'],
                pageTitle: 'About'
            }
        });
    });
