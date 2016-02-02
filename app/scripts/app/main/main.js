'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/', {
            templateUrl: 'scripts/app/main/main.html',
            controller: 'MainCtrl'
        });
    });
