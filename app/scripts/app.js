'use strict';

/**
 * @ngdoc overview
 * @name treadstoneApp
 * @description
 * # treadstoneApp
 *
 * Main module of the application.
 */
angular.module('treadstoneApp', [
        'ngAnimate',
        'ngCookies',
        'ngResource',
        'ngRoute',
        'ngSanitize',
        'ngTouch'
    ])
    .config(function ($routeProvider) {
        $routeProvider.otherwise({
            redirectTo: '/'
        });
    });
