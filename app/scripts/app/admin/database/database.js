'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/database', {
            templateUrl: 'scripts/app/admin/database/database.management.html',
            controller: 'DatabaseManagementController',
            data: {
                roles: ['ROLE_ADMIN'],
                pageTitle: 'Database Management'
            }
        });
    });
