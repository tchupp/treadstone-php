'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/dashboard', {
            templateUrl: 'scripts/app/dashboard/dashboard.html',
            controller: 'DashboardController',
            data: {
                roles: ['ROLE_USER'],
                pageTitle: 'Dashboard'
            }
        });
    });
