'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/accessdenied', {
            templateUrl: 'scripts/app/error/accessdenied.html',
            data: {
                roles: [],
                pageTitle: 'Access Denied'
            }
        });
    });
