'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/activate', {
            templateUrl: 'scripts/app/account/activate/activate.html',
            controller: 'ActivationController',
            data: {
                roles: [],
                pageTitle: 'Activate'
            }
        });
    });
