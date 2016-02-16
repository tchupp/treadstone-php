'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/register', {
            templateUrl: 'scripts/app/account/register/register.html',
            controller: 'RegisterController',
            data: {
                roles: [],
                pageTitle: 'Register'
            }
        });
    });
