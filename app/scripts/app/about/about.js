'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/about', {
            templateUrl: 'scripts/app/about/about.html',
            controller: 'AboutCtrl'
        });
    });
