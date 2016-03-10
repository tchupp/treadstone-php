'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/landing', {
            templateUrl: 'scripts/app/landing/landing.html',
            controller: 'LandingController',
            data: {
                roles: [],
                pageTitle: 'Landing'
            }
        });
    });
